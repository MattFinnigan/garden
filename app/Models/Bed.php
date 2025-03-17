<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CropEntry;
use Illuminate\Support\Carbon;
use App\Models\BedImage;

class Bed extends Model {
  use HasFactory;

  protected $fillable = [
    'name',
    'description',
    'l',
    'w',
    'x',
    'y',
    'deactivated'
  ];

  protected $with = ['images'];

  public function images () {
    return $this->hasMany(BedImage::class);
  }
}
