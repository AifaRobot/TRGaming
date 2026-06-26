@extends('basePage')

@section('header')
    @include('inHeader')
@endsection

@section('title')
    Admin
@endsection

@section('content')
    <nav  id="notification_container" class="navbar is-fixed-top is-hidden" role="navigation" aria-label="main navigation">
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

    <div class="modal" id="view_dialog">
        <div class="modal-background" id="view_dialog_background"></div>
        <div class="modal-card big-modal">
            <header class="modal-card-head">
                <p class="modal-card-title">Reporte del Candidato</p>
                <button class="delete" id="close_view_dialog" aria-label="close"></button>
            </header>
            <div id="report_spinner" class="table-spinner py-6">
                <div class="spinner"></div>
            </div>
            <section class="modal-card-body" id="export-data" style="display:none;">
                <div class="tabs is-boxed report-tabs mb-4">
                    <ul>
                        <li class="is-active" data-tab="tab-resumen"><a><span class="icon"><i class="fas fa-user"></i></span><span>Resumen</span></a></li>
                        <li data-tab="tab-mental"><a><span class="icon"><i class="fas fa-brain"></i></span><span>Agilidad Mental</span></a></li>
                        <li data-tab="tab-resultados"><a><span class="icon"><i class="fas fa-chart-bar"></i></span><span>Comprension y Resultados</span></a></li>
                        <li data-tab="tab-respuestas"><a><span class="icon"><i class="fas fa-comments"></i></span><span>Respuestas</span></a></li>
                    </ul>
                </div>

                <div id="tab-resumen" class="report-tab-content print-wrap">
                    <div class="columns">
                        <div class="column">
                            <div class="box">
                                <p class="title is-5 mb-3">Datos del Candidato</p>
                                <table class="table is-fullwidth is-striped">
                                    <tbody>
                                        <tr><td><strong>Nombre</strong></td><td class="worker_name"></td></tr>
                                        <tr><td><strong>Empresa</strong></td><td class="worker_company"></td></tr>
                                        <tr><td><strong>Posici&oacute;n</strong></td><td class="worker_pos"></td></tr>
                                        <tr><td><strong>Edad</strong></td><td class="worker_age"></td></tr>
                                        <tr><td><strong>Nivel Alcanzado</strong></td><td id="playedLevel"></td></tr>
                                        <tr><td><strong>Movimientos exitosos</strong></td><td id="success_move"></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="column">
                            <div class="box">
                                <p class="title is-5 mb-3">Tiempos</p>
                                <table class="table is-fullwidth is-striped">
                                    <tbody>
                                        <tr><td><strong>Tiempo total</strong></td><td id="total_time"></td></tr>
                                        <tr><td><strong>Tiempo en Demo</strong></td><td id="demo_time"></td></tr>
                                        <tr><td><strong>Tiempo en Juego</strong></td><td id="game_time"></td></tr>
                                        <tr><td><strong>Tiempo en Preguntas</strong></td><td id="question_time"></td></tr>
                                        <tr><td><strong>Planificaci&oacute;n Demo</strong></td><td id="init_time_demo"></td></tr>
                                        <tr><td><strong>Planificaci&oacute;n Juego</strong></td><td id="init_time_game"></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="has-text-centered mt-4">
                        <img src="{{ asset('talent_recruiters.jpg') }}" alt="Talent Recruiters" style="max-height:60px;">
                    </div>
                </div>

                <div id="tab-mental" class="report-tab-content print-wrap" style="display:none;">
                    <article class="message is-danger mb-4">
                        <div class="message-header"><p>Capacidad de an&aacute;lisis, razonabilidad y entendimiento del juego</p></div>
                        <div class="message-body">
                            <p class="mb-3">Eval&uacute;a la capacidad del candidato para comprender las reglas y formular una estrategia desde el inicio. Un desempe&ntilde;o alto indica comprensi&oacute;n r&aacute;pida y uso eficiente del tiempo.</p>
                            <div style="height:200px;"><canvas id="myChart"></canvas></div>
                            <div id="chart1" class="mt-2"></div>
                            <div id="chart2" class="mt-2"></div>
                        </div>
                    </article>
                    <article class="message is-link mb-4">
                        <div class="message-header"><p>Velocidad / Ritmo / Memoria Visual / Rapidez Mental</p></div>
                        <div class="message-body">
                            <p class="mb-3">Eval&uacute;a la fluidez y rapidez con que el candidato resuelve secuencias correctas. Un desempe&ntilde;o alto refleja memoria visual s&oacute;lida y ritmo de trabajo eficiente.</p>
                            <div style="height:200px;"><canvas id="myChart2"></canvas></div>
                            <div style="height:200px;" class="mt-3"><canvas id="myChart3"></canvas></div>
                            <div style="height:200px;" class="mt-3"><canvas id="myChart4"></canvas></div>
                            <div id="chart3" class="mt-2"></div>
                            <div id="chart4" class="mt-2"></div>
                            <div id="chart5" class="mt-2"></div>
                            <div class="columns mt-3 has-text-centered">
                                <div class="column"><p><strong>Secuencias correctas (Demo)</strong></p><p id="demo_secuences_num"></p></div>
                                <div class="column"><p><strong>Secuencias correctas (Juego)</strong></p><p id="game_secuences_num"></p></div>
                                <div class="column"><p><strong>Movimientos promedio (Demo)</strong></p><p id="demo_move_average"></p></div>
                                <div class="column"><p><strong>Movimientos promedio (Juego)</strong></p><p id="game_move_average"></p></div>
                            </div>
                            <div class="columns has-text-centered">
                                <div class="column"><p><strong>Secuencias largas Demo</strong></p><p id="large_secuences_num_demo"></p></div>
                                <div class="column"><p><strong>Secuencias largas Juego</strong></p><p id="large_secuences_num_game"></p></div>
                                <div class="column"><p><strong>Tiempo promedio por secuencia (Demo)</strong></p><p id="demo_time_average"></p></div>
                                <div class="column"><p><strong>Tiempo promedio por secuencia (Juego)</strong></p><p id="game_time_average"></p></div>
                            </div>
                        </div>
                    </article>
                </div>

                <div id="tab-resultados" class="report-tab-content print-wrap" style="display:none;">
                    <article class="message is-success mb-4">
                        <div class="message-header"><p>Comprensi&oacute;n de consigna / Uso de ayudas</p></div>
                        <div class="message-body">
                            <p class="mb-3">Analiza c&oacute;mo el candidato gestiona recursos disponibles (tiempo extra, mostrar camino) y la frecuencia de errores. Alto uso de ayudas puede indicar baja autonom&iacute;a bajo presi&oacute;n.</p>
                            <div style="height:200px;"><canvas id="myChart5"></canvas></div>
                            <div style="height:200px;" class="mt-3"><canvas id="myChart6"></canvas></div>
                            <div style="height:200px;" class="mt-3"><canvas id="myChart7"></canvas></div>
                            <div id="chart6" class="mt-2"></div>
                            <div id="chart7" class="mt-2"></div>
                            <div id="chart8" class="mt-2"></div>
                            <div class="columns mt-3 has-text-centered">
                                <div class="column"><p><strong>Ayudas de tiempo pedidas</strong></p><p id="time_help"></p></div>
                                <div class="column"><p><strong>Ayudas de ruta pedidas</strong></p><p id="route_help"></p></div>
                                <div class="column"><p><strong>Errores en Demo</strong></p><p id="demo_errors"></p></div>
                                <div class="column"><p><strong>Errores en Juego</strong></p><p id="game_errors"></p></div>
                            </div>
                        </div>
                    </article>
                    <article class="message is-dark mb-4">
                        <div class="message-header"><p>Persistencia / Tolerancia a la frustraci&oacute;n</p></div>
                        <div class="message-body">
                            <p class="mb-3">Mide la capacidad del candidato para recuperarse luego de errores y retomar la tarea. Un tiempo de reacci&oacute;n post-error bajo indica mayor resiliencia y adaptaci&oacute;n r&aacute;pida.</p>
                            <div style="height:200px;"><canvas id="myChart8"></canvas></div>
                            <div style="height:200px;" class="mt-3"><canvas id="myChart9"></canvas></div>
                            <div id="chart9" class="mt-2"></div>
                            <div id="chart10" class="mt-2"></div>
                            <div class="columns mt-3 has-text-centered">
                                <div class="column"><p><strong>Movimientos hasta 1er error (Demo)</strong></p><p id="demo_first_error_average"></p></div>
                                <div class="column"><p><strong>Movimientos hasta 1er error (Juego)</strong></p><p id="game_first_error_average"></p></div>
                                <div class="column"><p><strong>Tiempo tras 1er error (Demo)</strong></p><p id="demo_first_error_time"></p></div>
                                <div class="column"><p><strong>Tiempo tras 1er error (Juego)</strong></p><p id="game_first_error_time"></p></div>
                            </div>
                        </div>
                    </article>
                </div>

                <div id="tab-respuestas" class="report-tab-content print-wrap" style="display:none;">
                    <article class="message is-link mb-4">
                        <div class="message-header"><p>Conciencia de uno mismo / Agilidad para el cambio</p></div>
                        <div class="message-body"><div id="answers1"></div></div>
                    </article>
                    <article class="message is-link mb-4">
                        <div class="message-header"><p>Agilidad con las personas - Relaciones interpersonales</p></div>
                        <div class="message-body"><div id="answers2"></div></div>
                    </article>
                    <article class="message is-link mb-4">
                        <div class="message-header"><p>Agilidad con las personas - Resoluci&oacute;n de conflictos</p></div>
                        <div class="message-body"><div id="answers3"></div></div>
                    </article>
                    <article class="message is-link mb-4">
                        <div class="message-header"><p>Agilidad con las personas - Trabajo en equipo</p></div>
                        <div class="message-body"><div id="answers4"></div></div>
                    </article>
                    <article class="message is-link mb-4">
                        <div class="message-header"><p>Agilidad para el cambio - Adaptabilidad / Flexibilidad</p></div>
                        <div class="message-body"><div id="answers5"></div></div>
                    </article>
                    <article class="message is-success">
                        <div class="message-header"><p>Comentarios del Participante</p></div>
                        <div class="message-body"><div id="observations"></div></div>
                    </article>
                </div>
            </section>
            <footer class="modal-card-foot" style="justify-content:flex-end;">
                <button class="button" id="export_button">
                    <span class="icon"><i class="fas fa-file-export"></i></span>
                    <span>Exportar</span>
                </button>
                <button class="button" id="view_delete_dialog">Cerrar</button>
            </footer>
        </div>
    </div>

    <input id="option" value="<?php echo $option; ?>" style="display:none" />

    <div id="user_modal" class="modal">
        <div class="modal-background" id="user_modal_background"></div>
        <div class="modal-card" style="width: 55%;">
            <header class="modal-card-head">
                <p class="modal-card-title" id="edit_title">Nuevo Usuario</p>
                <button class="delete" id="close_user_modal" aria-label="close"></button>
            </header>
            <section class="modal-card-body">
                <div class="field">
                    <label class="label">Tipo de Usuario</label>
                    <div class="control">
                        <label class="radio"><input type="radio" name="type" value="0" checked> Individual</label>
                        <label class="radio"><input type="radio" name="type" value="1"> Liderazgo</label>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="name">Nombre</label>
                    <div class="control"><input class="input" type="text" name="name" id="name"></div>
                </div>
                <div class="field">
                    <label class="label" for="price">DNI</label>
                    <div class="control"><input class="input" type="number" name="price" id="price"></div>
                </div>
                <div class="field">
                    <label class="label" for="date_of_birth">Fecha de nacimiento</label>
                    <div class="control"><input class="input" type="date" name="date_of_birth" id="date_of_birth"></div>
                </div>
                <div class="field">
                    <label class="label" for="email">Correo Electr&oacute;nico</label>
                    <div class="control"><input class="input" type="email" name="email" id="email"></div>
                </div>
                <div class="field">
                    <label class="label" for="search_company">Empresa para la cual es la b&uacute;squeda</label>
                    <div class="control"><input class="input" type="text" name="search_company" id="search_company"></div>
                </div>
                <div class="field">
                    <label class="label" for="pos_to_apply">Posici&oacute;n a la que postula</label>
                    <div class="control"><input class="input" type="text" name="pos_to_apply" id="pos_to_apply"></div>
                </div>
                <div class="field">
                    <label class="label">Nivel de estudios alcanzado</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="studies">
                                <option>Secundario incompleto</option>
                                <option>Secundario completo</option>
                                <option>Secundario en curso</option>
                                <option>Terciario incompleto</option>
                                <option>Terciario completo</option>
                                <option>Terciario en curso</option>
                                <option>Universitario incompleto</option>
                                <option>Universitario completo</option>
                                <option>Universitario en curso</option>
                                <option>Estudios de postgrado incompleto</option>
                                <option>Estudios de postgrado completo</option>
                                <option>Estudios de postgrado en curso</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Selectora</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="selectora"></select>
                        </div>
                    </div>
                </div>
                <div class="field" id="in_charge">
                    <label class="label">&iquest;Ha tenido experiencia con gente a cargo?</label>
                    <div class="control">
                        <label class="radio"><input type="radio" name="in_charge" value="0" checked> No</label>
                        <label class="radio"><input type="radio" name="in_charge" value="1"> Si</label>
                    </div>
                </div>
            </section>
            <footer class="modal-card-foot">
                <input id="create_button" class="button is-primary" type="button" value="Crear">
                <input id="update_button" class="button is-warning is-hidden" type="button" value="Actualizar">
                <button class="button" id="cancel_user_modal">Cancelar</button>
            </footer>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
function makeBarChart(id, label, data, colors) {
    return new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels: data.map(function(d){ return d.label; }),
            datasets: [{ label: label, data: data.map(function(d){ return d.val; }), backgroundColor: colors }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
}
var myChart  = makeBarChart('myChart',  'Desempeño global',                    [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart2 = makeBarChart('myChart2', 'Secuencias correctas (Juego)',        [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart3 = makeBarChart('myChart3', 'Tiempo prom. secuencias (Juego)',     [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart4 = makeBarChart('myChart4', 'Secuencias largas (Juego)',           [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart5 = makeBarChart('myChart5', 'Ayudas de tiempo pedidas',            [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart6 = makeBarChart('myChart6', 'Ayudas de ruta pedidas',              [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart7 = makeBarChart('myChart7', 'Errores en el juego',                 [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart8 = makeBarChart('myChart8', 'Reinicios tras error (Juego)',        [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);
var myChart9 = makeBarChart('myChart9', 'Tiempo prom. tras 1er error (Juego)', [{label:'Mín',val:0},{label:'Actual',val:0},{label:'Máx',val:0}], ['#3273dc','#EB1C74','#23d160']);

function updateBarChart(chart, min, current, max) {
    chart.data.datasets[0].data = [min, current, max];
    chart.update();
}
</script>
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bulma.min.js') }}"></script>
    <script src="{{ asset('js/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('js/FileSaver.min.js') }}"></script>
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="{{ asset('js/users.js') }}"></script>
@endsection
