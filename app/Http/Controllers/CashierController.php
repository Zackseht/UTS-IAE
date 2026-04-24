<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('cashier.index', compact('menus'));
    }
}
