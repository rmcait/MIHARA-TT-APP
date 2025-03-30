<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Carbonクラスの追加
use Carbon\Carbon;

class WaitingList extends Model
{
    protected $fillable = ['time_slot', 'status', 'table_id'];

    const STATUS_WAITING = 'waiting';
    const STATUS_AVAILABLE = 'available';

    const TIME_SLOTS = [
        // ['開始時刻', '終了時刻', 'スロット名']
        ['09:00', '11:00', '09:00 ~ 11:00'],
        ['11:00', '13:00', '11:00 ~ 13:00'],
        ['13:00', '15:00', '13:00 ~ 15:00'],
        ['15:00', '17:00', '15:00 ~ 17:00'],
        ['19:00', '21:00', '19:00 ~ 21:00'],
    ];

    
    
    public static function getTimeSlotWithContext(): array
    {
        $now = Carbon::now('Asia/Tokyo'); // 現在の時刻（日本時間）

        // 特別処理として、8:30から9:00の間はnextが9:00 - 11:00になるようにする
        $eightThirty = Carbon::createFromFormat('H:i', '08:30');
        $nine = Carbon::createFromFormat('H:i', '09:00');

        if ($now->between($eightThirty, $nine)) {
            return [
                'now'      => $now, // 現在の時刻を返す
                'previous' => 'closed',
                'current'  => 'closed',
                'next'     => self::TIME_SLOTS[0][2],
            ];
        }

        // 通常の時間帯判定処理
        foreach (self::TIME_SLOTS as $index => [$start, $end, $label]) {
            $startTime = Carbon::createFromFormat('H:i', $start);
            $endTime = Carbon::createFromFormat('H:i', $end);


            if ($now->gte($startTime) && $now->lt($endTime)) {
                return [
                    'now'      => $now, // 現在の時刻を返す
                    'previous' => $index > 0 ? self::TIME_SLOTS[$index - 1][2] : 'closed',
                    'current'  => $label,
                    'next'     => isset(self::TIME_SLOTS[$index + 1]) ? self::TIME_SLOTS[$index + 1][2] : 'closed',
                ];
            }
        }

        return [
            'now'      => $now, // 現在の時刻を返す
            'previous' => 'closed',
            'current'  => 'closed',
            'next'     => self::TIME_SLOTS[0][2],
        ];
    }


public function table()
{
    return $this->belongsTo(Table::class);
}

}   
