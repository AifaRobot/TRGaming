@extends('basePage')

@section('header')
    @include('inHeader')
@endsection

@section('title')
    Admin
@endsection

@section('content')
    <!-- notification bar -->
    <nav  id="notification_container" class="navbar is-fixed-top is-hidden" role="navigation" aria-label="main navigation">
        <div class="column is-4 notification" id="notification_bar">
            <button class="delete" id="x_navegation"></button>
            <p id="notification_message"></p>
        </div>
    </nav>

    <!-- the delete modal -->
    <div class="columns is-centered my-6" style="width:90%; margin: 0 auto;">
        <div class="columns is-centered my-6" style="width:90%; margin: 0 auto;">
            <div class="column">
                <a href="{{ URL::to('admin/users') }}">
                    <div class="box-data is-flex" id="box-usuarios">
                        <div class="box-data-header" id="usuarios">
                            <i class="fas fa-users" style="margin-right:8px;"></i>Usuarios totales
                        </div>
                        <div class="box-data-header button-listar" id="listar-usuarios">
                            Listar                        
                        </div>
                        <div class="box-data-body columns is-vcentered" style="margin: -6px;">
                            <div class="column result" id="total-usuarios">?</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="column">
                <a href="{{ URL::to('admin/listUser/usersfinished') }}">
                    <div class="box-data is-flex" id="box-completaron-todo">
                        <div class="box-data-header" id="completaron-todo">
                            <i class="fas fa-trophy" style="margin-right:8px;"></i>Completaron todo
                        </div>
                        <div class="box-data-header button-listar" id="listar-completaron-todo">
                            Listar                        
                        </div>
                        <div class="box-data-body columns is-vcentered" style="margin: -6px;">
                            <div class="column result" id="usuarios-jugaron">?</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
</div>
    <div class="columns is-centered my-6" style="width:90%; margin: 0 auto;">
        <div class="columns is-centered my-6" style="width:90%; margin: 0 auto;">
            <div class="column">
                <a href="{{ URL::to('admin/listUser/usersnotfinished') }}">
                    <div class="box-data is-flex" id="box-no-completaron-todo" >
                        <div class="box-data-header" id="no-completaron-todo">
                            <i class="fas fa-hourglass-half" style="margin-right:8px;"></i>No terminaron
                        </div>
                        <div class="box-data-header button-listar" id="listar-no-completaron-todo">
                            Listar                        
                        </div>
                        <div class="box-data-body columns is-vcentered" style="margin: -6px;">
                            <div class="column result" id="usuarios-no-terminaron">?</div>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="column">
                <a href="{{ URL::to('admin/listUser/usersnotplayed') }}">
                    <div class="box-data is-flex" id="box-no-jugaron">
                        <div class="box-data-header" id="no-jugaron">
                            <i class="fas fa-gamepad" style="margin-right:8px;"></i>No jugaron
                        </div>
                        <div class="box-data-header button-listar" id="listar-no-jugaron">
                            Listar                        
                        </div>
                        <div class="box-data-body columns is-vcentered" style="margin: -6px;">
                            <div class="column result" id="usuarios-no-jugaron">?</div>
                        </div>
                    </div>
                </a>
            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/canvg/3.0.9/umd.min.js" integrity="sha512-uFbOsMTwJI9fB3saQ+aHUcHuhRyxxHonT7AWjFL25AglbRxiG7x3+3jDAA9YGVpgrdKRBTBHdSx9Xnyt7VsDNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script src="{{ asset('js/admin.js') }}"></script>

@endsection
