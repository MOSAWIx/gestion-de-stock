<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'name',
        'reference',
        'quantite',
        'prix_achat',
        'prix_vente',
        'description',
        'category_id',
        'stock_min',
        'status' // ⭐ مهم جداً
    ];

    public function isLowStock(): bool
    {
        if (is_null($this->stock_min)) {
            return false; // ou true selon ta règle
        }
        return $this->quantite < $this->stock_min;
    }

    public function getStatusAttribute()
    {
        if ($this->quantite == 0) {
            return 'rupture';
        }

        if ($this->quantite <= $this->stock_min) {
            return 'faible';
        }

        return 'en stock';
    }


    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }



    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
