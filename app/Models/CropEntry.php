<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Crop;
use App\Models\Location;
use App\Models\Bed;
use App\Models\Unit;

class CropEntry extends Model {
  use HasFactory;
  protected $table = 'crop_entries';
  protected $fillable = ['crop_id', 'location_id', 'action', 'stage', 'notes', 'image', 'qty', 'datetimestamp', 'bed_id', 'unit_id', 'area'];

  protected $with = ['location', 'bed', 'unit'];

  public function crop () {
    return $this->belongsTo(Crop::class);
  }
  public function location () {
    return $this->belongsTo(Location::class);
  }
  public function bed () {
    return $this->belongsTo(Bed::class);
  }
  public function unit () {
    return $this->belongsTo(Unit::class);
  }
}
