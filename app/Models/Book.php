<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function scopeFilter($query, $filter)
    {
        $query->when($filter['title'], function ($query, $search) {
            # Logical grouping
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });

        $query->when($filter['language'], function ($query, $search) {
            $query->where('language', 'like', "%{$search}%");
        });

        $query->when($filter['publisher'], function ($query, $search) {
            $query->whereHas('publisher', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        });

        $query->when($filter['genre'], function ($query, $search) {
            $query->whereHas('genre', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        });
    }
}
