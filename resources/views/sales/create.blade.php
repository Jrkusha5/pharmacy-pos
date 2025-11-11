@extends('layouts.app')

@section('title', 'Create New Sale')

@push('styles')
<style>
    .sales-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }
    
    .sales-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .product-search-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }
    
    .search-input-wrapper {
        position: relative;
    }
    
    .search-input-wrapper i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 1;
    }
    
    .search-input-wrapper input {
        padding-left: 45px;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        font-size: 1rem;
    }
    
    .search-input-wrapper input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
        margin-top: 20px;
        padding: 10px;
    }
    
    .product-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .product-card:hover {
        border-color: #667eea;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }
    
    .product-card.selected {
        border-color: #28a745;
        background: #f0fff4;
    }
    
    .product-card .checkbox-wrapper {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .product-card .checkbox-wrapper input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    
    .product-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 0.95rem;
        padding-right: 30px;
    }
    
    .product-details {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .product-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #28a745;
        margin-top: 10px;
    }
    
    .product-stock {
        font-size: 0.8rem;
        color: #dc3545;
        margin-top: 5px;
    }
    
    .product-stock.in-stock {
        color: #28a745;
    }
    
    .cart-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }
    
    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .cart-items {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .cart-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .cart-item:hover {
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .cart-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .cart-item-name {
        font-weight: 600;
        color: #333;
        flex: 1;
    }
    
    .cart-item-remove {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .cart-item-remove:hover {
        background: #c82333;
        transform: scale(1.1);
    }
    
    .cart-item-controls {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 10px;
        margin-top: 10px;
    }
    
    .cart-item-controls label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
        display: block;
    }
    
    .quantity-input, .price-input {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 8px;
        width: 100%;
    }
    
    .line-total {
        font-weight: 700;
        color: #667eea;
        font-size: 1.1rem;
        padding-top: 25px;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: sticky;
        top: 20px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .summary-row.total {
        border-bottom: none;
        font-size: 1.3rem;
        font-weight: 700;
        margin-top: 10px;
    }
    
    .btn-checkout {
        background: white;
        color: #667eea;
        border: none;
        border-radius: 10px;
        padding: 15px;
        font-weight: 600;
        font-size: 1.1rem;
        width: 100%;
        margin-top: 20px;
        transition: all 0.3s ease;
    }
    
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        background: #f8f9fa;
    }
    
    .btn-checkout:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    
    .loading-spinner {
        text-align: center;
        padding: 20px;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3em;
    }
    
    .selected-count {
        background: #28a745;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .form-label {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }
    
    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    
    .form-control option {
        color: #333;
    }
</style>
@endpush

@section('content')
<div class="sales-container">
    <div class="sales-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2"><i class="fas fa-cash-register me-2"></i>New Sale Transaction</h2>
                <p class="mb-0 opacity-75">Select products and complete the sale</p>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-2"></i>Back to Sales
            </a>
        </div>
    </div>

    <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
        @csrf
        
        <div class="row">
            <!-- Left Column: Product Search & Selection -->
            <div class="col-lg-7">
                <div class="product-search-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Search Products</h5>
                        <span class="selected-count" id="selectedCount">0 selected</span>
                    </div>
                    
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               class="form-control" 
                               id="productSearch" 
                               placeholder="Search by name, SKU, or barcode..."
                               autocomplete="off">
                    </div>
                    
                    <div id="productsContainer" class="products-grid">
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <p>Start typing to search for products...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart & Summary -->
            <div class="col-lg-5">
                <div class="cart-section">
                    <div class="cart-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h5>
                        <span class="badge bg-primary" id="cartItemCount">0 items</span>
                    </div>
                    
                    <div class="cart-items" id="cartItems">
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <p>No items in cart</p>
                            <small>Select products from the search results</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary & Checkout -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="summary-card">
                    <h5 class="mb-4"><i class="fas fa-receipt me-2"></i>Sale Summary</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sale Date</label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   name="sold_at" 
                                   value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="draft">Draft</option>
                                <option value="completed" selected>Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="note" rows="2" placeholder="Optional notes...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="summarySubtotal">0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span id="summaryTax">0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Discount:</span>
                        <span id="summaryDiscount">0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="summaryTotal">0.00</span>
                    </div>
                    
                    <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                    <input type="hidden" name="total" id="inputTotal" value="0">
                    
                    <button type="submit" class="btn-checkout" id="checkoutBtn" disabled>
                        <i class="fas fa-check-circle me-2"></i>Complete Sale
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Hidden inputs for cart items -->
        <div id="hiddenInputs"></div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedProducts = new Map();
    let searchTimeout;
    const searchInput = document.getElementById('productSearch');
    const productsContainer = document.getElementById('productsContainer');
    const cartItems = document.getElementById('cartItems');
    const selectedCount = document.getElementById('selectedCount');
    const cartItemCount = document.getElementById('cartItemCount');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    // Search products
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            productsContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <p>Start typing to search for products...</p>
                </div>
            `;
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchProducts(query);
        }, 300);
    });
    
    function searchProducts(query) {
        productsContainer.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        fetch(`{{ route('sales.search-items') }}?search=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    productsContainer.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>No products found</p>
                        </div>
                    `;
                    return;
                }
                
                productsContainer.innerHTML = data.map(item => {
                    const isSelected = selectedProducts.has(item.id);
                    return `
                        <div class="product-card ${isSelected ? 'selected' : ''}" 
                             data-product-id="${item.id}">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" 
                                       ${isSelected ? 'checked' : ''}
                                       onchange="toggleProduct(${item.id}, ${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            </div>
                            <div class="product-name">${escapeHtml(item.name)}</div>
                            <div class="product-details">SKU: ${escapeHtml(item.sku || 'N/A')}</div>
                            <div class="product-details">Category: ${escapeHtml(item.category)}</div>
                            <div class="product-price">Birr ${parseFloat(item.price).toFixed(2)}</div>
                            <div class="product-stock ${item.stock > 0 ? 'in-stock' : ''}">
                                Stock: ${item.stock} ${escapeHtml(item.unit)}
                            </div>
                        </div>
                    `;
                }).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                productsContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error loading products</p>
                    </div>
                `;
            });
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    
    // Toggle product selection
    window.toggleProduct = function(productId, productData) {
        if (selectedProducts.has(productId)) {
            selectedProducts.delete(productId);
        } else {
            selectedProducts.set(productId, {
                ...productData,
                quantity: 1,
                unit_price: parseFloat(productData.price)
            });
        }
        updateUI();
    };
    
    // Update cart item
    function updateCartItem(productId, field, value) {
        if (selectedProducts.has(productId)) {
            const product = selectedProducts.get(productId);
            product[field] = field === 'quantity' || field === 'unit_price' 
                ? parseFloat(value) || 0 
                : value;
            selectedProducts.set(productId, product);
            updateUI();
        }
    }
    
    // Remove from cart
    function removeFromCart(productId) {
        selectedProducts.delete(productId);
        updateUI();
        
        // Uncheck checkbox if product card is visible
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (productCard) {
            productCard.classList.remove('selected');
            const checkbox = productCard.querySelector('input[type="checkbox"]');
            if (checkbox) checkbox.checked = false;
        }
    }
    
    // Update UI
    function updateUI() {
        // Update selected count
        selectedCount.textContent = `${selectedProducts.size} selected`;
        
        // Update cart
        if (selectedProducts.size === 0) {
            cartItems.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <p>No items in cart</p>
                    <small>Select products from the search results</small>
                </div>
            `;
            cartItemCount.textContent = '0 items';
            checkoutBtn.disabled = true;
        } else {
            cartItems.innerHTML = Array.from(selectedProducts.entries()).map(([id, product]) => {
                const lineTotal = (product.quantity * product.unit_price).toFixed(2);
                return `
                    <div class="cart-item">
                        <div class="cart-item-header">
                            <div class="cart-item-name">${escapeHtml(product.name)}</div>
                            <button type="button" class="cart-item-remove" onclick="removeFromCart(${id})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="cart-item-controls">
                            <div>
                                <label>Quantity</label>
                                <input type="number" 
                                       class="form-control quantity-input" 
                                       value="${product.quantity}" 
                                       min="0.01" 
                                       step="0.01"
                                       onchange="updateCartItem(${id}, 'quantity', this.value)">
                            </div>
                            <div>
                                <label>Unit Price</label>
                                <input type="number" 
                                       class="form-control price-input" 
                                       value="${product.unit_price.toFixed(2)}" 
                                       min="0" 
                                       step="0.01"
                                       onchange="updateCartItem(${id}, 'unit_price', this.value)">
                            </div>
                            <div>
                                <label>Total</label>
                                <div class="line-total">Birr ${lineTotal}</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            cartItemCount.textContent = `${selectedProducts.size} item${selectedProducts.size > 1 ? 's' : ''}`;
            checkoutBtn.disabled = false;
        }
        
        // Update summary
        let subtotal = 0;
        selectedProducts.forEach(product => {
            subtotal += product.quantity * product.unit_price;
        });
        
        document.getElementById('summarySubtotal').textContent = subtotal.toFixed(2);
        document.getElementById('summaryTotal').textContent = subtotal.toFixed(2);
        document.getElementById('inputSubtotal').value = subtotal.toFixed(2);
        document.getElementById('inputTotal').value = subtotal.toFixed(2);
        
        // Update hidden inputs for form submission
        const hiddenInputs = document.getElementById('hiddenInputs');
        hiddenInputs.innerHTML = Array.from(selectedProducts.entries()).map(([id, product], index) => `
            <input type="hidden" name="items[${index}][item_id]" value="${id}">
            <input type="hidden" name="items[${index}][quantity]" value="${product.quantity}">
            <input type="hidden" name="items[${index}][unit_price]" value="${product.unit_price}">
        `).join('');
    }
    
    // Make removeFromCart available globally
    window.removeFromCart = removeFromCart;
    window.updateCartItem = updateCartItem;
    
    // Form validation
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        if (selectedProducts.size === 0) {
            e.preventDefault();
            alert('Please add at least one item to the sale.');
            return false;
        }
    });
});
</script>
@endpush
