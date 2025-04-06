<?php

use App\Models\Office;
use App\Models\Room;
use App\Models\User;
use App\Services\BreadcrumbsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tests\Helpers\EventHelper;

beforeEach(function () {
    // Создаем тестовые данные
    $this->user = User::factory()->create();
    $this->office = Office::factory()->create(['name' => 'Test Office']);
    $this->room = Room::factory()->create([
        'office_id' => $this->office->id,
        'name' => 'Test Room'
    ]);

    // Используем EventHelper для создания события
    $this->event = EventHelper::create(
        $this->user,
        $this->room,
        Carbon::now(),
        Carbon::now()->addHour()
    );
});

it('generates home breadcrumb for root path', function () {
    mockRequest('/');

    $breadcrumbs = BreadcrumbsService::generate();

    expect($breadcrumbs)->toBe([
        ['url' => '/offices', 'text' => 'Главная']
    ]);
});

it('generates office level breadcrumbs', function () {
    mockRequest("/offices/{$this->office->id}");

    $breadcrumbs = BreadcrumbsService::generate();

    expect($breadcrumbs)->toBe([
        ['url' => '/offices', 'text' => 'Главная'],
        ['url' => "/offices/{$this->office->id}", 'text' => 'Test Office']
    ]);
});

it('generates room level breadcrumbs', function () {
    mockRequest("/offices/{$this->office->id}/rooms/{$this->room->id}");

    $breadcrumbs = BreadcrumbsService::generate();

    expect($breadcrumbs)->toBe([
        ['url' => '/offices', 'text' => 'Главная'],
        ['url' => "/offices/{$this->office->id}", 'text' => 'Test Office'],
        ['url' => "/offices/{$this->office->id}/rooms/{$this->room->id}", 'text' => 'Test Room']
    ]);
});

it('generates event level breadcrumbs', function () {
    mockRequest("/offices/{$this->office->id}/rooms/{$this->room->id}/events/{$this->event->id}");

    $breadcrumbs = BreadcrumbsService::generate();

    expect($breadcrumbs)->toBe([
        ['url' => '/offices', 'text' => 'Главная'],
        ['url' => "/offices/{$this->office->id}", 'text' => 'Test Office'],
        ['url' => "/offices/{$this->office->id}/rooms/{$this->room->id}", 'text' => 'Test Room'],
        ['url' => "/offices/{$this->office->id}/rooms/{$this->room->id}/events/{$this->event->id}", 'text' => $this->event->name]
    ]);
});

it('logs warning when entity not found', function () {
    Log::shouldReceive('warning')
        ->once()
        ->withArgs(function ($message) {
            return str_contains($message, 'Breadcrumbs entity not found');
        });

    mockRequest("/offices/999/rooms/999");

    $breadcrumbs = BreadcrumbsService::generate();

    expect($breadcrumbs)->toBe([
        ['url' => '/offices', 'text' => 'Главная']
    ]);
});

// Хелпер для мока запроса
function mockRequest(string $path): void
{
    $request = Request::create($path);
    app()->instance('request', $request);
}
