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
        $waiting->status = $waiting->status === 'waiting' ? 'available' : 'waiting';
        $waiting->save();

        return response()->json([
            'status' => $waiting->status,
            'table_id' => $waiting->table->id // table_idを追加
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