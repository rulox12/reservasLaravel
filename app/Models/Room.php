<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'room_number',
        'type',
        'price',
        'status',
        'capacity',
        'climate_control'
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
