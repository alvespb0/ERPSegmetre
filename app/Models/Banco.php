<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banco extends Model
{
    protected $table = 'banco';

    use SoftDeletes;

    protected $fillable = [
        'nome',
        'cnpj', # unique
        'numero_banco'
    ];

    public function contas(){
        return $this->hasMany(Conta::class);
    }
}
