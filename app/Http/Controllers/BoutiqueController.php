<?php
namespace App\Http\Controllers;
use App\Models\Boutique;
use App\Models\Document;
use App\Services\BoutiqueService;
use App\Support\Wilayas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
class BoutiqueController extends Controller
{
    public function __construct(private BoutiqueService $boutiqueService) {}
    public function index(): View
    {
        $boutiques = Boutique::query()->where('id_client',auth()->id())->withCount('commandes')->latest('id_boutique')->get();
        return view('boutiques.index',compact('boutiques'));
    }
    public function create(): View
    {
        $wilayas = Wilayas::all();
        return view('boutiques.create',compact('wilayas'));
    }
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom_boutique'=>['required','string','max:100'],
            'wilaya'=>['required','string',Rule::in(Wilayas::all())],
            'adresse'=>['required','string','max:255'],
            'justificatif_local'=>['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);
        $this->boutiqueService->create(auth()->user(),$validated,$request->file('justificatif_local'));
        return redirect()->route('boutiques.index')->with('success','Boutique ajoutée avec succès.');
    }
    public function show(Boutique $boutique): View
    {
        $this->authorizeOwner($boutique);
        $boutique->loadCount('commandes');
        $justificatif = $boutique->documents()->where('type_document','justificatif_local')->latest('id_document')->first();
        return view('boutiques.show',compact('boutique','justificatif'));
    }
    public function edit(Boutique $boutique): View
    {
        $this->authorizeOwner($boutique);
        $wilayas = Wilayas::all();
        $justificatif = $boutique->documents()->where('type_document','justificatif_local')->latest('id_document')->first();
        return view('boutiques.edit',compact('boutique','wilayas','justificatif'));
    }
    public function update(Request $request, Boutique $boutique): RedirectResponse
    {
        $this->authorizeOwner($boutique);
        if ($request->input('action_statut') === 'archiver') {
            $this->boutiqueService->archiver($boutique);
            return redirect()->route('boutiques.index')->with('success','Boutique archivée. Elle ne peut plus être utilisée pour une nouvelle commande.');
        }
        if ($request->input('action_statut') === 'reactiver') {
            $this->boutiqueService->reactiver($boutique);
            return redirect()->route('boutiques.index')->with('success','Boutique réactivée avec succès.');
        }
        $validated = $request->validate([
            'nom_boutique'=>['required','string','max:100'],
            'wilaya'=>['required','string',Rule::in(Wilayas::all())],
            'adresse'=>['required','string','max:255'],
            'justificatif_local'=>['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);
        $this->boutiqueService->update($boutique,$validated,$request->file('justificatif_local'));
        return redirect()->route('boutiques.show',$boutique)->with('success','Boutique modifiée avec succès.');
    }
    public function destroy(Boutique $boutique): RedirectResponse
    {
        $this->authorizeOwner($boutique);
        if (! $this->boutiqueService->delete($boutique)) {
            return redirect()->route('boutiques.index')->with('error','Cette boutique est déjà liée à une commande. Archivez-la plutôt que de la supprimer.');
        }
        return redirect()->route('boutiques.index')->with('success','Boutique supprimée avec succès.');
    }
    public function document(Boutique $boutique, Document $document): BinaryFileResponse
    {
        $this->authorizeOwner($boutique);
        if ((int)$document->id_boutique !== (int)$boutique->id_boutique || $document->type_document !== 'justificatif_local') abort(404);
        $disk = Storage::disk('local');
        if (! $document->chemin_fichier || ! $disk->exists($document->chemin_fichier)) abort(404);
        return response()->file($disk->path($document->chemin_fichier));
    }
    private function authorizeOwner(Boutique $boutique): void
    {
        if ((int)$boutique->id_client !== (int)auth()->id()) abort(403);
    }
}
