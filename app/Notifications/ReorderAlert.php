<?php

// app/Notifications/ReorderAlert.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Item;

class ReorderAlert extends Notification {
  public function __construct(public Item $item, public int $onHand) {}
  public function via($notifiable){ return ['mail','database']; }
  public function toMail($n){
    return (new MailMessage)
      ->subject("Reorder Alert: {$this->item->name}")
      ->line("On hand: {$this->onHand} | Reorder level: {$this->item->reorder_level}")
      ->line("Suggested reorder qty: {$this->item->reorder_quantity}");
  }
  public function toArray($n){ return ['item_id'=>$this->item->id,'on_hand'=>$this->onHand]; }
}

