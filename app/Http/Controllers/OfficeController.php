<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Models\Office;
use Inertia\Inertia;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Route: GET|HEAD /offices
     */
    public function index()
    {
        $offices = Office::all();
        return Inertia::render('Index', ['offices' => $offices]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * Route: GET|HEAD /offices/create
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * Route: POST /offices
     */
    public function store(StoreOfficeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * Route: GET|HEAD /offices/{office}
     */
    public function show(Office $office)
    {
        $offices = Office::all();


        return Inertia::render('Index', ['offices' => $offices, 'selectedOffice' => $office]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * Route: GET|HEAD /offices/{office}/edit
     */
    public function edit(Office $office)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * Route: PUT|PATCH offices/{office}
     */
    public function update(UpdateOfficeRequest $request, Office $office)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * Route: DELETE offices/{office}
     */
    public function destroy(Office $office)
    {
        //
    }
}
