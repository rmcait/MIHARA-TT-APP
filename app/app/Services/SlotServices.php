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

        // 初回にファイルが存在しなければ、'closed'を保存して作成しておく
        if (!Storage::exists('last_slot.txt')) {
            Storage::put('last_slot.txt', 'closed');
            Log::info('📄 last_slot.txt を初期化しました（初回）');
        }

        // txtファイルから前回のSlot（次の時間帯）を取得
        $lastSlot = Storage::exists('last_slot.txt') ? Storage::get('last_slot.txt') : 'closed';
        Log::info('now: ' . $nowTime);
        Log::info('Current Slot: ' . $currentSlot);
        Log::info('Next Slot: ' . $nextSlot);
        Log::info('Last Slot (next): ' . $lastSlot);

        // currentSlotがclosedの場合、全てのtableとwaitingListのstatusをavailableにリセット
        if ($currentSlot === 'closed') {
            Log::info('🔍 currentSlotが閉じているため、全てのテーブルと待機リストの状態をリセットします');

            // 全テーブルのstatusをavailableに設定
            $tables = Table::all();
            foreach ($tables as $table) {
                $table->status = Table::STATUS_AVAILABLE;
                $table->save();
                Log::info('Table ' . $table->id . ' status reset to available');
            }

            // 全待機リストのstatusをavailableに設定
            $waitingLists = WaitingList::all();
            foreach ($waitingLists as $waiting) {
                $waiting->status = WaitingList::STATUS_AVAILABLE;
                $waiting->save();
                Log::info('Waiting list ' . $waiting->id . ' status reset to available');
            }
        }

        // nextSlotと一致するタイミングで処理を実行
        if ($currentSlot === $lastSlot) {
            Log::info('🔍 次のスロットに進むため、スロット更新処理を実行');

            // 全テーブルのリセット処理
            $tables = Table::all();
            $tables->status = Table::STATUS_AVAILABLE; // 利用中から空きに変更

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
        }
    }
}