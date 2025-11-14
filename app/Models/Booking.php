<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
    ];

    /**
     * @return BelongsTo: Get the user (attendee) who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo: Get the event that was booked.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

}
