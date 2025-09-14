<?php
// tests/Feature/PublicHomepageTest.php

use App\Models\Event;
use App\Models\User;
use function Pest\Laravel\get; // Helper Pest untuk membuat request GET

// Kita memberitahu Pest untuk me-refresh database setiap kali tes dijalankan
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('pengunjung bisa melihat event yang sudah publish di homepage', function () {
    // 1. Arrange (Persiapan)
    // Buat satu event yang sudah di-publish
    $publishedEvent = Event::factory()->create(['is_published' => true]);
    // Buat satu event yang masih draft
    $draftEvent = Event::factory()->create(['is_published' => false]);

    // 2. Act (Aksi)
    // Lakukan request GET ke halaman utama, seolah-olah kita adalah pengunjung
    $response = get(route('home'));

    // 3. Assert (Penegasan/Pengecekan)
    // Pastikan halaman berhasil dimuat (status 200 OK)
    $response->assertStatus(200);
    // Pastikan nama event yang di-publish MUNCUL di halaman
    $response->assertSee($publishedEvent->name);
    // Pastikan nama event yang draft TIDAK MUNCUL di halaman
    $response->assertDontSee($draftEvent->name);
});
