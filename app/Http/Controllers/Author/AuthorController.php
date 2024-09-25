<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Models\Author;
use Carbon\Carbon;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::whereNull('deleted_at')
            ->with('country')
            ->get()
            ->map(function ($author) {
                return [
                    'id'        => $author->id,
                    'name'      => $author->name,
                    'bio'       => $author->bio,
                    'birthdate' => $author->birthdate,
                    'country'   => $author->country->name
                ];
            });

        return response()->json([
            'authors'   => $authors,
            'message'   => 'Show all authors'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        $author = Author::create($request->validated());

        return response()->json([
            'author'    => $author,
            'message'   => 'Author created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        if (!$author || $author->deleted_at != null) {
            abort(404);
        }

        # Eager loading
        $author->load('country');

        $author = [
            'id'        => $author->id,
            'name'      => $author->name,
            'bio'       => $author->bio,
            'birthdate' => $author->birthdate,
            'country'   => $author->country->name
        ];

        return response()->json([
            'author'    => $author,
            'message'   => "Show detail of {$author['name']}"
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        if (!$author || $author->deleted_at != null) {
            abort(404);
        }

        $author->update($request->validated());

        return response()->json(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        if (!$author || $author->deleted_at != null) {
            abort(404);
        }

        $author->update([
            'deleted_at' => Carbon::now()
        ]);

        return response()->json(status: 204);
    }
}
