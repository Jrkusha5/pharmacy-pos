@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- Left side: Product grid --}}
        <div class="col-md-8">
            <div class="d-flex justify-content-between mb-3">
                <input type="text" id="search" class="form-control w-50" placeholder="Search product">
            </div>

            <div class="row">
                @foreach($items as $item)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h6 class="card-title">{{ $item->name }}</h6>
                            <p class="text-danger fw-bold">Birr {{ number_format($item->selling_price, 2) }}</p>
                            <p class="small text-muted">{{ $item->stock }} available</p>

                            <form action="{{ route('cart_items.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <div class="input-group mb-2">
                                    <input type="number" name="quantity" value="1" min="1" class="form-control text-center">
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Right side: Cart --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Cart</span>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Clear Cart</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($cart && $cart->items->count())
                                @foreach($cart->items as $cartItem)
                                <tr>
                                    <td>{{ $cartItem->item->name }}</td>
                                    <td>
                                        <form action="{{ route('cart_items.update', $cartItem->id) }}" method="POST" class="d-flex">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="quantity" value="{{ $cartItem->quantity }}" min="1" class="form-control form-control-sm text-center" style="width:60px;">
                                            <button type="submit" class="btn btn-sm btn-success ms-1">✓</button>
                                        </form>
                                    </td>
                                    <td>GH₵ {{ number_format($cartItem->unit_price, 2) }}</td>
                                    <td>GH₵ {{ number_format($cartItem->quantity * $cartItem->unit_price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart_items.destroy', $cartItem->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">✕</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Your Cart is Empty!</td>
                                </tr>
                            @endif
                        </tbody>
                        @if($cart && $cart->items->count())
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total:</td>
                                <td colspan="2">Birr {{ number_format($total, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="" class="btn btn-primary w-100">Checkout</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
