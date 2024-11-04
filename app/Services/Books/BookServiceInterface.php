<?php

namespace App\Services\Books;

use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;

interface BookServiceInterface
{
    public function getAllBooks();
    public function getBookById(Book $book);
    public function createBook(StoreBookRequest $request);
    public function updateBook(UpdateBookRequest $request, Book $book);
    public function deleteBook(Book $book);
    public function searchBooks(Request $request);
}
