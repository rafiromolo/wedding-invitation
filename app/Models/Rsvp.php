<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    /** @use HasFactory<\Database\Factories\RsvpFactory> */
    use HasFactory;

    protected $fillable = ['status', 'attending_count', 'message'];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
