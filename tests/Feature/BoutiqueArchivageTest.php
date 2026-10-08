<?php
namespace Tests\Feature;
use App\Models\Boutique;
use App\Models\Commande;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
class BoutiqueArchivageTest extends TestCase
{
    use RefreshDatabase;
    private function createClient(string $email='client.archive@test.com'): User
    {
        $u=new User(); $u->name='Client Archive Test'; $u->email=$email; $u->password='password123'; $u->role='client'; $u->email_verified_at=now(); $u->save(); return $u;
    }
    private function createBoutique(User $client,string $statut='active'): Boutique
    {
        return Boutique::create(['id_client'=>$client->id_user,'nom_boutique'=>'Boutique Archive Test','wilaya'=>'Oran','adresse'=>'Oran','statut'=>$statut]);
    }
    public function test_un_client_peut_archiver_sa_boutique(): void
    {
        $c=$this->createClient(); $b=$this->createBoutique($c);
        $r=$this->actingAs($c)->put(route('boutiques.update',$b),['action_statut'=>'archiver']);
        $r->assertRedirect(route('boutiques.index'));
        $this->assertDatabaseHas('boutiques',['id_boutique'=>$b->id_boutique,'statut'=>'archivee']);
    }
    public function test_un_client_peut_reactiver_sa_boutique(): void
    {
        $c=$this->createClient(); $b=$this->createBoutique($c,'archivee');
        $r=$this->actingAs($c)->put(route('boutiques.update',$b),['action_statut'=>'reactiver']);
        $r->assertRedirect(route('boutiques.index'));
        $this->assertDatabaseHas('boutiques',['id_boutique'=>$b->id_boutique,'statut'=>'active']);
    }
    public function test_une_boutique_archivee_reste_visible_dans_mes_boutiques(): void
    {
        $c=$this->createClient(); $this->createBoutique($c,'archivee');
        $r=$this->actingAs($c)->get(route('boutiques.index')); $r->assertOk(); $r->assertSee('Boutique Archive Test'); $r->assertSee('Archivée');
    }
    public function test_une_boutique_archivee_ne_peut_pas_recevoir_une_nouvelle_commande(): void
    {
        $c=$this->createClient(); $b=$this->createBoutique($c,'archivee');
        $this->expectException(ValidationException::class);
        Commande::create(['id_client'=>$c->id_user,'id_boutique'=>$b->id_boutique,'statut'=>'soumise','motif_statut'=>null,'stock_reserve'=>false,'montant_total'=>0]);
    }
}
