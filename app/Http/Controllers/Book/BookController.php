<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Books\BookCollection;
use App\Http\Resources\Books\BookResource;
use App\Models\Book;
use App\Services\Books\BookServiceInterface;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(protected BookServiceInterface $service)
    {
    }

    /**
     * Search books
     */
    public function searchBooks(Request $request)
    {
        $books = $this->service->searchBooks($request);

        if ($books->isEmpty()) {
            return response()->json([
                'book' => []
            ], status: 404);
        }

        return new BookCollection($books);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = $this->service->getAllBooks();

        return new BookCollection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = $this->service->createBook($request);

        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book = $this->service->getBookById($book);

        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->service->updateBook($request, $book);

        return response()->json(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $this->service->deleteBook($book);

        return response()->json(status: 204);
    }
}
