<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publisher\StorePublisherRequest;
use App\Http\Requests\Publisher\UpdatePublisherRequest;
use App\Models\Publisher;
use Carbon\Carbon;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publishers = Publisher::whereNull('deleted_at')->get();

        return response()->json([
            'publishers'    => $publishers,
            'message'       => 'Show all publishers'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePublisherRequest $request)
    {
        $publisher = Publisher::create($request->validated());

        return response()->json([
            'publisher' => $publisher,
            'message'   => 'Publisher create successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $publisher)
    {
        abort_if(!$publisher || $publisher->deleted_at != null, 404);

        return response()->json([
            'publisher' => $publisher,
            'message'   => "Show detail of {$publisher->name}"
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePublisherRequest $request, Publisher $publisher)
    {
        abort_if(!$publisher || $publisher->deleted_at != null, 404);

        $publisher->update($request->validated());

        return response()->json(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher)
    {
        abort_if(!$publisher || $publisher->deleted_at != null, 404);

        $publisher->update([
            'deleted_at' => Carbon::now()
        ]);

        return response()->json(status: 204);
    }
}
