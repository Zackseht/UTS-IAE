<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = filter_var(config('midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = filter_var(config('midtrans.is_sanitized'), FILTER_VALIDATE_BOOLEAN);
        Config::$is3ds = filter_var(config('midtrans.is_3ds'), FILTER_VALIDATE_BOOLEAN);
    }

    public function checkout(Request $request)
    {
        $cart = $request->input('cart'); // array of ['id', 'quantity', 'price', 'name']
        if (!$cart || count($cart) == 0) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $totalPrice = 0;
        $items = [];
        
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
            $items[] = [
                'id' => $item['id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
            ];
        }

        $orderNumber = 'ORD-' . time();
        
        $order = Order::create([
            'order_number' => $orderNumber,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderNumber,
                'gross_amount' => $totalPrice,
            ),
            'item_details' => $items,
        );

        try {
            $snapToken = Snap::getSnapToken($params);
            
            $order->payment_url = $snapToken;
            $order->save();

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()], 500);
        }
    }

    public function success(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::where('order_number', $orderId)->first();
        if ($order) {
            $order->status = 'paid';
            $order->save();
        }
        return response()->json(['message' => 'Payment Success recorded']);
    }

    public function checkoutCash(Request $request)
    {
        $cart = $request->input('cart');
        $cashAmount = $request->input('cash_amount');

        if (!$cart || count($cart) == 0) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        if ($cashAmount < $totalPrice) {
            return response()->json(['error' => 'Uang tidak cukup'], 400);
        }

        $orderNumber = 'ORD-' . time();
        
        $order = Order::create([
            'order_number' => $orderNumber,
            'total_price' => $totalPrice,
            'status' => 'paid',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        session()->flash('cash_amount', $cashAmount);
        session()->flash('change_amount', $cashAmount - $totalPrice);

        return response()->json(['redirect_url' => route('receipt', $orderNumber)]);
    }

    public function receipt($orderNumber)
    {
        $order = Order::with('items.menu')->where('order_number', $orderNumber)->firstOrFail();
        return view('cashier.receipt', compact('order'));
    }
}
