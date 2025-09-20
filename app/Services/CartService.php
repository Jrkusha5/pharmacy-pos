<?php

// app/Services/CartService.php
namespace App\Services;

use App\Models\{Cart,CartItem,Item};

class CartService
{
  public function getOrCreate(string $sessionKey): Cart {
    return Cart::firstOrCreate(['session_key'=>$sessionKey]);
  }

  public function addItem(Cart $cart, int $itemId, int $qty, ?float $price = null): CartItem {
    $item = Item::findOrFail($itemId);
    $ci = CartItem::firstOrNew(['cart_id'=>$cart->id,'item_id'=>$itemId]);
    $ci->quantity = ($ci->exists ? $ci->quantity : 0) + $qty;
    $ci->unit_price = $price ?? $item->batches()->orderBy('expires_at')->value('sell_price') ?? 0;
    $ci->save();
    return $ci;
  }

  public function removeItem(Cart $cart, int $itemId): void {
    CartItem::where('cart_id',$cart->id)->where('item_id',$itemId)->delete();
  }

  public function clear(Cart $cart): void { $cart->items()->delete(); }
}

