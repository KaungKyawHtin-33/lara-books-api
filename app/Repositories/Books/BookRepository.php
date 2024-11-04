<?php

namespace App\Repositories\Books;

use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookRepository implements BookRepositoryInterface
{
    public function getAll()
    {
        return Book::whereNull('deleted_at')
            ->with([
                'publisher:id,name',
                'genre:id,name',
                'authors:id,name,country_id',
                'authors.country:id,name',
                'categories:id,name'
            ])
            ->withCount(['authors', 'categories'])
            ->paginate(5);
    }

    public function getById(Book $book)
    {
        if (!$book || $book->deleted_at != null) {
            abort(404);
        }

        # Eager loading for N+1 query
        $book->load([
            'publisher:id,name',
            'genre:id,name',
            'authors:id,name,country_id',
            'authors.country:id,name',
            'categories:id,name'
        ]);

        return $book;
    }

    public function create(StoreBookRequest $request)
    {
        $image_name = null;

        if ($request->validated('image_path') && $request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $image_name = processImageUpload($file, 'book');
        }

        $book = $request->safe()->except('authors', 'categories');
        $book['image_path'] = $image_name;

        DB::beginTransaction();
        try {
            $book = Book::create($book);

            if ($request->filled('authors')) {
                $book->authors()->attach($request->authors);
            }

            if ($request->filled('categories')) {
                $book->categories()->attach($request->categories);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $book;
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        abort_if(!$book || $book->deleted_at != null, 404);

        if ($request->validated('image_path') && $request->hasFile('image_path')) {
            $file = $request->file('image_path');

            $book['image_path'] = processImageUpload($file, 'book');
        }

        DB::beginTransaction();
        try {
            $book->update($request->safe()->except('authors', 'categories'));

            if ($request->filled('authors')) {
                $book->authors()->sync($request->authors);
            }

            if ($request->filled('categories')) {
                $book->categories()->sync($request->categories);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(Book $book)
    {
        abort_if(!$book || $book->deleted_at != null, 404);

        $book->update([
            'deleted_at' => Carbon::now()
        ]);

        $book->authors()->detach($book->authors->pluck('id'));
        $book->categories()->detach($book->categories->pluck('id'));
    }

    public function searchBooks(Request $request)
    {
        if (!$request->hasAny(['title', 'language', 'publisher', 'genre']) &&
            ($request->min_price != 1 || $request->max_price == 1) &&
            ($request->max_price != 1 || $request->min_price == 1)) {
            abort(404);
        }

        return Book::whereNull('deleted_at')
            ->filter($request)
            ->when($request->min_price == 1, function ($query) {
                $query->orderBy('price');
            })
            ->when($request->max_price == 1, function ($query) {
                $query->orderBy('price', 'desc');
            })
            ->with(['publisher', 'genre', 'authors.country', 'categories'])
            ->paginate(5);
    }
}
