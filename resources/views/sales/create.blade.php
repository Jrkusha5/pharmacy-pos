@extends('layouts.app')

@section('title', 'Create New Sale')

@push('styles')
<style>
    .item-row {
        transition: all 0.3s ease;
        border-left: 4px solid #0d6efd;
    }
    .item-row:hover {
        background-color: #f8f9fa;
        border-left-color: #198754;
    }
    .table-fixed thead {
        position: sticky;
        top: 0;
        background-color: #fff;
        z-index: 10;
    }
    .summary-card {
        position: sticky;
        top: 80px;
    }
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
    }
    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-cash-register me-2"></i>Create New Sale</h1>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Sales
                </a>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Sale Form --}}
            <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                @csrf
                <div class="row">
                    {{-- Sale Info --}}
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Sale Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sold_at" class="form-label">Sale Date</label>
                                        <input type="datetime-local" class="form-control" id="sold_at" name="sold_at"
                                            value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="completed" {{ old('status') == 'completed' || !old('status') ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="note" class="form-label">Notes</label>
                                        <textarea class="form-control" id="note" name="note" rows="2">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sale Items --}}
                        <div class="card">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Sale Items</h5>
                                <button type="button" class="btn btn-light btn-sm" id="addItemBtn">
                                    <i class="fas fa-plus me-1"></i> Add Item
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="30%">Item</th>
                                                <th width="10%">Quantity</th>
                                                <th width="15%">Unit Price</th>
                                                <th width="15%">Total</th>
                                                <th width="20%">Batch/Expiry</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsContainer">
                                            {{-- Old Items Reload --}}
                                            @if(old('items'))
                                                @foreach(old('items') as $index => $item)
                                                    <tr class="item-row" data-index="{{ $index }}">
                                                        <td>
                                                            <select class="form-select item-select" name="items[{{ $index }}][item_id]" required>
                                                                <option value="">Select Item</option>
                                                                @foreach($items as $itemOption)
                                                                    <option value="{{ $itemOption->id }}"
                                                                        {{ $item['item_id'] == $itemOption->id ? 'selected' : '' }}
                                                                        data-price="{{ $itemOption->sell_price }}">
                                                                        {{ $itemOption->name }} ({{ $itemOption->code }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0.01" class="form-control quantity"
                                                                name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0" class="form-control unit-price"
                                                                name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control line-total" value="0.00" readonly>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control batch-no"
                                                                    name="items[{{ $index }}][batch_no]" value="{{ $item['batch_no'] ?? '' }}"
                                                                    placeholder="Batch No">
                                                                <input type="date" class="form-control expires-at"
                                                                    name="items[{{ $index }}][expires_at]" value="{{ $item['expires_at'] ?? '' }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-item">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center p-3 {{ old('items') && count(old('items')) > 0 ? 'd-none' : '' }}" id="noItemsMessage">
                                    <p class="text-muted mb-0">No items added yet. Click "Add Item" to get started.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sale Summary --}}
                    <div class="col-md-4">
                        <div class="card summary-card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Sale Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Subtotal:</span>
                                    <span id="subtotal">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span id="tax">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Discount:</span>
                                    <span id="discount">0.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold" id="total">0.00</span>
                                </div>
                                <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                                <input type="hidden" name="total" id="inputTotal" value="0">

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i> Save Sale
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary">
                                        <i class="fas fa-file-alt me-2"></i> Save as Draft
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = {{ old('items') ? count(old('items')) : 0 }};
        const itemsContainer = document.getElementById('itemsContainer');
        const noItemsMessage = document.getElementById('noItemsMessage');
        const addItemBtn = document.getElementById('addItemBtn');

        // Add new item row
        addItemBtn.addEventListener('click', function() {
            addItemRow();
            updateSummary();
            toggleNoItemsMessage();
        });

        // Remove item row
        itemsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                const row = e.target.closest('tr');
                row.remove();
                updateSummary();
                toggleNoItemsMessage();
                reindexItems();
            }
        });

        // Calculate line total when quantity or price changes
        itemsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
                const row = e.target.closest('tr');
                calculateLineTotal(row);
                updateSummary();
            }
        });

        // Update price when item selection changes
        itemsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.getAttribute('data-price') || 0;
                const row = e.target.closest('tr');
                const unitPriceInput = row.querySelector('.unit-price');

                if (price > 0 && (!unitPriceInput.value || unitPriceInput.value == 0)) {
                    unitPriceInput.value = price;
                    calculateLineTotal(row);
                    updateSummary();
                }
            }
        });

        // Initialize calculations for existing items
        document.querySelectorAll('.item-row').forEach(row => {
            calculateLineTotal(row);
        });

        updateSummary();
        toggleNoItemsMessage();

        function addItemRow() {
            const template = `
                <tr class="item-row" data-index="${itemIndex}">
                    <td>
                        <select class="form-select item-select" name="items[${itemIndex}][item_id]" required>
                            <option value="">Select Item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->sell_price }}">
                                    {{ $item->name }} ({{ $item->code }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" class="form-control quantity"
                            name="items[${itemIndex}][quantity]" value="1" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" class="form-control unit-price"
                            name="items[${itemIndex}][unit_price]" value="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control line-total" value="0.00" readonly>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control batch-no"
                                name="items[${itemIndex}][batch_no]" placeholder="Batch No">
                            <input type="date" class="form-control expires-at"
                                name="items[${itemIndex}][expires_at]">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            itemsContainer.insertAdjacentHTML('beforeend', template);
            itemIndex++;
        }

        function calculateLineTotal(row) {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const lineTotal = quantity * unitPrice;

            row.querySelector('.line-total').value = lineTotal.toFixed(2);
        }

        function updateSummary() {
            let subtotal = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const lineTotal = parseFloat(row.querySelector('.line-total').value) || 0;
                subtotal += lineTotal;
            });

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('total').textContent = subtotal.toFixed(2);
            document.getElementById('inputSubtotal').value = subtotal;
            document.getElementById('inputTotal').value = subtotal;
        }

        function toggleNoItemsMessage() {
            if (document.querySelectorAll('.item-row').length === 0) {
                noItemsMessage.classList.remove('d-none');
            } else {
                noItemsMessage.classList.add('d-none');
            }
        }

        function reindexItems() {
            document.querySelectorAll('.item-row').forEach((row, index) => {
                row.setAttribute('data-index', index);

                row.querySelector('.item-select').setAttribute('name', `items[${index}][item_id]`);
                row.querySelector('.quantity').setAttribute('name', `items[${index}][quantity]`);
                row.querySelector('.unit-price').setAttribute('name', `items[${index}][unit_price]`);
                row.querySelector('.batch-no').setAttribute('name', `items[${index}][batch_no]`);
                row.querySelector('.expires-at').setAttribute('name', `items[${index}][expires_at]`);
            });

            itemIndex = document.querySelectorAll('.item-row').length;
        }

        // Form validation
        document.getElementById('saleForm').addEventListener('submit', function(e) {
            const items = document.querySelectorAll('.item-row');
            if (items.length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the sale.');
                return false;
            }

            let valid = true;
            items.forEach(row => {
                const itemId = row.querySelector('.item-select').value;
                const quantity = row.querySelector('.quantity').value;
                const unitPrice = row.querySelector('.unit-price').value;

                if (!itemId || !quantity || !unitPrice) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields for all items.');
                return false;
            }
        });
    });
</script>
@endpush
