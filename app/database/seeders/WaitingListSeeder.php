<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WaitingList;
use App\Models\Table;
use Illuminate\Database\Seeder;

class WaitingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeSlots = WaitingList::getCurrentAndPreviousTimeSlot();
        $currentSlot = $timeSlots['current'];

        foreach (Table::all() as $table) {
                WaitingList::create([
                    'time_slot' => $currentSlot,
                    'status' => WaitingList::STATUS_AVAILABLE,
                    'table_id' => $table->id,
                ]);
        }
    }
}
