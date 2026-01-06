<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureItem extends Model
{
    protected $fillable = [
        'facture_id',
        'produit_id',
        'prix',
        'quantite',
        'total_ligne',
    ];




    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
