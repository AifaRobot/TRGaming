@extends('basePage')

@section('title')
    Login
@endsection

@section('content')
<section class="login-hero">
    <div class="login-box">
        <div class="has-text-centered mb-5">
            <img src="{{ asset('talent_recruiters.jpg') }}" alt="Talent Recruiters" style="max-width: 180px;">
        </div>
        <h1 class="login-title">Panel de Administración</h1>
        <form method="post">
            @csrf
            <div class="field">
                <label class="label">Usuario</label>
                <div class="control has-icons-left">
                    <input class="input" name="nickname" type="text" placeholder="Usuario">
                    <span class="icon is-left"><i class="fas fa-user"></i></span>
                </div>
                <small class="has-text-danger">{{ $errors->first('nickname') }}</small>
            </div>
            <div class="field mt-4">
                <label class="label">Contraseña</label>
                <div class="control has-icons-left">
                    <input class="input" name="password" type="password" placeholder="Contraseña">
                    <span class="icon is-left"><i class="fas fa-lock"></i></span>
                </div>
                <small class="has-text-danger">{{ $errors->first('password') }}</small>
            </div>
            <div class="field mt-5">
                <button type="submit" class="button is-fullwidth login-btn">
                    <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                    <span>Entrar</span>
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
