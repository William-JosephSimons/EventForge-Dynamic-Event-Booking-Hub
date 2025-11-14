<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date_time',
        'location',
        'capacity',
    ];

    // casts date_time into a datetime object
    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo: Get the organiser who created the event.
     */
    public function organiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany: Get all the bookings for the event.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return BelongsToMany: The attendees who have booked the event.
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookings');
    }

    /**
     * @return BelongsToMany: the categories the event has
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_events');
    }
}
