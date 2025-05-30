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
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->id();
            // tableの時間帯のカラム
            $table->enum('time_slot',  ['09:00 ~ 11:00', '11:00 ~ 13:00', '13:00 ~ 15:00', '15:00 ~ 17:00', '17:00 ~ 19:00', '19:00 ~ 21:00', 'closed']);
            // tableのWaitingStatusのカラム
            $table->enum('status', ['waiting', 'available'])->default('available');
            // tableのidを外部キーとして持つ
            // 自動的にtablesテーブルのidと紐づくようになっている
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_lists');
    }
};
