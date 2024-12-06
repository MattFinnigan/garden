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
  protected $with = ['crop_entries'];
  public function crop_entries() {
    return $this->hasMany(CropEntry::class);
  }

}
