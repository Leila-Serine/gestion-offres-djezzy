<?php

namespace App\Http\Controllers;
use App\Services\OffreService;
use App\Models\Offre;

use Illuminate\Http\Request;

class OffreController extends Controller
{public function __construct(
    private OffreService $offreService
) {
}
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $offres = $this->offreService->getAll();

    return view('offres.index', compact('offres'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    return view('offres.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validatedData = $request->validate([
        'nom_offre' => ['required', 'string', 'max:100'],
        'categorie' => ['required', 'string', 'max:20'],
    ]);

    $this->offreService->create($validatedData);

    return redirect()
        ->route('offres.index')
        ->with('success', 'Offre créée avec succès.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offre $offre)
{
    return view('offres.edit', compact('offre'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offre $offre)
{
    $validatedData = $request->validate([
        'nom_offre' => ['required', 'string', 'max:100'],
        'categorie' => ['required', 'string', 'max:20'],
    ]);

    $this->offreService->update($offre, $validatedData);

    return redirect()
        ->route('offres.index')
        ->with('success', 'Offre modifiée avec succès.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offre $offre)
{
    $this->offreService->delete($offre);

    return redirect()
        ->route('offres.index')
        ->with('success', 'Offre supprimée avec succès.');
}
}
