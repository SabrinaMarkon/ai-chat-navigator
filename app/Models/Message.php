<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Message extends Model
{
    use Searchable;

    protected $fillable = [
        'conversation_id',
        'content',
        'role',
        'position',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'content' => $this->content,
            'role' => $this->role,
            'position' => $this->position,
        ];
    }
}
