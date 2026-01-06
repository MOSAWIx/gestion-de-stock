<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facture;

class FixFactureReferencesSeeder extends Seeder
{
    public function run()
    {
        $factures = Facture::whereNull('reference')->get();

        if ($factures->isEmpty()) {
            $this->command->info('✅ Toutes les factures ont déjà une référence.');
            return;
        }

        foreach ($factures as $facture) {
            $facture->reference = 'BL-' . str_pad($facture->id, 6, '0', STR_PAD_LEFT);
            $facture->save();
        }

        $this->command->info('🎉 Références générées avec succès.');
    }
}
