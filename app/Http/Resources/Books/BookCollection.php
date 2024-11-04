<?php

namespace App\Http\Resources\Books;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($book) {
                return [
                    'id'                => $book->id,
                    'title'             => $book->title,
                    'description'       => $book->description,
                    'price'             => $book->price,
                    'stock'             => $book->stock,
                    'image_path'        => $book->image_path,
                    'language'          => $book->language,
                    'publisher'         => optional($book->publisher)->name ?? 'Unknown Publisher',
                    'genre'             => optional($book->genre)->name ?? 'Unknown Genre',
                    'published_at'      => $book->published_at,
                    'categories_count'  => $book->categories_count,
                    'categories'        => $book->categories->pluck('name'),
                    'authors_count'     => $book->authors_count,
                    'authors'           => $book->authors->map(function ($author) {
                        return [
                            'name'      => $author->name,
                            'country'   => optional($author->country)->name ?? 'Unknown Country'
                        ];
                    })
                ];
            }),
            'meta' => [
                'total'         => $this->total(),
                'count'         => $this->count(),
                'per_page'      => $this->perPage(),
                'current_page'  => $this->currentPage(),
                'total_pages'   => $this->lastPage(),
            ]
        ];
    }
}
