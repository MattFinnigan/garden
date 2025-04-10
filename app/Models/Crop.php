<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CropEntry;
use App\Models\Plant;

class Crop extends Model {
  use HasFactory;
  protected $with = ['plant', 'crop_entries'];
  public function crop_entries() {
    return $this->hasMany(CropEntry::class)->orderBy('datetimestamp', 'desc');
  }
  public function plant() {
    return $this->belongsTo(Plant::class);
  }
  public function latestEntry() {
    return $this->hasOne(CropEntry::class)->orderBy('datetimestamp', 'desc');
  }
  public function firstEntry() {
    return $this->hasOne(CropEntry::class)->orderBy('datetimestamp', 'asc');
  }
}
