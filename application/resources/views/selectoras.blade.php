@extends('basePage')

@section('header')
@include('inHeader')
@endsection

@section('title')
Admin
@endsection

@section('content')
<!-- notification bar -->
<nav id="notification_container" class="navbar is-fixed-top is-hidden" role="navigation" aria-label="main navigation">
    <div class="column is-4 notification" id="notification_bar">
        <button class="delete" id="x_navegation"></button>
        <p id="notification_message"></p>
    </div>
</nav>

<div class="box column is-full mt-2 px-6 py-6 ">
    <div id="table_spinner" class="table-spinner" style="display:none;">
        <div class="spinner"></div>
    </div>
    <div class="column is-full table-container">
        <table class="table is-fullwidth" id="product_table">
            <thead></thead>
        </table>
    </div>
</div>

<div id="edit_modal" class="modal">
    <div class="modal-background" id="modal_background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title" id="edit_title">Actualizar Selectora</p>
            <p class="modal-card-title" id="create_title">Crear Selectora</p>
            <button class="delete" id="close_modal" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field">
                <label class="label" for="name">Nombre</label>
                <div class="control">
                    <input class="input" type="text" name="name" id="name">
                </div>
            </div>
            <div class="field">
                <label class="label" for="email">Correo Electrónico</label>
                <div class="control">
                    <input class="input" type="email" name="email" id="email">
                </div>
            </div>
        </section>
        <footer class="modal-card-foot">
            <input id="create_button" class="button is-primary" type="button" value="Crear">
            <input id="update_button" class="button is-warning is-hidden" type="button" value="Actualizar">
            <button class="button" id="cancel_modal">Cancelar</button>
        </footer>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bulma.min.js') }}"></script>
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script src="{{ asset('js/FileSaver.min.js') }}"></script>

<script src="{{ asset('js/html2canvas.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/canvg/3.0.9/umd.min.js"
    integrity="sha512-uFbOsMTwJI9fB3saQ+aHUcHuhRyxxHonT7AWjFL25AglbRxiG7x3+3jDAA9YGVpgrdKRBTBHdSx9Xnyt7VsDNQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ asset('js/selectorasList.js') }}"></script>

@endsection