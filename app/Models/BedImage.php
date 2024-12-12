<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bed;

class BedImage extends Model {
  use HasFactory;

  protected $fillable = [
    'bed_id',
    'name'
  ];
  public $timestamps = false;

  public function bed () {
    return $this->belongsTo(Bed::class);
  }
}
