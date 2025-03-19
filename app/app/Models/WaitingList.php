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
        ['09:00', '11:00', '09-11'],
        ['11:00', '13:00', '11-13'],
        ['13:00', '15:00', '13-15'],
        ['15:00', '17:00', '15-17'],
        ['19:00', '21:00', '19-21'],
    ];

    
    
    public static function getCurrentAndPreviousTimeSlot(): array
{
    $now = Carbon::now();

    foreach (self::TIME_SLOTS as $index => [$start, $end, $label]) {
        if (
            $now->between(
                Carbon::createFromFormat('H:i', $start),
                Carbon::createFromFormat('H:i', $end)
            )
        ) {
            $previous = $index > 0 ? self::TIME_SLOTS[$index - 1][2] : 'closed';

            return [
                'current' => $label,
                'previous' => $previous,
            ];
        }
    }

    // 該当なしの場合は両方とも 'closed'
    return [
        'current' => 'closed',
        'previous' => 'closed',
    ];
}
}   
