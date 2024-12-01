<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Crop;
use App\Models\Location;

class CropHistory extends Model {
  use HasFactory;
  protected $table = 'crop_history';
  protected $fillable = ['crop_id', 'location_id', 'action', 'stage', 'notes', 'image', 'qty'];

  public function crop () {
    return $this->belongsTo(Crop::class);
  }
  public function location () {
    return $this->belongsTo(Location::class);
  }
}
