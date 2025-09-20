@extends('layouts.app')

@section('title', 'New Purchase - Pharmacy Management')

@section('content')
<div class="container-fluid px-4">
  <div class="page-inner">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">New Purchase</h4>
          </div>
          <div class="card-body">

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('purchases.store') }}" method="POST">
              @csrf

              <!-- Purchase Header -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <label for="supplier_id" class="form-label">Supplier *</label>
                  <select class="form-select" name="supplier_id" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $supplier)
                      <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="invoice_no" class="form-label">Invoice No</label>
                  <input type="text" name="invoice_no" class="form-control" value="AUTO" readonly>
                </div>
                <div class="col-md-3">
                  <label for="purchased_at" class="form-label">Purchase Date *</label>
                  <input type="date" name="purchased_at" class="form-control" value="{{ old('purchased_at', now()->toDateString()) }}" required>
                </div>
                <div class="col-md-3">
                  <label for="status" class="form-label">Status *</label>
                  <select name="status" class="form-select" required>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="posted" {{ old('status') == 'posted' || old('status') === null ? 'selected' : '' }}>Posted</option>
                  </select>
                </div>
              </div>

              <!-- Payment Info -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <label for="payment_method" class="form-label">Payment Method *</label>
                  <select name="payment_method" class="form-select" required>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="paid_amount" class="form-label">Paid Amount *</label>
                  <input type="number" step="0.01" name="paid_amount" class="form-control" value="{{ old('paid_amount', 0) }}" required min="0">
                </div>
                <div class="col-md-3">
                  <label for="due_date" class="form-label">Due Date</label>
                  <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                </div>
                <div class="col-md-3">
                  <label for="due_amount" class="form-label">Due Amount</label>
                  <input type="number" step="0.01" name="due_amount" class="form-control" value="{{ old('due_amount', 0) }}" readonly>
                </div>
              </div>

              <!-- Purchase Items Table -->
              <div class="table-responsive mb-3">
                <table class="table table-bordered" id="itemsTable">
                  <thead class="table-light">
                    <tr>
                      <th>Item *</th>
                      <th>Qty *</th>
                      <th>Unit Cost *</th>
                      <th>Sell Price *</th>
                      <th>Expiry Date</th>
                      <th>Line Total</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <select name="items[0][item_id]" class="form-select item-select" required>
                          <option value="">-- Select Item --</option>
                          @foreach($items as $item)
                            <option value="{{ $item->id }}" data-cost="{{ $item->cost_price }}" data-price="{{ $item->sell_price }}">
                              {{ $item->name }} ({{ $item->sku }})
                            </option>
                          @endforeach
                        </select>
                      </td>
                      <td><input type="number" name="items[0][quantity]" class="form-control qty" min="1" value="1" required></td>
                      <td><input type="number" step="0.0001" name="items[0][unit_cost]" class="form-control unit-cost" required></td>
                      <td><input type="number" step="0.01" name="items[0][sell_price]" class="form-control sell-price" required></td>
                      <td><input type="date" name="items[0][expires_at]" class="form-control expiry-date"></td>
                      <td><input type="number" step="0.01" name="items[0][line_total]" class="form-control line-total" readonly></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-danger removeRow" disabled>
                          <i class="fa fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="mb-3">
                <button type="button" class="btn btn-sm btn-primary" id="addRow">
                  <i class="fa fa-plus"></i> Add Item
                </button>
              </div>

              <!-- Totals -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <label for="subtotal" class="form-label">Subtotal</label>
                  <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" value="0" readonly>
                </div>
                <div class="col-md-3">
                  <label for="tax" class="form-label">Tax</label>
                  <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="0">
                </div>
                <div class="col-md-3">
                  <label for="total" class="form-label">Total *</label>
                  <input type="number" step="0.01" name="total" id="total" class="form-control" value="0" readonly required>
                </div>
                <div class="col-md-3">
                  <label for="note" class="form-label">Notes</label>
                  <textarea name="note" class="form-control" rows="1">{{ old('note') }}</textarea>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-success">
                  <i class="fa fa-save me-2"></i> Save Purchase
                </button>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                  <i class="fa fa-times me-2"></i> Cancel
                </a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let rowIdx = 1;

$('#addRow').on('click', function() {
  let newRow = `<tr>
    <td>
      <select name="items[${rowIdx}][item_id]" class="form-select item-select" required>
        <option value="">-- Select Item --</option>
        @foreach($items as $item)
          <option value="{{ $item->id }}" data-cost="{{ $item->cost_price }}" data-price="{{ $item->sell_price }}">
            {{ $item->name }} ({{ $item->sku }})
          </option>
        @endforeach
      </select>
    </td>
    <td><input type="number" name="items[${rowIdx}][quantity]" class="form-control qty" min="1" value="1" required></td>
    <td><input type="number" step="0.0001" name="items[${rowIdx}][unit_cost]" class="form-control unit-cost" required></td>
    <td><input type="number" step="0.01" name="items[${rowIdx}][sell_price]" class="form-control sell-price" required></td>
    <td><input type="text" name="items[${rowIdx}][batch_no]" class="form-control batch-no"></td>
    <td><input type="date" name="items[${rowIdx}][expires_at]" class="form-control expiry-date"></td>
    <td><input type="number" step="0.01" name="items[${rowIdx}][line_total]" class="form-control line-total" readonly></td>
    <td>
      <button type="button" class="btn btn-sm btn-danger removeRow">
        <i class="fa fa-trash"></i>
      </button>
    </td>
  </tr>`;
  $('#itemsTable tbody').append(newRow);
  rowIdx++;

  // Enable remove buttons if there are multiple rows
  if ($('#itemsTable tbody tr').length > 1) {
    $('.removeRow').prop('disabled', false);
  }
});

// Remove row
$(document).on('click', '.removeRow', function() {
  $(this).closest('tr').remove();
  calculateTotals();

  // Disable remove button if only one row remains
  if ($('#itemsTable tbody tr').length === 1) {
    $('.removeRow').prop('disabled', true);
  }
});

// Auto-fill cost and price when item is selected
$(document).on('change', '.item-select', function() {
  let row = $(this).closest('tr');
  let selectedOption = $(this).find('option:selected');
  let cost = selectedOption.data('cost') || 0;
  let price = selectedOption.data('price') || 0;

  row.find('.unit-cost').val(cost);
  row.find('.sell-price').val(price);

  // Auto-generate batch number based on item and date
  let itemName = selectedOption.text().split(' (')[0].substring(0, 3).toUpperCase();
  let date = new Date().toISOString().slice(0, 10).replace(/-/g, '');
  let random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
  row.find('.batch-no').val(`${itemName}-${date}-${random}`);

  calculateLineTotal(row);
});

// Auto calculate line total
$(document).on('input', '.qty, .unit-cost', function() {
  let row = $(this).closest('tr');
  calculateLineTotal(row);
});

function calculateLineTotal(row) {
  let qty = parseFloat(row.find('.qty').val()) || 0;
  let unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
  let lineTotal = qty * unitCost;
  row.find('.line-total').val(lineTotal.toFixed(2));
  calculateTotals();
}

$('#tax, input[name="paid_amount"]').on('input', calculateTotals);

function calculateTotals() {
  let subtotal = 0;
  $('.line-total').each(function() {
    subtotal += parseFloat($(this).val()) || 0;
  });
  $('#subtotal').val(subtotal.toFixed(2));

  let tax = parseFloat($('#tax').val()) || 0;
  let paid = parseFloat($('input[name="paid_amount"]').val()) || 0;
  let total = subtotal + tax;
  $('#total').val(total.toFixed(2));

  // Update due_amount
  let dueAmount = total - paid;
  $('input[name="due_amount"]').val(dueAmount.toFixed(2));
}

// Initialize calculations
calculateTotals();
</script>
@endpush
