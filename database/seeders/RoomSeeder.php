<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rooms')->truncate();

        $prices = [
            'single' => 50000,
            'double' => 100000,
            'suite' => 250000,
        ];

        $rooms = [
            [
                'room_number' => '101',
                'type' => 'single',
                'price' => $prices['single'],
                'status' => 'available',
                'capacity' => '2',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '102',
                'type' => 'double',
                'price' => $prices['double'],
                'status' => 'reserved',
                'capacity' => '4',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '201',
                'type' => 'suite',
                'price' => $prices['suite'],
                'status' => 'occupied',
                'capacity' => '8',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '202',
                'type' => 'double',
                'price' => $prices['double'],
                'status' => 'available',
                'capacity' => '4',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '301',
                'type' => 'single',
                'price' => $prices['single'],
                'status' => 'available',
                'capacity' => '2',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '302',
                'type' => 'suite',
                'price' => $prices['suite'],
                'status' => 'reserved',
                'capacity' => '8',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '401',
                'type' => 'single',
                'price' => $prices['single'],
                'status' => 'available',
                'capacity' => '2',
                'climate_control' => 'fan',
            ],
            [
                'room_number' => '402',
                'type' => 'double',
                'price' => $prices['double'],
                'status' => 'available',
                'capacity' => '4',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '501',
                'type' => 'suite',
                'price' => $prices['suite'],
                'status' => 'occupied',
                'capacity' => '8',
                'climate_control' => 'air_conditioning',
            ],
            [
                'room_number' => '502',
                'type' => 'double',
                'price' => $prices['double'],
                'status' => 'reserved',
                'capacity' => '4',
                'climate_control' => 'fan',
            ],
        ];

        DB::table('rooms')->insert($rooms);
    }
}
