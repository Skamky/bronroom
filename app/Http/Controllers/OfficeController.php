<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Models\Office;
use Illuminate\Http\JsonResponse;
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
        $offices = Office::withCount('rooms')->get();

        return Inertia::render('Home', ['offices' => $offices]);
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
        $offices = Office::withCount('rooms')->get();

        // Загружаем комнаты с подсчётом загруженности для передачи в Index
        // Используем load() чтобы сохранить доступ к методам getWorkload() и getStatus()
        $office->load(['rooms' => function ($q) {
            $q->withCount([
                'events as booked_events' => function ($q2) {
                    $q2->where('start_date', '<=', now())
                        ->where('end_date', '>=', now());
                },
            ])->orderBy('name');
        }]);

        // Преобразуем в array для Inertia (использует toArray() где есть workload и status)
        return Inertia::render('Index', [
            'offices' => $offices,
            'selectedOffice' => $office,
            'rooms' => $office->rooms,
        ]);
    }

    public function businessHours(Office $office): JsonResponse
    {
        return response()->json([
            'daysOfWeek' => [1, 2, 3, 4, 5],
            'startTime' => $office->start_time->format('H:i'),
            'endTime' => $office->end_time->format('H:i'),
        ]);
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
