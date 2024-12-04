<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model {
  use HasFactory;

  protected $fillable = [
    'image',
    'crop_id',
    'name'
  ];
  public function cropHistory() {
    return $this->hasMany(CropHistory::class);
  }

}
