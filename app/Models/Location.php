<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Bed;
use App\Models\CropHistory;
class Location extends Model {
  use HasFactory;
  public function beds () {
    return $this->hasMany(Bed::class);
  }
  public function crop_history () {
    return $this->hasMany(CropHistory::class);
  }
}
