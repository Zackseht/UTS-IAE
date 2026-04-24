@extends('layouts.app')

@section('content')
<div class="grid grid-pos">
    <!-- Menu Grid -->
    <div>
        <!-- Search Bar -->
        <div style="margin-bottom: 1.5rem;">
            <input type="text" id="menu-search" class="form-control" placeholder="Cari menu makanan/minuman..." onkeyup="filterMenu()" style="border-radius: 20px; padding: 0.75rem 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        </div>

        <div class="grid grid-cols-3">
            @foreach($menus as $menu)
            <div class="glass-panel menu-card" data-name="{{ strtolower($menu->name) }}" onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})" style="cursor: pointer;">
                @if($menu->image)
                    <img src="/storage/{{ $menu->image }}" class="menu-image" alt="{{ $menu->name }}">
                @else
                    <div class="menu-image" style="background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af;">No Image</div>
                @endif
                <div style="font-weight: 600; font-size: 1.1rem; margin-top: 0.5rem;">{{ $menu->name }}</div>
                <div style="color: var(--primary-color); font-weight: 800;">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Cart Panel -->
    <div class="glass-panel floating-cart" style="padding: 1.25rem;">
        <h3 style="margin-top: 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; font-size: 1.1rem;">Current Order</h3>
        
        <div id="cart-items" style="min-height: 100px; max-height: 250px; overflow-y: auto; margin-bottom: 1rem; font-size: 0.9rem;">
            <div id="empty-cart" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">Cart is empty</div>
        </div>

        <div style="border-top: 2px solid #e5e7eb; padding-top: 0.75rem; margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 1.1rem;">
                <span>Total</span>
                <span id="cart-total">Rp 0</span>
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button id="pay-cash-btn" class="btn" style="flex: 1; font-size: 0.9rem; padding: 0.75rem; background-color: var(--success-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;" disabled onclick="promptCash()">Cash</button>
            <button id="pay-button" class="btn btn-primary" style="flex: 1; font-size: 0.9rem; padding: 0.75rem;" disabled onclick="checkout()">Midtrans</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];

    function filterMenu() {
        const query = document.getElementById('menu-search').value.toLowerCase();
        const cards = document.querySelectorAll('.menu-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function addToCart(id, name, price) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1 });
        }
        renderCart();
    }

    function updateQuantity(id, change) {
        const index = cart.findIndex(item => item.id === id);
        if (index > -1) {
            cart[index].quantity += change;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('empty-cart');
        const totalEl = document.getElementById('cart-total');
        const payBtn = document.getElementById('pay-button');
        const payCashBtn = document.getElementById('pay-cash-btn');

        if (cart.length === 0) {
            container.innerHTML = '<div id="empty-cart" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">Cart is empty</div>';
            totalEl.innerText = 'Rp 0';
            payBtn.disabled = true;
            payCashBtn.disabled = true;
            return;
        }

        payBtn.disabled = false;
        payCashBtn.disabled = false;
        container.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            total += item.price * item.quantity;
            const el = document.createElement('div');
            el.className = 'cart-item';
            el.innerHTML = `
                <div style="flex: 1;">
                    <div style="font-weight: 600;">${item.name}</div>
                    <div style="color: var(--primary-color); font-size: 0.875rem;">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</div>
                </div>
                <div class="cart-controls" style="display: flex; align-items: center; gap: 0.5rem;">
                    <button onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span style="font-weight: 600; width: 20px; text-align: center;">${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, 1)">+</button>
                </div>
            `;
            container.appendChild(el);
        });

        totalEl.innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
    }

    function promptCash() {
        let total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let cashStr = prompt("Total Belanja: Rp " + new Intl.NumberFormat('id-ID').format(total) + "\n\nMasukkan jumlah uang yang diterima dari pelanggan:");
        if (!cashStr) return;
        
        let cashAmount = parseInt(cashStr.replace(/\D/g, ''));
        if (isNaN(cashAmount) || cashAmount < total) {
            alert("Uang tidak valid atau kurang dari total belanja!");
            return;
        }

        let change = cashAmount - total;
        if (confirm("Uang Diterima: Rp " + new Intl.NumberFormat('id-ID').format(cashAmount) + "\nKembalian: Rp " + new Intl.NumberFormat('id-ID').format(change) + "\n\nProses pembayaran ini?")) {
            processCashPayment(cashAmount);
        }
    }

    function processCashPayment(cashAmount) {
        const payCashBtn = document.getElementById('pay-cash-btn');
        payCashBtn.disabled = true;
        payCashBtn.innerText = 'Processing...';

        fetch('/checkout/cash', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cart, cash_amount: cashAmount })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.error || data.message || 'Server error');
            }
            return data;
        })
        .then(data => {
            if (data.redirect_url) {
                cart = [];
                window.location.href = data.redirect_url;
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
                payCashBtn.disabled = false;
                payCashBtn.innerText = 'Pay with Cash';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error Server: ' + err.message);
            payCashBtn.disabled = false;
            payCashBtn.innerText = 'Pay with Cash';
        });
    }

    function checkout() {
        const payBtn = document.getElementById('pay-button');
        payBtn.disabled = true;
        payBtn.innerText = 'Processing...';

        fetch('/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cart: cart })
        })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                throw new Error(text.substring(0, 100)); // throw first 100 chars
            }
            return res.json();
        })
        .then(data => {
            if(data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        alert("payment success!");
                        cart = [];
                        renderCart();
                    },
                    onPending: function(result){
                        alert("wating your payment!");
                    },
                    onError: function(result){
                        alert("payment failed!");
                    },
                    onClose: function(){
                        payBtn.disabled = false;
                        payBtn.innerText = 'Process Payment';
                    }
                });
            } else {
                alert('Error processing checkout: ' + (data.error || 'Unknown error'));
                payBtn.disabled = false;
                payBtn.innerText = 'Process Payment';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error Server: ' + err.message);
            payBtn.disabled = false;
            payBtn.innerText = 'Process Payment';
        });
    }
</script>
@endpush
@endsection
