<?php

// app/Notifications/BatchExpiryAlert.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Batch;

class BatchExpiryAlert extends Notification {
  public function __construct(public Batch $batch, public int $daysLeft) {}
  public function via($notifiable){ return ['mail','database']; }
  public function toMail($notifiable){
    return (new MailMessage)
      ->subject("Expiry Alert: {$this->batch->item->name} / {$this->batch->batch_no}")
      ->line("Batch expires on {$this->batch->expires_at?->toDateString()} ({$this->daysLeft} days left).")
      ->line("Qty on hand: {$this->batch->qty_on_hand}");
  }
  public function toArray($n){ return ['batch_id'=>$this->batch->id,'days_left'=>$this->daysLeft]; }
}

