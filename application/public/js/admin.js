$(document).ready(async function(){	
    $('.box-data').mouseenter((event) => {
        const key = returnKey(event.currentTarget.id);
        $('#' + key).css('visibility','hidden');
        $('#' + key).css('position','absolute');

        $('#listar-' + key).css('visibility','visible');
        $('#listar-' + key).css('position','static');
    });

    $('.box-data').mouseleave((event) => {
        const key = returnKey(event.currentTarget.id);
        $('#' + key).css('visibility','visible');
        $('#' + key).css('position','static');

        $('#listar-' + key).css('visibility','hidden');
        $('#listar-' + key).css('position','absolute');
    });

    function returnKey(eventId) {
        switch (eventId) {
            case 'box-completaron-todo':
                return 'completaron-todo';
            case 'box-no-completaron-todo':
                return 'no-completaron-todo';
            case 'box-no-jugaron':
                return 'no-jugaron';
            default:
                return 'usuarios';
        }
    }

    function loadDataGame(){
        $.ajax({
			url:'/api/workers/gamedata',
			method:'GET',
            dataType:'json',
			success:function(result){
                $('#total-usuarios').text(result.data.totalUsuarios);
                $('#usuarios-jugaron').text(result.data.usuariosJugaron);
                $('#usuarios-no-terminaron').text(result.data.usuariosNoTerminaron);
                $('#usuarios-no-jugaron').text(result.data.usuariosNoJugaron);
                //$('#demo').text(result.data.demo);
                //$('#nivel-inicial').text(result.data.nivelInicial);
                //$('#ruleta').text(result.data.ruleta);
			},
			error:function(result){
                notification("Hubo un error al cargar los datos del juego", 2);
			},
		})
    }

    loadDataGame();


})
