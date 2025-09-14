<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'total_price',
        'status',
        'unique_code',
        'is_selected',
        'transaction_id',
    ];

    // Relasi: Sebuah booking dimiliki oleh seorang User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Sebuah booking milik sebuah Event
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Relasi: Sebuah booking memiliki banyak Tiket (Many-to-Many)
    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class)
                    ->withPivot('quantity', 'price_per_ticket');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
