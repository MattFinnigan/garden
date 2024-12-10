<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model {
  use HasFactory;
  public $timestamps = false;
  protected $fillable = ['name', 'description', 'image', 'days_to_harvest', 'variety', 'sow_from', 'sow_to'];
}
