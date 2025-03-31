<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Table;
use App\Models\WaitingList;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SlotServices 
{
    public function autoUpdateFromWaiting()
    {
        // 現在のSlot取得
        $slotContext = WaitingList::getTimeSlotWithContext();
        $currentSlot = $slotContext['current'];
        $nextSlot = $slotContext['next']; // 次のスロットを取得
        $nowTime = $slotContext['now'];

        // ファイルが存在しなければ nextSlot を保存して作成
        if (!Storage::exists('last_slot.txt')) {
            Storage::put('last_slot.txt', $nextSlot);
            Log::info("📄 last_slot.txt を初期化しました（初回）: {$nextSlot}");
        }
        // txtファイルから前回のSlot（次の時間帯）を取得
        $lastSlot = Storage::exists('last_slot.txt') ? Storage::get('last_slot.txt') : 'closed';
        Log::info('now: ' . $nowTime);
        Log::info('Current Slot: ' . $currentSlot);
        Log::info('Next Slot: ' . $nextSlot);
        Log::info('Last Slot (next): ' . $lastSlot);

        // nextSlotと一致するタイミングで処理を実行
        if ($currentSlot === $lastSlot) {
            Log::info('🔍 次のスロットに進むため、スロット更新処理を実行');

            // 全テーブルのリセット処理
            $tables = Table::all();
            foreach ($tables as $table) {
                $table->status = Table::STATUS_AVAILABLE;
                $table->save();
                Log::info("🧹 テーブルID {$table->id} を空き状態に更新");
            }

            // currentSlotのwaitingを探して処理
            $waitingLists = WaitingList::where('time_slot', $currentSlot)
                ->where('status', WaitingList::STATUS_WAITING)
                ->get();

            foreach ($waitingLists as $waiting) {
                // 対応するテーブルを取得
                $table = $waiting->table;
                $table->status = Table::STATUS_IN_USE; // 待機状態から利用中に変更
                $table->save();
            }

            $waitingLists = WaitingList::where('time_slot', '!=', $nextSlot)->get();

            foreach ($waitingLists as $waiting) {

                $waiting->status = WaitingList::STATUS_AVAILABLE;
                $waiting->time_slot = $nextSlot;
                $waiting->save();
            }

            // 現在のスロットを保存して次回に備える
            Storage::put('last_slot.txt', $nextSlot);

            Log::info('スロット更新完了: ' . $nextSlot);
        } else {
            // スロットが変更されていない場合のログ
            Log::info('🔍 スロットは変更されていないためスキップ');

            $waitingLists = WaitingList::where('time_slot', $lastSlot)
            ->where('status', WaitingList::STATUS_WAITING)
            ->get();



        if ($waitingLists->isEmpty()) {
            Log::info("📭 次のスロット（{$lastSlot}）に待機中はありません。");
        } else {
            Log::info("📝 現在のスロット（{$lastSlot}）に待機中のリスト:");
            foreach ($waitingLists as $waiting) {
                Log::info("・テーブルID: {$waiting->table_id}, ステータス: {$waiting->status}");
            }
        }

            // スロットが変更されていない場合の処理
            Log::info('スロットは変更されていません。次のスロット: ' . $nextSlot);
        }
    }
}