<?php

use App\Models\Event;
use App\Models\User;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('organizer hanya bisa melihat event miliknya sendiri di dasbor', function () {
    // Arrange: Buat dua organizer
    $organizerA = User::factory()->organizer()->create();
    $organizerB = User::factory()->organizer()->create();

    // Buat event untuk masing-masing organizer
    $eventA = Event::factory()->create(['user_id' => $organizerA->id]);
    $eventB = Event::factory()->create(['user_id' => $organizerB->id]);

    // Act: Login sebagai Organizer A dan kunjungi halaman manajemen event
    $response = actingAs($organizerA)->get(route('events.index'));

    // Assert:
    // Pastikan halaman berhasil dimuat
    $response->assertStatus(200);
    // Pastikan event milik Organizer A terlihat
    $response->assertSee($eventA->name);
    // Pastikan event milik Organizer B TIDAK terlihat
    $response->assertDontSee($eventB->name);
});

test('pengguna biasa tidak bisa mengakses dasbor organizer', function () {
    // Arrange: Buat user biasa
    $user = User::factory()->create(['role' => 'user']);

    // Act: Login sebagai user biasa dan coba akses dasbor event
    $response = actingAs($user)->get(route('events.index'));

    // Assert: Pastikan akses ditolak (Forbidden)
    $response->assertStatus(403);
});
