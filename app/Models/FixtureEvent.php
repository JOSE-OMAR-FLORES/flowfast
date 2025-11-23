<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixtureEvent extends Model
{
    protected $fillable = [
        'fixture_id',
        'player_id',
        'team_id',
        'event_type',
        'minute',
        'extra_time',
        'description',
        'metadata',
    ];

    protected $casts = [
        'minute' => 'integer',
        'extra_time' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

        /**
         * Determina si el evento es una sustitución.
         */
        public function isSubstitution(): bool
        {
            return $this->event_type === 'substitution';
        }

            /**
             * Devuelve el minuto completo del evento, incluyendo tiempo extra si aplica.
             */
            public function getFullMinuteAttribute(): string
            {
                if ($this->extra_time && $this->extra_time > 0) {
                    return $this->minute . '+' . $this->extra_time;
                }
                return (string) $this->minute;
            }
}
