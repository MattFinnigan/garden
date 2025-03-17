<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Crop;
use App\Models\Bed;

class CropEntry extends Model {
  use HasFactory;
  protected $table = 'crop_entries';
  protected $fillable = ['crop_id', 'location_id', 'action', 'stage', 'notes', 'image', 'qty', 'datetimestamp'];

  public function crop () {
    return $this->belongsTo(Crop::class);
  }
  public function bed () {
    return $this->belongsTo(Bed::class);
  }
}
