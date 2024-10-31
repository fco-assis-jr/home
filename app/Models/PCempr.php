<?php

namespace App\Models;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class PCempr extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;

    protected $connection = 'oracle';
    protected $table = 'pcempr';
    protected $primaryKey = 'matricula';

    protected $fillable = [
        'matricula',
        'nome',
        'usuariobd',
        'pccontro'

    ];


}
