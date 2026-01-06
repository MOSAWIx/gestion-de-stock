<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'produit_id',
        'type',
        'quantite',
        'stock_avant',
        'stock_apres',
        'motif',
        'user_id',
    ];

    /**
     * mouvement appartient à un produit
     */
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * mouvement appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
