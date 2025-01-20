<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $connection = 'mysql'; // Conexão com o MySQL
    protected $table = 'users'; // Tabela227 de usuários no MySQL

    protected $fillable = [
        'matricula',
        'name',
        'usuariobd',
        'password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getAuthIdentifierName()
    {
        return 'usuariobd';
    }

    public function getAuthIdentifier()
    {
        return $this->usuariobd;
    }
}
