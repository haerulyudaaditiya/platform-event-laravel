<?php

use App\Models\Event;
use App\Models\User;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('pengunjung (tamu) bisa melihat event yang sudah publish di homepage', function () {
    // Arrange: Buat event publish dan draft
    $publishedEvent = Event::factory()->create(['is_published' => true]);
    $draftEvent = Event::factory()->create(['is_published' => false]);

    // Act: Kunjungi halaman utama sebagai tamu
    $response = get(route('home'));

    // Assert: Pastikan event publish terlihat dan draft tidak terlihat
    $response->assertStatus(200);
    $response->assertSee($publishedEvent->name);
    $response->assertDontSee($draftEvent->name);
});

test('pengguna biasa yang login bisa melihat event yang sudah publish di homepage', function () {
    // Arrange: Buat user biasa dan event-event
    $user = User::factory()->create(['role' => 'user']);
    $publishedEvent = Event::factory()->create(['is_published' => true]);
    $draftEvent = Event::factory()->create(['is_published' => false]);

    // Act: Login sebagai user dan kunjungi halaman utama
    $response = actingAs($user)->get(route('home'));

    // Assert: Pastikan hasilnya sama seperti tamu
    $response->assertStatus(200);
    $response->assertSee($publishedEvent->name);
    $response->assertDontSee($draftEvent->name);
});
