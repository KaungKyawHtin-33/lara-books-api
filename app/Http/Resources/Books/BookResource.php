<?php

namespace App\Http\Resources\Books;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->description,
            'price'             => $this->price,
            'stock'             => $this->stock,
            'image_path'        => $this->image_path,
            'language'          => $this->language,
            'publisher'         => optional($this->publisher)->name ?? 'Unknown Publisher',
            'genre'             => optional($this->genre)->name ?? 'Unknown Genre',
            'published_at'      => $this->published_at,
            'categories'        => $this->categories->pluck('name'),
            'authors'           => $this->authors->map(function ($author) {
                return [
                    'name'      => $author->name,
                    'country'   => optional($author->country)->name ?? 'Unknown Country'
                ];
            })
        ];
    }
}
