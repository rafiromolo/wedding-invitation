<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Guest extends Model
{
    /** @use HasFactory<\Database\Factories\GuestFactory> */
    use HasFactory;

    protected $fillable = ['name', 'label', 'max_pax'];

    protected $hidden = ['token'];

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            if (empty($guest->token)) {
                $guest->token = static::generateUniqueToken();
            }
        });
    }

    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::lower(Str::random(20));
        } while (static::where('token', $token)->exists());

        return $token;
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function rsvp(): HasOne
    {
        return $this->hasOne(Rsvp::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(GuestView::class);
    }
}
