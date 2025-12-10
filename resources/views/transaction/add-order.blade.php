@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Pesanan Baru</h4>
                    <p class="mb-0">Transaksi #{{ $transaction->id_transaksi }} - {{ $transaction->nama }}</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('transaction.store-order', $transaction->id_transaksi) }}" id="orderForm">
                        @csrf
                        
                        <div id="order-items">
                            <!-- Order items will be added here dynamically -->
                            <div class="order-item card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group mb-3">
                                                <label>Makanan/Minuman</label>
                                                <select name="items[0][fnb_id]" class="form-control fnb-select" required>
                                                    <option value="">Pilih Makanan/Minuman</option>
                                                    @foreach($fnbs as $fnb)
                                                        <option value="{{ $fnb->id }}" data-price="{{ $fnb->harga_jual }}">
                                                            {{ $fnb->nama }} - Rp {{ number_format($fnb->harga_jual, 0, ',', '.') }}
                                                            @if($fnb->stok == -1)
                                                                <span class="badge badge-success">Unlimited</span>
                                                            @else
                                                                (Stok: {{ $fnb->stok }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label>Jumlah</label>
                                                <input type="number" name="items[0][qty]" min="1" class="form-control qty-input" value="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label>Total Harga</label>
                                                <p class="form-control-static item-total">Rp 0</p>
                                                <input type="hidden" name="items[0][price]" class="item-price" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-success" id="add-item">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Semua Pesanan
                                </button>
                                <a href="{{ route('transaction.show', $transaction->id_transaksi) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        
                        <input type="hidden" id="existing-total" value="{{ $existingTotal }}">
                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <h5>Total Baru: <span id="grand-total">Rp {{ number_format($existingTotal, 0, ',', '.') }}</span></h5>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemCount = 1; // Start from 1 since we have one item by default
        const orderItems = document.getElementById('order-items');
        const addItemBtn = document.getElementById('add-item');
        const orderForm = document.getElementById('orderForm');

        // Add new item row
        addItemBtn.addEventListener('click', function() {
            const newItem = document.querySelector('.order-item').cloneNode(true);
            const newIndex = itemCount++;
            
            // Update all names and IDs
            newItem.innerHTML = newItem.innerHTML.replace(/items\[\d+\]/g, `items[${newIndex}]`);
            
            // Reset values
            const select = newItem.querySelector('select');
            const qtyInput = newItem.querySelector('.qty-input');
            const itemTotal = newItem.querySelector('.item-total');
            const itemPrice = newItem.querySelector('.item-price');
            
            select.selectedIndex = 0;
            qtyInput.value = 1;
            itemTotal.textContent = 'Rp 0';
            itemPrice.value = '0';
            
            // Show remove button for all items except first one
            newItem.querySelector('.remove-item').style.display = 'block';
            
            // Add to DOM
            orderItems.appendChild(newItem);
            
            // Initialize event listeners for the new item
            initializeItemEvents(newItem);
        });

        // Initialize event listeners for the first item
        initializeItemEvents(document.querySelector('.order-item'));

        // Function to initialize event listeners for an item
        function initializeItemEvents(item) {
            const select = item.querySelector('select');
            const qtyInput = item.querySelector('.qty-input');
            const removeBtn = item.querySelector('.remove-item');
            
            // Update price when FNB is selected or quantity changes
            select.addEventListener('change', updateItemTotal);
            qtyInput.addEventListener('input', updateItemTotal);
            
            // Remove item
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    item.remove();
                    updateGrandTotal();
                });
            }
            
            // Initial update
            updateItemTotal.call(select);
        }

        // Update item total price
        function updateItemTotal() {
            const item = this.closest('.order-item');
            const select = item.querySelector('select');
            const qtyInput = item.querySelector('.qty-input');
            const itemTotal = item.querySelector('.item-total');
            const itemPrice = item.querySelector('.item-price');
            
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption ? parseFloat(selectedOption.getAttribute('data-price') || 0) : 0;
            const qty = parseInt(qtyInput.value) || 0;
            const total = price * qty;
            
            itemTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            itemPrice.value = total;
            
            updateGrandTotal();
        }

        // Update grand total
        function updateGrandTotal() {
            const itemTotals = document.querySelectorAll('.item-price');
            const existingTotal = parseFloat(document.getElementById('existing-total').value) || 0;
            let newItemsTotal = 0;

            itemTotals.forEach(input => {
                newItemsTotal += parseFloat(input.value) || 0;
            });

            const grandTotal = existingTotal + newItemsTotal;

            document.getElementById('grand-total').textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
        }

        // Form submission
        orderForm.addEventListener('submit', function(e) {
            // No need to prevent default, let it submit normally
            // The server will handle the array of items
        });
    });
</script>

<style>
    .order-item {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    .order-item .card-body {
        padding: 1rem;
    }
    .remove-item {
        margin-bottom: 1rem;
    }
    .form-group {
        margin-bottom: 0;
    }
    .form-control-static {
        padding-top: calc(0.375rem + 1px);
        padding-bottom: calc(0.375rem + 1px);
        margin-bottom: 0;
        line-height: 1.5;
    }
</style>
@endsection