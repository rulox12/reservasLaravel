<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        DB::table('bookings')->truncate();

        $bookings = [
            [
                'user_id' => 2,
                'room_id' => 1,
                'check_in_date' => Carbon::today()->format('Y-m-d'),
                'check_out_date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'total_price' => 150000,
                'status' => 'confirmed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'room_id' => 2,
                'check_in_date' => Carbon::today()->addDays(1)->format('Y-m-d'),
                'check_out_date' => Carbon::today()->addDays(4)->format('Y-m-d'),
                'total_price' => 200000,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'room_id' => 1,
                'check_in_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'check_out_date' => Carbon::today()->addDays(5)->format('Y-m-d'),
                'total_price' => 150000,
                'status' => 'confirmed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'room_id' => 3,
                'check_in_date' => Carbon::today()->addDays(1)->format('Y-m-d'),
                'check_out_date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'total_price' => 180000,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'room_id' => 2,
                'check_in_date' => Carbon::today()->format('Y-m-d'),
                'check_out_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'total_price' => 200000,
                'status' => 'confirmed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bookings')->insert($bookings);
    }
}
