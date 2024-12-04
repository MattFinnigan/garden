<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Crop;
return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('units', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('image')->nullable();
      $table->foreignIdFor(Crop::class)->constrained();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::withoutForeignKeyConstraints(function () {
      Schema::dropIfExists('units');
    });
  }
};
