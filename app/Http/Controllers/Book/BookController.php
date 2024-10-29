<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Search books
     */
    public function searchBooks(Request $request)
    {
        if (!$request->hasAny(['title', 'language', 'publihser', 'genre']) &&
            ($request->min_price != 1 || $request->max_price == 1) &&
            ($request->max_price != 1 || $request->min_price == 1)) {
            abort(404);
        }

        $book = Book::whereNull('deleted_at')
            ->filter($request)
            ->when($request->min_price == 1, function ($query) {
                $query->orderBy('price');
            })
            ->when($request->max_price == 1, function ($query) {
                $query->orderBy('price', 'desc');
            })
            ->with(['publisher', 'genre', 'authors.country', 'categories'])
            ->get()
            ->map(function ($book) {
                return [
                    'id'            => $book->id,
                    'title'         => $book->title,
                    'description'   => $book->description,
                    'price'         => $book->price,
                    'stock'         => $book->stock,
                    'image_path'    => $book->image_path,
                    'language'      => $book->language,
                    'publisher'     => optional($book->publisher)->name ?? 'Unknown Publisher',
                    'genre'         => optional($book->genre)->name ?? 'Unknown Genre',
                    'published_at'  => $book->published_at,
                    'categories'    => $book->categories->pluck('name'),
                    'authors'       => $book->authors->map(function ($author) {
                        return [
                            'name'      => $author->name,
                            'country'   => optional($author->country)->name ?? 'Unknown Country'
                        ];
                    })
                ];
            });

        if ($book->isEmpty()) {
            return response()->json([
                'book' => []
            ], status: 404);
        }

        return response()->json([
            'book'      => $book,
            'message'   => 'Show search of books'
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::whereNull('deleted_at')
            ->with(['publisher', 'genre', 'authors.country', 'categories'])
            ->withCount(['authors', 'categories'])
            ->get()
            ->map(function ($book) {
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
            });

        return response()->json([
            'books'     => $books,
            'message'   => 'Show all books'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        if ($request->validated('image_path') && $request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $image_name = 'book_' . uniqid() . '_' . time() . '.' . $file->extension();
            $file->storeAs("public/books/{$image_name}");
        }

        $book = $request->safe()->except('authors', 'categories');
        $book['image_path'] = $image_name;
        $book = Book::create($book);
        $book->authors()->attach($request->authors);
        $book->categories()->attach($request->categories);

        return response()->json([
            'book'    => $book,
            'message' => 'Book created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        if (!$book || $book->deleted_at != null) {
            abort(404);
        }

        # Eager loading for N+1 query
        $book->load(['publisher', 'genre', 'authors.country', 'categories']);

        $book = [
            'id'            => $book->id,
            'title'         => $book->title,
            'description'   => $book->description,
            'price'         => $book->price,
            'stock'         => $book->stock,
            'image_path'    => $book->image_path,
            'language'      => $book->language,
            'publisher'     => optional($book->publisher)->name ?? 'Unknown Publisher',
            'genre'         => optional($book->genre)->name ?? 'Unknown Genre',
            'published_at'  => $book->published_at,
            'categories'    => $book->categories->pluck('name'),
            'authors'       => $book->authors->map(function ($author) {
                return [
                    'name'      => $author->name,
                    'country'   => optional($author->country)->name ?? 'Unknown Country'
                ];
            })
        ];

        return response()->json([
            'book'      => $book,
            'message'   => "Show detail of {$book['title']}"
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        abort_if(!$book || $book->deleted_at != null, 404);

        if ($request->validated('image_path') && $request->hasFile('image_path')) {
            $file = $request->file('image_path');

            $image_name = 'book_' . uniqid() . '_' . time() . '.' . $file->extension();
            $file->storeAs("public/books/{$image_name}");
            $book['image_path'] = $image_name;
        }

        $book->update($request->safe()->except('authors', 'categories'));
        $book->authors()->sync($request->authors);
        $book->categories()->sync($request->categories);

        return response()->json(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        abort_if(!$book || $book->deleted_at != null, 404);

        $book->update([
            'deleted_at' => Carbon::now()
        ]);

        $book->authors()->detach($book->authors->pluck('id'));
        $book->categories()->detach($book->categories->pluck('id'));

        return response()->json(status: 204);
    }
}
