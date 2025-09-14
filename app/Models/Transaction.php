<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Properti $fillable menentukan kolom mana saja dari tabel
     * yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'snap_token',
    ];

    /**
     * Mendefinisikan relasi: Satu Transaksi dimiliki oleh seorang User.
     * Nama method 'user'
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi: Satu Transaksi memiliki banyak Booking.
     * Nama method 'bookings'
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
