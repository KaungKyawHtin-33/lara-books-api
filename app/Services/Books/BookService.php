<?php

namespace App\Services\Books;

use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use App\Repositories\Books\BookRepositoryInterface;
use Illuminate\Http\Request;

class BookService implements BookServiceInterface
{
    public function __construct(protected BookRepositoryInterface $repository)
    {
    }

    public function getAllBooks()
    {
        return $this->repository->getAll();
    }

    public function getBookById(Book $book)
    {
        return $this->repository->getById($book);
    }

    public function createBook(StoreBookRequest $request)
    {
        return $this->repository->create($request);
    }

    public function updateBook(UpdateBookRequest $request, Book $book)
    {
        $this->repository->update($request, $book);
    }

    public function deleteBook(Book $book)
    {
        $this->repository->delete($book);
    }

    public function searchBooks(Request $request)
    {

        return $this->repository->searchBooks($request);
    }
}
