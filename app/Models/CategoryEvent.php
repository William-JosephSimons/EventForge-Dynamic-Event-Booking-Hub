<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryEvent extends Model
{
    protected $fillable = [
        'category_id',
        'event_id',
    ];

    /**
     * @return BelongsTo: Links event to category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo: Links category to event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
