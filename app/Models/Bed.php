<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\Crop;

class Bed extends Model {
  use HasFactory;

  protected $fillable = [
    'name',
    'description',
    'image',
  ];

  public function location () {
    return $this->belongsTo(Location::class);
  }

  public function crops () {
    return $this->hasMany(Crop::class);
  }
}
