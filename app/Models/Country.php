<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    use HasFactory;

    public function authors(): HasMany
    {
        return $this->hasMany(Author::class);
    }

    public function books(): HasManyThrough
    {
        return $this->hasManyThrough(Book::class, Author::class);
    }
}
