<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CropEntry;

class Unit extends Model {
  use HasFactory;

  protected $fillable = [
    'image',
    'crop_id',
    'name'
  ];
  public function crop_entries() {
    return $this->hasMany(CropEntry::class);
  }

}
