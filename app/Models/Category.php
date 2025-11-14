<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    /**
     * @return BelongsToMany: get the events with that category
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'category_events');
    }
}
