<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\CropEntry;

class Bed extends Model {
  use HasFactory;

  protected $fillable = [
    'name',
    'description',
    'image',
    'l',
    'w'
  ];

  public function location () {
    return $this->belongsTo(Location::class);
  }

  public function crop_entries () {
    return $this->hasMany(CropEntry::class);
  }
}
