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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            // unsignedTinyInteger: 0 to 255の正の数を許容している
            // つまり省メモリで数値を扱うことができる
            $table->unsignedTinyInteger('number');
            // 利用状況のカラム、デフォルトは空き
            $table->enum('status', ['available', 'in_use'])->default('available'); // 利用状況
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
