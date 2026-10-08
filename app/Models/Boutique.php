<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Boutique extends Model
{
    protected $primaryKey = 'id_boutique';
    protected $fillable = ['id_client','nom_boutique','wilaya','adresse','statut'];
    public function client(): BelongsTo { return $this->belongsTo(User::class,'id_client','id_user'); }
    public function commandes(): HasMany { return $this->hasMany(Commande::class,'id_boutique','id_boutique'); }
    public function documents(): HasMany { return $this->hasMany(Document::class,'id_boutique','id_boutique'); }
    public function scopeActive(Builder $query): Builder { return $query->where('statut','active'); }
    public function scopeArchivee(Builder $query): Builder { return $query->where('statut','archivee'); }
    public function estActive(): bool { return $this->statut === 'active'; }
    public function estArchivee(): bool { return $this->statut === 'archivee'; }
}
