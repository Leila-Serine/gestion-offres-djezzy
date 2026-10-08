<?php
namespace App\Providers;
use App\Models\Boutique;
use App\Models\Commande;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
class BoutiqueArchiveServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        View::composer('commandes.create', function ($view): void {
            $data = $view->getData();
            if (! array_key_exists('boutiques',$data)) return;
            $boutiques = $data['boutiques'];
            if ($boutiques instanceof Collection) {
                $view->with('boutiques',$boutiques->filter(fn($b)=>$b->statut === 'active')->values());
            } elseif (is_array($boutiques)) {
                $view->with('boutiques',array_values(array_filter($boutiques,fn($b)=>($b->statut ?? null) === 'active')));
            }
        });
        Commande::creating(function (Commande $commande): void {
            $boutique = Boutique::query()->find($commande->id_boutique);
            if ($boutique && $boutique->statut !== 'active') {
                throw ValidationException::withMessages([
                    'id_boutique'=>'Cette boutique est archivée et ne peut pas être utilisée pour une nouvelle commande.',
                ]);
            }
        });
    }
}
