@extends('basePage')

@section('header')
    @extends('outHeader')
@endsection

@section('title')
    Login
@endsection

@section('content')
    <form method="post">
        @csrf
        <div class="columns is-centered my-6">
            <div class="box column is-half py-6 px-6">
                <h2 class="title has-text-centered">Entrar</h2>
                <div class="my-4">
                    <label for="username">Usuario</label>
                    <input class="input" name="username" type="text" id="username" placeholder="Username">
                    <small class="has-text-danger">{{ $errors->first('username') }}</small>
                </div>
                <div class="my-4">
                    <label for="pass">Contraseña</label>
                    <input class="input" name="password" type="password" id="username" placeholder="pass">
                    <small class="has-text-danger">{{ $errors->first('password') }}</small>
                </div>
                <div class="has-text-right">
                    <button type="submit" class="button is-danger">Entrar</button>
                </div>
            </div>
        </div>
    </form>
@endsection