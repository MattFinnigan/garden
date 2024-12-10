<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('plants', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('variety');
      $table->string('description')->nullable();
      $table->string('image')->nullable();
      $table->int('days_to_harvest')->nullable();
      $table->int('sow_from')->nullable();
      $table->int('sow_to')->nullable();
    });
  }
  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('plants');
  }
};
