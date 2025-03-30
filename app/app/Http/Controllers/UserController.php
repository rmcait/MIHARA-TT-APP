<?php

namespace App\Http\Controllers;
use App\Models\WaitingList;
use App\Models\Table;
use App\Services\SlotServices; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $slotServices;

    // コンストラクタで SlotServices をインジェクト
    public function __construct(SlotServices $slotServices)
    {
        $this->slotServices = $slotServices;
    }
    public function index()
    {
        $tables = Table::all(); // テーブル情報の取得
        $waitingLists = WaitingList::all(); // 待機リストの取得
        // getTimeSlotWithContext を呼び出して現在の利用時間帯を取得
    $timeSlotContext = WaitingList::getTimeSlotWithContext(); 
    
        return view('user.tables', compact('tables', 'waitingLists', 'timeSlotContext'));
    }

    // 状態だけをJSONで返すAPI（定期的に取得用）
    public function fetchTableStatuses()
    {
        return response()->json(
            Table::select('id', 'number', 'status')->get()
        );
    }

    public function fetchWaitingLists()
{
    // 待機リスト情報だけを返す
    return response()->json(WaitingList::select('table_id', 'status')->get());
}

public function triggerAutoUpdate()
{
    // SlotServices の autoUpdateFromWaiting メソッドを呼び出し
    $this->slotServices->autoUpdateFromWaiting();

    // 処理が完了したことを知らせる
    return response()->json(['message' => '自動更新処理が完了しました']);
}
}
