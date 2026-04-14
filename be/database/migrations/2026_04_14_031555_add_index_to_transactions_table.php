<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->index(['user_id', 'created_at'], 'idx_user_created');
        $table->index(['user_id', 'category'], 'idx_user_category');
    });
}

public function down()
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->dropIndex('idx_user_created');
        $table->dropIndex('idx_user_category');
    });
}
};
