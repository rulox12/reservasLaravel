<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run()
    {
        DB::table('rooms')->truncate();

        $rooms = [
            [
                'room_number' => '101',
                'type' => 'single',
                'price' => 50000,
                'status' => 'available',
                'capacity' => '2',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '102',
                'type' => 'double',
                'price' => 100000,
                'status' => 'reserved',
                'capacity' => '4',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '201',
                'type' => 'suite',
                'price' => 250000,
                'status' => 'occupied',
                'capacity' => '8',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '202',
                'type' => 'double',
                'price' => 150000,
                'status' => 'available',
                'capacity' => '4',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '301',
                'type' => 'single',
                'price' => 60000,
                'status' => 'available',
                'capacity' => '2',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '302',
                'type' => 'suite',
                'price' => 300000,
                'status' => 'reserved',
                'capacity' => '8',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '401',
                'type' => 'single',
                'price' => 55000,
                'status' => 'available',
                'capacity' => '2',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '402',
                'type' => 'double',
                'price' => 120000,
                'status' => 'available',
                'capacity' => '4',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '501',
                'type' => 'suite',
                'price' => 280000,
                'status' => 'occupied',
                'capacity' => '8',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '502',
                'type' => 'double',
                'price' => 130000,
                'status' => 'reserved',
                'capacity' => '4',
                'climate_control' => 'fan',
            ],
        ];

        DB::table('rooms')->insert($rooms);
    }
}
