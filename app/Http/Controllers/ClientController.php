<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients
     */
    public function index(Request $request)
    {
        // On récupère les données uniques pour les filtres Tom Select
        $allNames = Client::pluck('client')->unique()->filter();
        $allPhones = Client::whereNotNull('telephone')->pluck('telephone')->unique()->filter();

        $query = Client::query();

        // 🔎 Recherche par nom client
        if ($request->filled('client')) {
            $query->where('client', 'like', '%' . $request->client . '%');
        }

        // 🔎 Recherche par téléphone
        if ($request->filled('telephone')) {
            $query->where('telephone', 'like', '%' . $request->telephone . '%');
        }

        $clients = $query
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        return view('clients.index', compact('clients', 'allNames', 'allPhones'));
    }

    /**
     * Formulaire d'ajout
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistrer un client
     */
    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string',
        ]);

        Client::create([
            'client' => $request->client,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'adresse' => $request->adresse,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client ajouté avec succès');
    }

    /**
     * Formulaire de modification
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Mettre à jour le client
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'client' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string',
        ]);

        $client->update([
            'client' => $request->client,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'adresse' => $request->adresse,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client modifié avec succès');
    }

    /**
     * Supprimer le client
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès');
    }
}
