<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestView extends Model
{
    public $timestamps = false;

    protected $fillable = ['viewed_at', 'user_agent'];

    protected function casts(): array
    {
        return ['viewed_at' => 'datetime'];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
