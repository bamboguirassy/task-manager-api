<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'terminee',
        'priorite',
        'date_limite'
    ];

    // load  
    // protected  $with = ['user'];
    


    public static function boot(): void
    {
        parent::boot();

        // executer avant chaque création de tache
        static::creating(function ($tache) {
            $tache->uid = (string) Str::uuid();
            $tache->user_id = auth()->id();
        });
        // executer après chaque création de tache
        static::created(function ($tache) {
            
        });
        // avant chaque mise à jour de tache
        static::updating(function ($tache) {
            
        });
        // après chaque mise à jour de tache
        static::updated(function ($tache) {
            
        });
        // avant chaque suppression de tache
        static::deleting(function ($tache) {
            if(!$tache->terminee) {
                abort(403, 'Vous ne pouvez pas supprimer une tache non terminée');
            }
        });
        // après chaque suppression de tache
        static::deleted(function ($tache) {
            
        });
    }

    /**
     * Get the user that owns the Tache
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
