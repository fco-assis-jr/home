<div class="row">
    @csrf
    <section class="login-content">
        <div class="logo">
            <img width="300" src="{{ asset('images/logo.png') }}" alt="logo" style="margin-top: -10px;">
        </div>
        <div class="login-box">
            <form class="login-form" wire:submit.prevent="login">
                <h3 class="login-head"><i class="bi bi-person me-2"></i>LOGIN</h3>
                <div class="mb-3">
                    <label class="form-label">NOME DE USUÁRIO</label>
                    <input class="form-control" type="text" placeholder="login" autofocus wire:model="loginName" autocomplete="off">
                    @error('loginName')
                    <span class="error">Este campo é obrigatório</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">SENHA</label>
                    <input class="form-control" type="password" placeholder="Password" wire:model="password" autocomplete="off">
                    @error('password')
                    <span class="error">Este campo é obrigatório</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <div class="utility">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" wire:model="remember"><span class="label-text">Permaneça conectado</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-3 btn-container d-grid">
                    <button class="btn btn-danger btn-block"><i class="bi bi-box-arrow-in-right me-2 fs-5"></i>ENTRAR</button>
                    {{ session('error') }}
                </div>
            </form>
        </div>
    </section>
    @script
    <script>
        let deviceType = window.innerWidth <= 660 ? 'mobile' : 'desktop';
        $wire.dispatch('post-created', { refreshPosts: deviceType });
    </script>
    @endscript
</div>
