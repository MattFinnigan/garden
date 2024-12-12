<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('bed_images', function (Blueprint $table) {
      $table->id();
      $table->foreignId('bed_id')->constrained()->onDelete('cascade');
      $table->string('name');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::withoutForeignKeyConstraints(function () {
      Schema::dropIfExists('bed_images');
    });
  }
};
