<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use Carbon\Carbon;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::whereNull('deleted_at')
            ->with('publisher')
            ->with('genre')
            ->with('authors')
            ->with('categories')
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
                    'publisher'     => $book->publisher->name,
                    'genre'         => $book->genre->name,
                    'published_at'  => $book->published_at,
                    'authors'       => $book->authors->pluck('name'),
                    'categories'    => $book->categories->pluck('name'),
                ];
            });
        return $books;
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

        $book = $request->except('authors', 'categories');
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

        $book->load(['publisher', 'genre', 'authors', 'categories']);

        $book = [
            'id'            => $book->id,
            'title'         => $book->title,
            'description'   => $book->description,
            'price'         => $book->price,
            'stock'         => $book->stock,
            'image_path'    => $book->image_path,
            'language'      => $book->language,
            'publisher'     => $book->publisher->name,
            'genre'         => $book->genre->name,
            'published_at'  => $book->published_at,
            'authors'       => $book->authors->pluck('name'),
            'categories'    => $book->categories->pluck('name'),
        ];

        return response()->json([
            'book'      => $book,
            'message'   => 'Show detail of ' . $book['title']
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        if (!$book || $book->deleted_at != null) {
            abort(404);
        }

        if ($request->validated('image_path') && $request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $image_name = 'book_' . uniqid() . '_' . time() . '.' . $file->extension();
            $file->storeAs("public/books/{$image_name}");
            $book['image_path'] = $image_name;
        }

        $book->update($request->except('authors', 'categories'));
        $book->authors()->sync($request->authors);
        $book->categories()->sync($request->categories);

        return response()->json(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if (!$book || $book->deleted_at != null) {
            abort(404);
        }

        $book->update([
            'deleted_at' => Carbon::now()
        ]);

        $book->authors()->detach($book->authors->pluck('id'));
        $book->categories()->detach($book->categories->pluck('id'));

        return response()->json(status: 204);
    }
}
