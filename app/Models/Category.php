<?php

// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
  protected $fillable = [
    'name',
  'code',
  'description'
];
  public function items(){
     return $this->hasMany(Item::class); }
}

