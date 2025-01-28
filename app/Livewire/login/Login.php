<?php

namespace App\Livewire\login;

use Illuminate\Support\Facades\Session;
use App\Models\PCempr;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Exception;

class Login extends Component
{
    use LivewireAlert;

    public $loginName;
    public $password;
    public $remember = false;

    protected $rules = [
        'loginName' => 'required|string',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        try {
            $usuario = $this->buscarUsuario();

            if ($usuario) {
                $user = new PCempr((array)$usuario);

                // Verificar permissões
                $permissoes = $this->getPermissoes($user);

                if ($permissoes === false) {
                    return; // Retorna imediatamente se não houver permissões
                }

                // Atribuir permissões ao usuário
                Session::put('bdc_controc', $permissoes);

                // Busca Foto
                $foto = $this->buscarFoto($user->matricula);

                if (isset($foto->fotemp)) {
                    $fotoBase64 = 'data:image/jpeg;base64,' . base64_encode($foto->fotemp);
                    Session::put('foto_usuario', $fotoBase64);
                }

                // Autenticar usuário
                Auth::login($user, $this->remember);

                return redirect()->route('index');
            } else {
                $this->alertaErro('Usuário ou Senha Inválidos');
            }
        } catch (Exception $e) {
            $this->alertaErro('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    // Metodo para buscar usuário no banco de dados Oracle
    private function buscarUsuario()
    {
        try {
            return DB::connection('oracle')
                ->table('pcempr')
                ->where('usuariobd', strtoupper($this->loginName))
                ->where('situacao' , 'A')
                ->whereRaw("decrypt(senhabd, usuariobd) = ?", [strtoupper($this->password)])
                ->first();
        } catch (Exception $e) {
            $this->alertaErro('Erro ao buscar usuário: ' . $e->getMessage());
            return null;
        }
    }

    // Metodo para buscar a foto do usuário
    private function buscarFoto($matricula)
    {
        try {
             $sql = /** @lang text */'
                    SELECT F.FOTEMP
                    FROM R034FOT@DBLSENIOR F
                    INNER JOIN PCEMPR P ON TO_NUMBER (F.NUMCAD) = TO_NUMBER (P.CHAPA_RM)
                    WHERE P.MATRICULA = ?
                ';
             $foto = DB::connection('oracle')->selectOne($sql, [$matricula]);

            return $foto;
        } catch (Exception $e) {
            return null;
        }
    }


    // Metodo para obter permissões do usuário
    private function getPermissoes(PCempr $user)
    {
        try {
            $permissoes = DB::connection('oracle')
                ->table('bdc_controc')
                ->where('codusuario', $user->matricula)
                ->where('ACESSO', 'S')
                ->orderBy('codmod', 'asc')
                ->get();

            if ($permissoes->isEmpty()) {
                $this->alertaErro('Usuário sem permissão');
                return false;
            }

            return $this->montarPermissoes($permissoes, $user->matricula);
        } catch (Exception $e) {
            $this->alertaErro('Erro ao obter permissões: ' . $e->getMessage());
            return false;
        }
    }

    // Metodo para montar as permissões da PCCONTRO e PCCONTROI
    private function montarPermissoes($permissoes, $matricula)
    {
        try {
            $resultado = [];

            foreach ($permissoes as $contro) {
                $contro->bdc_controi = DB::connection('oracle')
                    ->table('bdc_controi')
                    ->where('codmod', $contro->codmod)
                    ->where('codusuario', $matricula)
                    ->where('ACESSO', 'S')
                    ->get();

                $resultado[] = $contro;
            }

            return $resultado;
        } catch (Exception $e) {
            $this->alertaErro('Erro ao montar permissões: ' . $e->getMessage());
            return false;
        }
    }

    // Metodo para exibir alertas de erro
    private function alertaErro($mensagem)
    {
        $this->alert('error', $mensagem, [
            'timer' => 3000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.login.login')->layout('layouts.login-layout');
    }
}
