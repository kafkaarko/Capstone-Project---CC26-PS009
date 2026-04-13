<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();

        // relasi user
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        // file gambar
        $table->string('image');

        // hasil OCR mentah
        $table->text('raw_text')->nullable();

        // hasil parsing
        $table->bigInteger('price_output')->nullable();
        $table->timestamp('datetime')->nullable();
        $table->string('category')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
