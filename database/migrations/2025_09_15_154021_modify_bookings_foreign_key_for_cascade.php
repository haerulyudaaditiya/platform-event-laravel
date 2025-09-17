<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Hapus foreign key yang lama
            $table->dropForeign(['event_id']);

            // Tambahkan foreign key yang baru dengan onDelete('cascade')
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Logika untuk membatalkan (opsional, tapi baik untuk ada)
            $table->dropForeign(['event_id']);
            $table->foreign('event_id')
                ->references('id')
                ->on('events');
        });
    }
};
