<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingChange extends Model
{
    protected $fillable = [
        'booking_id', 'admin_id', 'field', 'old_value', 'new_value'
    ];
}
