<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CropHistory;
use App\Models\Plant;
use App\Models\Unit;

class Crop extends Model {
  use HasFactory;

  public function crop_history () {
    return $this->hasMany(CropHistory::class)->orderBy('datetimestamp', 'desc');
  }
  
  public function plant () {
    return $this->belongsTo(Plant::class);
  }

  public function units () {
    return $this->hasMany(Unit::class);
  }
}
