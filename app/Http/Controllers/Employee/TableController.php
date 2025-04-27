<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\WaitingList;

class TableController extends Controller
{
    // テーブル一覧表示
    public function index()
    {
        $tables = Table::all();
        // 全待機リストの状態を取得（テーブルとのリレーションも取得）
        $waitingLists = WaitingList::with('table')->get();
        $timeSlotContext = WaitingList::getTimeSlotWithContext(); 
        return view('employee.tables', compact('tables', 'waitingLists', 'timeSlotContext'));
    }

    // 利用状況を切り替える処理（AJAX用）
    public function toggleStatus(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $timesSlotContext = WaitingList::getTimeSlotWithContext();

        $currentSlotContext = $timesSlotContext['current'];

        if ($currentSlotContext === 'closed') {
            // 全テーブルをavailableにするだけ
            Table::query()->update(['status' => Table::STATUS_AVAILABLE]);
            
        }

        $table->status = $table->status === Table::STATUS_AVAILABLE
            ? Table::STATUS_IN_USE
            : Table::STATUS_AVAILABLE;
        $table->save();

        return response()->json(['status' => $table->status]);
    }

    // 待機リストの状態を切り替える処理（AJAX用）
    public function toggleWaitingStatus(Request $request, $id)
{
    $waiting = WaitingList::findOrFail($id);

    $now = now('Asia/Tokyo');
    $timeSlotContext = WaitingList::getTimeSlotWithContext();
    
    $nextSlotLabel = $timeSlotContext['next'];

    $currentSlotLabel = $timeSlotContext['current'];

    $today830 = $now->copy()->setTime(8, 30);

        // まだ今日の8:30前かつcurrentがclosedだったらリセット
        if ($now->lt($today830) && $currentSlotLabel === 'closed') {
            WaitingList::query()->update(['status' => 'available']);
        }

    // next が "closed" の場合 → 翌日 08:30 を超えるまでは切り替えNG
    if ($nextSlotLabel === 'closed' || $nextSlotLabel === '09:00 ~ 11:00') {
        $now = now('Asia/Tokyo');
        $today830 = $now->copy()->setTime(8, 30);
    
        // 今が今日の8:30より前なら、今日の8:30を使う
        // 今が今日の8:30より後なら、明日の8:30を使う
        $target830 = $now->gt($today830) 
            ? $now->copy()->addDay()->setTime(8, 30)
            : $today830;
    
        if ($now->lt($target830)) {
            return response()->json([
                'error' => '営業時間外です。切り替えは8:30以降に可能です。'
            ], 422);
        }
    } else {
        // 通常スロットに対して 30分前チェックを行う
        $slot = collect(WaitingList::TIME_SLOTS)->firstWhere(2, $nextSlotLabel);
        if ($slot) {
            $nextSlotStart = \Carbon\Carbon::createFromFormat('H:i', $slot[0], 'Asia/Tokyo');
            $threshold = $nextSlotStart->copy()->subMinutes(30);

            if ($now->lt($threshold)) {
                return response()->json([
                    'error' => '切り替えは次のスロットの30分前から可能です。'
                ], 422);
            }
        }
    }

    $waiting->status = $waiting->status === 'waiting' ? 'available' : 'waiting';
    $waiting->save();

    return response()->json([
        'status' => $waiting->status,
        'table_id' => $waiting->table->id
    ]);
}

    // テーブル状態を一括で取得するAPI
    public function fetchTableStatuses()
    {
        $tables = Table::all(['id', 'number', 'status']);
        return response()->json($tables);
    }

     // テーブルのステータス一覧を返す
     public function getTableStatuses()
     {
         return response()->json(Table::select('id', 'status')->get());
     }
 
     // 待ち状況のステータス一覧を返す
     public function getWaitingListStatuses()
     {
         return response()->json(WaitingList::select('id', 'status')->get());
     }

     // 現在と次のタイムスロット情報を返す
        public function getTimeSlotContext()
        {
            $timeSlotContext = WaitingList::getTimeSlotWithContext();
            return response()->json($timeSlotContext);
        }
}