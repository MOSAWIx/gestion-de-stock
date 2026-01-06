<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'reference',
        'client_id',
        'total',
    ];

    /* ===== RELATIONS ===== */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(FactureItem::class);
    }

    /* ===== GENERATE REFERENCE ===== */
    public static function generateReference()
    {
        $year = now()->year;

        $lastFacture = self::whereYear('created_at', $year)
            ->whereNotNull('reference')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastFacture) {
            $lastNumber = (int) substr($lastFacture->reference, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'FAC-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
