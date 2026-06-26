$(document).ready(function(){
    function secondsToString(seconds)
    {
        var numyears = Math.floor(seconds / 31536000);
        var numdays = Math.floor((seconds % 31536000) / 86400); 
        var numhours = Math.floor(((seconds % 31536000) % 86400) / 3600);
        var numminutes = Math.floor((((seconds % 31536000) % 86400) % 3600) / 60);
        var numseconds = ((((seconds % 31536000) % 86400) % 3600) % 60);
        if (String(numseconds).length > 2)
            numseconds = numseconds.toFixed(2)

        var s = ""
        if (numyears != 0)
            s += numyears + " a "

        if (numdays != 0)
            s += numdays + " d "

        if (numhours != 0)
            s += numhours + " h "

        if (numminutes != 0)
            s += numminutes + " min "

        s += numseconds + " seg "

        return s;    
    }


    function drawChart(id, title, overText, min, max, current, humanReadTime) 
    {
        let barBoxWidth = 450
        let centerBarSize = (barBoxWidth/100) * ((100/(max)) * (current))
        let fixed = 0;
        min = min.toFixed(2);
        max = max.toFixed(2);
        current = current.toFixed(2);
        if (max == 0)
            centerBarSize = 2;

        if (humanReadTime)
        {
            min = secondsToString(min)
            max = secondsToString(max)
            current = secondsToString(current)
        }
        
        let space = String(min).length * 8

        var canvas = document.createElement("canvas");
        var w = window;
        canvas.width = (w.innerWidth/100)*70;
        var ctx = canvas.getContext("2d");

        ctx.fillStyle = $(id).parent().parent().css("background-color");
        ctx.fillRect(0,0,(w.innerWidth/100)*70,(w.innerHeight/100)*70); 

        ctx.fillStyle = "#000";
        ctx.font = ctx.font.replace(/\d+px/, "18px");
        ctx.fillText(min, 0, 70); 
        ctx.fillText(max, space+512, 70); 
        ctx.fillText(current, space+centerBarSize+20+fixed, 25); 
        
        ctx.beginPath();
        ctx.rect(space+40, 45, barBoxWidth, 46);
        ctx.stroke();
        
        var grd = ctx.createLinearGradient(0, 0, centerBarSize, 0);
        grd.addColorStop(0, "#EB1972FF");
        grd.addColorStop(1, "#EB197280");
        ctx.fillStyle = grd;
        ctx.fillRect(space+42, 47, centerBarSize - 2, 42);
        
        ctx.fillStyle = "#ddd";
        ctx.beginPath();
        ctx.arc(space+centerBarSize+34+fixed, 68, 26, 0, 2 * Math.PI);
        ctx.fill();
        
        ctx.fillStyle = "#222";
        ctx.beginPath();
        ctx.arc(space+centerBarSize+34+fixed, 68, 26, 0, 2 * Math.PI);
        ctx.stroke();
        
        $(id).html(canvas)
    }

    

    let = selected = -1;
    let = uSelected = -1;
    let = vSelected = -1;
    let productsList = [];
    let last_registry;
    let metrics;

    let productTable = $('#product_table').DataTable({
        searching: true,
        pageLength: 5,
        columnDefs: [{ "width": "35%", "targets": 4 }],
        columns: [
            { title: "DNI" },
            { title: "Nombre" },
            { title: "Tipo" },
            { title: "Ha Jugado" },
            { title: "" },
        ],
        language: {
            emptyTable:   '<div class="has-text-centered py-4"><i class="fas fa-users fa-2x has-text-grey-light"></i><p class="has-text-grey mt-2">No hay usuarios registrados</p></div>',
            zeroRecords:  '<div class="has-text-centered py-4"><i class="fas fa-search fa-2x has-text-grey-light"></i><p class="has-text-grey mt-2">No se encontraron resultados</p></div>',
            info:          "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
            infoEmpty:     "Sin usuarios",
            lengthMenu:    "Mostrar _MENU_ registros",
            search:        "Buscar:",
            infoFiltered:  "(filtrado de _MAX_ registros en total)",
            paginate:      { previous: "Anterior", next: "Siguiente" },
        }
    });

    $('#product_table_wrapper .dataTables_filter').append(
        '<a class="button is-link save_user ml-3" title="Agregar usuario"><span class="icon"><i class="fas fa-plus"></i></span><span>Agregar Usuario</span></a>'
    );

    let notification = function(message, typeCode){
        $("#notification_container").hide();
        $("#notification_message").html("")
        switch (typeCode) 
        {
            case 1: $("#notification_bar").removeClass("is-danger")
                    $("#notification_bar").addClass("is-primary")
            break;                
            case 2: $("#notification_bar").removeClass("is-primary")
                    $("#notification_bar").addClass("is-danger")
        }
        $("#notification_container").toggle(600, function()
        {
            $("#notification_message").html(message)
        });
        $("#notification_container").fadeOut(10000);
    }

    let getMetrics = function()
    {
		$.ajax({
			url:'/api/workers/metrics',
			method:'GET',
			dataType:'json',
			success:function(result){
                metrics = result;
			},
			error:function(result)
            {
                notification("Error al obtener metricas", 2);
			}
            
		})
    }
    getMetrics();


    function calcularEdad(fecha) {
        // Si la fecha es correcta, calculamos la edad

        if (typeof fecha != "string" && fecha && esNumero(fecha.getTime())) {
            fecha = formatDate(fecha, "yyyy-MM-dd");
        }

        var values = fecha.split("-");
        var dia = values[2];
        var mes = values[1];
        var ano = values[0];

        // cogemos los valores actuales
        var fecha_hoy = new Date();
        var ahora_ano = fecha_hoy.getYear();
        var ahora_mes = fecha_hoy.getMonth() + 1;
        var ahora_dia = fecha_hoy.getDate();

        // realizamos el calculo
        var edad = (ahora_ano + 1900) - ano;
        if (ahora_mes < mes) {
            edad--;
        }
        if ((mes == ahora_mes) && (ahora_dia < dia)) {
            edad--;
        }
        if (edad > 1900) {
            edad -= 1900;
        }

        // calculamos los meses
        var meses = 0;

        if (ahora_mes > mes && dia > ahora_dia)
            meses = ahora_mes - mes - 1;
        else if (ahora_mes > mes)
            meses = ahora_mes - mes
        if (ahora_mes < mes && dia < ahora_dia)
            meses = 12 - (mes - ahora_mes);
        else if (ahora_mes < mes)
            meses = 12 - (mes - ahora_mes + 1);
        if (ahora_mes == mes && dia > ahora_dia)
            meses = 11;

        // calculamos los dias
        var dias = 0;
        if (ahora_dia > dia)
            dias = ahora_dia - dia;
        if (ahora_dia < dia) {
            ultimoDiaMes = new Date(ahora_ano, ahora_mes - 1, 0);
            dias = ultimoDiaMes.getDate() - (dia - ahora_dia);
        }

        return edad + " años";
    }

    let getAllProducts = function(){
        const option = $('#option').val();
        let url;
        if (option == 'all'){
            url = '/api/workers';
        } else if (option == 'usersnotfinished') {
            url = '/api/workers/usersnotfinished';
        } else if (option == 'usersfinished') {
            url = '/api/workers/usersfinished';
        } else {
            url = '/api/workers/usersnotplayed';
        }
        $('#table_spinner').show();
        $('#product_table_wrapper').hide();
		$.ajax({
			url:url,
			method:'GET',
			dataType:'json',
			success:function(result){
                $('#table_spinner').hide();
                $('#product_table_wrapper').show();
                types = ["Individual", "Liderazgo"]
                productsList = result['workers'];
                productTable.clear();
                let tableButtons = "";
                for(i=0; i <= result['workers'].length; i++){
                    let worker = result['workers'][i];
                    if (worker == null)
                        continue;

                    tableButtons = '<div style="display:flex;justify-content:flex-end;align-items:center;gap:4px;">';
                    if (worker.played)
                        tableButtons += '<a id="clean_'+i+'" title="Limpiar registros" class="clean_button my-1 button"><span class="icon"><i class="fas fa-eraser"></i></span></a>';
                    if (worker.tiene_registros)
                        tableButtons += '<a id="see_'+i+'" href="#edit" title="Ver reporte" class="view_dialog my-1 button is-link"><span class="icon"><i class="fas fa-eye"></i></span></a>';
                    tableButtons += '<a id="u_'+i+'" href="#edit" title="Editar" class="my-1 button is-warning update"><span class="icon"><i class="fas fa-pen"></i></span></a>';
                    tableButtons += '<a id="delete_'+i+'" title="Eliminar" class="delete_button my-1 button is-danger"><span class="icon"><i class="fas fa-trash"></i></span></a>';
                    tableButtons += '</div>';

                    productTable.row.add([worker.dni, worker.name, types[worker.type], worker.played ? "Si":"No", tableButtons]).draw();
                }
			},
			error:function(result){
                $('#table_spinner').hide();
                $('#product_table_wrapper').show();
                notification("Error al obtener usuarios", 2);
			}
		})
    }

    let selectoras;

    const getAllSelectoras = (worker) => {
        $('#selectora').empty();
        $.ajax({
			url: '/api/selectoras',
			method: 'GET',
			dataType: 'json',
			success: function(result){
                selectoras = result['selectoras'];
                for(i=0; i <= result['selectoras'].length; i++){
                    let selectora = result['selectoras'][i];
                    if (selectora == null)
                        continue;
                                        
                    let option = `<option value='${selectora.id}'${selectora.id == worker.id_selectoras ? 'selected' : ''}>${selectora.name}</option>`;
                    
                    $('#selectora').append(option);
                }
			},
			error:function(result){
                notification("Error al obtener usuarios", 2);
			}
            
		});
        $("#selectora").val('6')
        
    }

    
    let closeUserModal = function() {
        $("#user_modal").removeClass("is-active");
    };

    let createProduct = function(worker, func){
		$.ajax({
			url:'/api/workers',
			method:'POST',
            dataType:'json',
            data: {'worker':worker},
			success:function(result){
                notification(result["message"], 1);
                func();
                closeUserModal();
			},
			error:function(xhr){
                let msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : "Error al crear usuario";
                notification(msg, 2);
			},

		})

    };

    let updateProduct = function(worker, func){
        $.ajax({
            url:'/api/workers/'+worker.dni,
			method:'PUT',
            data: {'worker':worker},
            dataType:'json',
			success:function(result){
                notification(result["message"], 1)
                func();
                closeUserModal();
			},
			error:function(result){
				notification("Error al actualizar usuario", 2);
			}

		})
    };

    let deleteProduct = function(id, func){
        $.ajax({
            url:'/api/workers/'+productsList[id].dni,
			method:'DELETE',
            dataType:'json',
			success:function(result){
                notification(result["message"], 1)
                func();
			},
			error:function(result){
				notification("Error al eliminar usuario", 2);
			}
            
		})
    };
    let cleanRegisters = function(id, func){
        $.ajax({
            url:'/api/workers/'+productsList[id].dni+"/clean",
			method:'DELETE',
            dataType:'json',
			success:function(result){
                notification(result["message"], 1)
                func();
			},
			error:function(result){
				notification("Error al eliminar registros", 2);
			}
            
		})
    };




    getAllProducts();

    $('#in_charge').hide();
    $("#update_button").hide();
    $("#update_button").removeClass("is-hidden");

    $("#notification_container").hide();
    $("#notification_container").removeClass("is-hidden");
    
    $("#cancel_delete_dialog, #x_delete_dialog").click(function(){
        $("#delete_dialog").removeClass("is-active");
    })

    $("#cancel_clean_dialog, #x_clean_dialog").click(function(){
        $("#clean_dialog").removeClass("is-active");
    })
    $("#view_delete_dialog, #x_view_dialog, #close_view_dialog, #view_dialog_background").click(function(){
        $("#view_dialog").removeClass("is-active");
    });

    $(".report-tabs li").click(function(){
        $(".report-tabs li").removeClass("is-active");
        $(this).addClass("is-active");
        $(".report-tab-content").hide();
        $("#" + $(this).data("tab")).show();
        var tabCharts = {
            "tab-mental":    [myChart, myChart2, myChart3, myChart4],
            "tab-resultados":[myChart5, myChart6, myChart7, myChart8, myChart9]
        };
        var tab = $(this).data("tab");
        if (tabCharts[tab]) tabCharts[tab].forEach(function(c){ c.resize(); });
    });

    $("body").keydown(function(e)
    {
        if (e.keyCode == 27)
            $("#delete_dialog").removeClass("is-active");
    })

    $("#accept_delete_dialog").click(function(){
        $("#delete_dialog").removeClass("is-active") 
        deleteProduct(selected, getAllProducts)
    })

    $("#accept_clean_dialog").click(function(){
        $("#clean_dialog").removeClass("is-active") 
        cleanRegisters(selected, getAllProducts)
    })

    $("body").on("click", "a.update", function(event){
        uSelected = $(this).attr('id').split("_")[1];
        let p = productsList[uSelected];
        getAllSelectoras(p);
        $("#name").val(p.name);
        $("#price").val(p.dni);
        $("#studies").val(p.education_level);
        $("#email").val(p.email);
        $("#date_of_birth").val(p.date_of_birth);
        $("#pos_to_apply").val(p.pos_to_apply);
        $("#search_company").val(p.search_company);
        $("input[name='type'][value='"+p.type+"']").prop('checked', true);
        $("input[name='in_charge'][value='"+p.in_charge+"']").prop('checked', true);
        $("#edit_title").html("Actualizar ("+p.name+")");
        $("#create_button").hide();
        $("#update_button").show().removeClass("is-hidden");
        $("#user_modal").addClass("is-active");
    })

    $("body").on("click", "a.delete_button", function(event){
        selected = $(this).attr('id').split("_")[1];
        $("#delete_dialog").addClass("is-active") ;
        deleteProduct(selected, getAllProducts)
    })

    $("body").on("click", "a.clean_button", function(event){
        selected = $(this).attr('id').split("_")[1];
        $("#clean_dialog").addClass("is-active") ;
        cleanRegisters(selected, getAllProducts)
    })
    $("body").on("click", "a.view_dialog", function(event){
        const p_id = $(this).attr('id').split("_")[1];
        $("#view_dialog").addClass("is-active");
        $("#report_spinner").show();
        $("#export-data").hide();
        $('.report-tabs li').removeClass('is-active');
        $('.report-tabs li[data-tab="tab-resumen"]').addClass('is-active');
        $('.report-tab-content').hide();
        $('#tab-resumen').show();
        $.ajax({
			url:'/api/workers/'+productsList[p_id].dni+'/report',
			method:'GET',
			dataType:'json',
			success:function(result)
            {
                last_registry = result;
                let worker = productsList[p_id];
                $("#report_spinner").hide();
                $("#export-data").show();

                $(".worker_name").text(worker.name);
                $(".worker_company").text(worker.search_company);
                $(".worker_pos").text(worker.pos_to_apply);
                $(".worker_age").text(calcularEdad(worker.date_of_birth));


                $("#playedLevel").text( worker.played==1 ? "1 - Demo Completo" :
                                        worker.played==2 ? "2 - Juego Completo" :
                                        worker.played==3 ? "3 - Respuestas Completo" : "Sin completar");

                $("#total_time").text(secondsToString(result["total_time"]));
                $("#demo_move_average").text(result["demo_move_average"].toFixed(2));
                $("#game_move_average").text(result["game_move_average"].toFixed(2));
                $("#demo_time_average").text(secondsToString(result["demo_time_average"]));
                $("#game_time_average").text(secondsToString(result["game_time_average"]));
                $("#demo_secuences_num").text(result["demo_secuences_num"]);
                $("#demo_route").text(result["demo_secuences_num"]);
                $("#game_secuences_num").text(result["game_secuences_num"]);
                $("#game_route").text(result["game_secuences_num"]);
                $("#demo_first_error_average").text(result["demo_first_error_average"]);
                $("#game_first_error_average").text(result["game_first_error_average"]);
                $("#large_secuences_num_demo").text(result["large_secuences_num_demo"]);
                $("#large_secuences_num_game").text(result["large_secuences_num_game"]);
                $("#game_errors").text(result["game_errors"]);
                $("#demo_errors").text(result["demo_errors"]);
                $("#success_move").text(result["asserts"]);
                $("#game_time").text(secondsToString(result["game_time"]));
                $("#win").text("Porcentaje de recorrido: "+ productsList[p_id].win+ "%");
                $("#demo_time").text(secondsToString(result["demo_time"]));
                $("#init_time_demo").text(secondsToString(result["init_move_demo"]));
                $("#init_time_game").text(secondsToString(result["init_move_game"]));
                $("#question_time").text("Tiempo en preguntas: "+secondsToString(result["question_time"]));
                $("#demo_first_error_time").text(secondsToString(result["demo_first_error_time"]));
                $("#game_first_error_time").text(secondsToString(result["game_first_error_time"]));
                $("#time_help").text(result["time_help"]);
                $("#route_help").text(result["route_help"]);
                vSelected = p_id;

                var m = {};

                if (worker.type==0) //individual
                {
                    m["Ingresaste a un nuevo sector"] =  ["A) A quien, por lo que te cuenta, presum&iacute;s piensa muy parecido a vos entonces seguramente estar&aacute;n de acuerdo frente a las situaciones a resolver.", "B) A quien, por lo que te cuenta, tiene una mirada diferente a la tuya entonces crees que se complementar&iacute;an muy bien.", "C) Dependiendo del momento y la madurez en la que se encuentren el equipo y la organización elegir&iacute;a por uno o por otro."];
                    m["Cuando tu jefe/a te propone"] = ["A) Frecuentemente ten&eacute;s listadas aquellas cuestiones que te propon&eacute;s mejorar respecto a tu desempeño/gestión.", "B) Ten&eacute;s dificultad para identificar tus fortalezas y oportunidades de mejora en el rol.", "C) Ya ten&eacute;s identificadas acciones para potenciar tus fortalezas y desarrollas otras habilidades."];
                    m["Tuviste un problema con un compa"] = ["A) Involucr&aacute;s a tu jefe para ponerlo al tanto del tema y as&iacute; colabore en la resolución.", "B) Convoc&aacute;s a otro compañero para que te de su opinión sobre cómo encarar el di&aacute;logo.", "C) Planific&aacute;s el mensaje que quer&eacute;s transmitir y habl&aacute;s directamente con quien tuviste el problema."];
                    m["Tu jefe directo "] = ["A) Le ped&iacute;s a un compañero del sector que te ayude.", "B) Apel&aacute;s a tus conocimientos e ideas y lo arm&aacute;s solo/a.", "C) Te tom&aacute;s algunos d&iacute;as para compartir tus ideas con especialistas en el tema antes de presentar la propuesta."];
                    m["La compañía donde tra"] = ["A) Que es inviable: por el tipo de información y conversaciones que ten&eacute;s necesit&aacute;s indefectiblemente compartir el d&iacute;a a d&iacute;a con las personas de tu sector. ", "B) Desconfi&aacute;s de las ventajas de esta propuesta; sent&iacute;s que se ve afectado tu sentido de pertenencia por no contar con un puesto de trabajo propio. ", "C) Sab&eacute;s que va a ser un cambio que te va a costar y te requerir&aacute; re plantearte algunos h&aacute;bitos pero confi&aacute;s en que si se tomó esta decisión es porque es la mejor."];
                    m["Hace menos de 1 a"] = ["A) Acept&aacute;s la propuesta, si pensaron en vos es porque creen que est&aacute;s a la altura de lo que la posición requiere y adem&aacute;s es una gran oportunidad para ganar experiencia en una nueva &aacute;rea.", "B) Rechaz&aacute;s la propuesta. Prefer&iacute;s seguir creciendo en tu &aacute;rea de expertise m&aacute;s all&aacute; de que el cambio implique un crecimiento jer&aacute;rquico.", "C) Rechaz&aacute;s la propuesta por el momento ya que hace muy poco tiempo que est&aacute;s en tu puesto actual. Ped&iacute;s que te tengan en cuenta m&aacute;s adelante."];
                    m["Ingresaste hace 6 meses"] = ["A) Te parece una gran oportunidad, consider&aacute;s que la empresa valorar&aacute; tus ganas de formarte y tratar&aacute; de hacer todo lo que est&eacute; a su alcance para conseguirte una licencia mientras dure la especialización.", "B) Te parece inviable y no lo plante&aacute;s.", "C) Antes de pedir la licencia arm&aacute;s un esquema para plantear la posibilidad de seguir colaborando a distancia mientras est&aacute;s cursando afuera."];
                }
                else //liderazgo
                {
                    m["Tenés que seleccionar"] = ["A) Aquel que, por lo que te cuenta, presum&iacute;s piensa muy parecido a vos entonces seguramente no cuestione tus decisiones haciendo que sea m&aacute;s simple ejecutarlas.", "B) Aquel que, por lo que te cuenta, tiene una mirada diferente a la tuya entonces crees que se complementarían muy bien potenciando al equipo.", "C) Dependiendo del momento y la madurez en la que se encuentren el equipo y la organizaci&oacute;n elegir&iacute;as por uno o por otro."]
                    m["El gerente general"] = ["A) Les env&iacute;as un mail con la informaci&oacute;n", "B) Los convoc&aacute;s a una reuni&oacute;n y lo comunic&aacute;s personalmente", "C) No comunic&aacute;s nada y esper&aacute;s que se enteren solos, el radiopasillo siempre es m&aacute;s r&aacute;pido"];
                    m["Un reporte habla con vos"] = ["A) Convoc&aacute;s a la otra persona involucrada y ten&eacute;s una reuni&oacute;n con ambos para aclarar el tema", "B) Le suger&iacute;s a tu reporte que primero hable con su compañero/a de equipo", "C) Convoc&aacute;s a la otra persona involucrada en el conflicto a una reuni&oacute;n con vos y lo culp&aacute;s por lo que ha ocurrido"];
                    m["Tu jefe directo"] = ["A) Le transfer&iacute;s el pedido al jefe especialista en el tema para que idee una nueva estrategia con su equipo y te la presente", "B) Convoc&aacute;s al jefe especialista en el tema, para idear en conjunto la nueva estrategia", "C) Te qued&aacute;s hasta despu&eacute;s de hora solo pensando la nueva estrategia para presentarla al d&iacute;a siguiente a tu jefe"];
                    m["Te pidieron revisar"] = ["A) Relevar&iacute;as con cada integrante del equipo los impactos de la nueva modalidad para levantar propuestas de mejora", "B) Armar&iacute;as la propuesta en funci&oacute;n del desempeño que tuvo cada uno ", "C) Responder&iacute;as que es un tema que debe relevar y del cual debe ocuparse Recursos Humanos"];
                    m["Hace menos de"] = ["A) Acept&aacute;s la propuesta, si pensaron en vos es porque creen que est&aacute;s a la altura de lo que la posici&oacute;n requiere y adem&aacute;s es una gran oportunidad para ganar experiencia en una nueva &aacute;rea", "B) Rechaz&aacute;s la propuesta. Prefer&iacute;s seguir creciendo en tu &aacute;rea de expertise m&aacute;s all&aacute; de que el cambio implique un crecimiento jer&aacute;rquico", "C) Rechaz&aacute;s la propuesta por el momento ya que hace muy poco tiempo que est&aacute;s en tu puesto actual. Ped&iacute;s que te tengan en cuenta m&aacute;s adelante"];
                    m["Hace un"] = ["A) Te parece una gran idea, valor&aacute;s sus ganas de formarse, y tratar&aacute;s de hacer todo lo que est&eacute; a tu alcance para conseguirle la licencia.", "B) Te parece inviable y le dec&iacute;s que no.", "C) Acept&aacute;s la idea siempre que pueda, aunque sea, trabajar algunas horas part time a distancia."];
                }

                //Limpio
                $("#answers1").empty();
                $("#answers2").empty();
                $("#answers3").empty();
                $("#answers4").empty();
                $("#answers5").empty();

                var answerTag;    //2022 Feb
                let total_points = 0; 
                for (let a of result["answers"])
                {
                    total_points += a.points;

                    answerTag = a.question.startsWith("Ingresaste a") || a.question.startsWith("Tenés que seleccionar") ?   "#answers1" :
                                a.question.startsWith("Cuando tu") || a.question.startsWith("El gerente general") ?         "#answers2" :
                                a.question.startsWith("Tuviste un") || a.question.startsWith("Un reporte habla con vos") ?  "#answers3" :
                                a.question.startsWith("Tu jefe") || a.question.startsWith("Tu jefe") ?                      "#answers4" :
                                                                                                                            "#answers5";


                    $(answerTag).append("<p><strong>" + a.question.replace("á", "&aacute;").replace("é", "&eacute;").replace("í", "&iacute;").replace("ó", "&oacute;").replace("ú", "&uacute;") + "</strong></p>");
                    $(answerTag).append("<br>");

                    for (let keyAtt in m) 
                        if (a.question.startsWith(keyAtt))
                        {
                            for (let r of m[keyAtt])
                                $(answerTag).append("<p" + (a.answer.substring(0, 2)==r.substring(0, 2) ? ">" : " style='color:#CACACA;'>") + "&nbsp;&nbsp;&nbsp;&nbsp;"+r+"</p>");

                            break;
                        }
                    
                    if (answerTag=="#answers5")
                        $(answerTag).append("<br>");
                }
                
                //Otras yerbas
                $("#answers1").append("<br>");
                $("#answers1").append("<p><strong>¿Qu&eacute; otro recurso o ayuda te hubiera resultado útil?</strong></p>");
                $("#answers1").append("<br>");
                $("#answers1").append("<p>&nbsp;&nbsp;&nbsp;&nbsp;"+worker.question1+"</p>");

                $("#answers1").append("<br>");
                $("#answers1").append("<p><strong>¿Cambiaste algo en tu estrategia en los diferentes intentos de resolución?</strong></p>");
                $("#answers1").append("<br>");
                $("#answers1").append("<p>&nbsp;&nbsp;&nbsp;&nbsp;"+worker.question2+"</p>");

                $("#answers1").append("<br>");
                $("#answers1").append("<p><strong>¿Si tuvieras que volver a realizarlo, har&iacute;as algo diferente?</strong></p>");
                $("#answers1").append("<br>");
                $("#answers1").append("<p>&nbsp;&nbsp;&nbsp;&nbsp;"+worker.question3+"</p>");

                $("#observations").empty();
                $("#observations").append("<p>&nbsp;&nbsp;&nbsp;&nbsp;"+worker.observation+"</p>");

                function mVal(key, idx) { return (metrics && metrics[key]) ? metrics[key][idx] : 0; }
                function mMax(key, val) { return Math.max(mVal(key, 1), val || 0); }

                drawChart("#chart1","Tiempo total en que se realizó el juego: ", "", mVal('total_time',0), mMax('total_time', result["total_time"]), result["total_time"], true);
                drawChart("#chart2","Tiempo de planificación / acción el juego: ", "", mVal('init_move_game',0), mMax('init_move_game', result["init_move_game"]), result["init_move_game"], true);

                drawChart("#chart3","Cantidad de secuencias correctas en el juego: ", "", mVal('game_secuences_num',0), mMax('game_secuences_num', result["game_secuences_num"]), result["game_secuences_num"], false);
                drawChart("#chart4","Tiempo promedio de la cantidad de secuencias correctas en el juego: ", "", mVal('game_time_average',0), mMax('game_time_average', result["game_time_average"]), result["game_time_average"], true);
                drawChart("#chart5","Cantidad de secuencias de más de 5 pasos en el juego: ", "", mVal('large_secuences_num_game',0), mMax('large_secuences_num_game', result["large_secuences_num_game"]), result["large_secuences_num_game"], false);

                drawChart("#chart6","Cantidad de veces que el candidato seleccionó más tiempo: ", "", mVal('time_help',0), mMax('time_help', result["time_help"]), result["time_help"], false);
                drawChart("#chart7","Cantidad de veces que el candidato seleccionó mostrar el camino: ", "", mVal('route_help',0), mMax('route_help', result["route_help"]), result["route_help"], false);
                drawChart("#chart8","Cantidad de errores en el juego: ", "", mVal('game_errors',0), mMax('game_errors', result["game_errors"]), result["game_errors"], false);

                drawChart("#chart9","Cantidad de veces que emprendió el camino nuevamente en el juego: ", "", mVal('game_secuences_num',0), mMax('game_secuences_num', result["game_secuences_num"]), result["game_secuences_num"], false);
                drawChart("#chart10","Tiempo promedio de movimientos después el primer error en el juego: ", "", mVal('game_first_error_time',0), mMax('game_first_error_time', result["game_first_error_time"]), result["game_first_error_time"], true);

                updateBarChart(myChart,  mVal('total_time',0),                result["total_time"],              mMax('total_time', result["total_time"]));
                updateBarChart(myChart2, mVal('game_secuences_num',0),        result["game_secuences_num"],      mMax('game_secuences_num', result["game_secuences_num"]));
                updateBarChart(myChart3, mVal('game_time_average',0),         result["game_time_average"],       mMax('game_time_average', result["game_time_average"]));
                updateBarChart(myChart4, mVal('large_secuences_num_game',0),  result["large_secuences_num_game"],mMax('large_secuences_num_game', result["large_secuences_num_game"]));
                updateBarChart(myChart5, mVal('time_help',0),                 result["time_help"],               mMax('time_help', result["time_help"]));
                updateBarChart(myChart6, mVal('route_help',0),                result["route_help"],              mMax('route_help', result["route_help"]));
                updateBarChart(myChart7, mVal('game_errors',0),               result["game_errors"],             mMax('game_errors', result["game_errors"]));
                updateBarChart(myChart8, mVal('game_secuences_num',0),        result["game_secuences_num"],      mMax('game_secuences_num', result["game_secuences_num"]));
                updateBarChart(myChart9, mVal('game_first_error_time',0),     result["game_first_error_time"],   mMax('game_first_error_time', result["game_first_error_time"]));
            },
            error:function(xhr){
				$("#report_spinner").hide();
				let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "HTTP " + xhr.status;
				notification("Error al obtener reporte: " + msg, 2);
				$("#view_dialog").removeClass("is-active");
			}
        })
    })

    function createWorker () {
        return {
            name: $("#name").val(), 
            dni: $("#price").val(), 
            type: $("input[name='type']:checked").val(), 
            education_level: $("#studies").val(), 
            email: $("#email").val(), 
            date_of_birth: $("#date_of_birth").val(), 
            in_charge: $("input[name='in_charge']:checked").val(), 
            pos_to_apply: $("#pos_to_apply").val(), 
            search_company: $("#search_company").val(),
            id_selectoras: $('#selectora').val()
        }
    }


    $("#create_button").click(function(){
        const WORKER = createWorker();
        createProduct(WORKER, getAllProducts);
    }
    )
    $("#update_button").click(function(){        
        const WORKER = createWorker();
        updateProduct(WORKER, getAllProducts);
    }
    )

    $("body").on("click", "a.save_user", function(){
        $("#edit_title").html("Nuevo Usuario");
        $("#name, #price, #email, #search_company, #pos_to_apply").val('');
        $("#date_of_birth").val('');
        $("input[name='type'][value='0']").prop('checked', true);
        $("input[name='in_charge'][value='0']").prop('checked', true);
        getAllSelectoras({});
        $("#update_button").hide();
        $("#create_button").show();
        $("#user_modal").addClass("is-active");
    });

    $("#close_user_modal, #cancel_user_modal, #user_modal_background").click(function(){
        closeUserModal();
    });

    $("body").keydown(function(e){
        if (e.keyCode == 27) closeUserModal();
    });

    $("#x_navegation").click(function(){
        $("#notification_container").hide()
    }

    )

    $("#price,#age").on("input",function(){
        let value = $("#price").val();
        value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        $("#price").val(value);

    })

    $(document).ready(function() {
      $('input[type=radio][name="type"]').on('change', function() {
        if (this.value == 1){
            $('#in_charge').show(); 
        }
        else{
            $('#in_charge').hide();   
        }
      });
    });


    var pdf,page_section,HTML_Width,HTML_Height,top_left_margin,PDF_Width,PDF_Height,canvas_image_width,canvas_image_height;
	
	function calculatePDF_height_width(selector,index){
		page_section = $(selector).eq(index);
		//HTML_Width = page_section.width();
		//HTML_Height = page_section.height();
        HTML_Width = 1214;
        HTML_Height = 1726;
		top_left_margin = 15;
		PDF_Width = HTML_Width + (top_left_margin * 2);
		PDF_Height = (PDF_Width * 1.2) + (top_left_margin * 2) + 300;
		canvas_image_width = HTML_Width;
		canvas_image_height = HTML_Height;
	}


    $("#export_button").on('click', function(){
        let worker = productsList[vSelected];
        let wraps = $(".print-wrap");
        let pdf;
        let currentVisible = wraps.filter(":visible");

        function captureTab(index) {
            wraps.hide();
            $(wraps[index]).show();
            return html2canvas(wraps[index], { allowTaint: true, useCORS: true }).then(function(canvas) {
                calculatePDF_height_width(".print-wrap", index === 0 ? 0 : index - 1);
                var imgData = canvas.toDataURL("image/png", 1.0);
                if (index === 0) {
                    pdf = new jspdf.jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
                    pdf.addImage(imgData, 'PNG', top_left_margin, top_left_margin, HTML_Width, HTML_Height);
                } else {
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', top_left_margin, top_left_margin, HTML_Width, HTML_Height);
                }
            });
        }

        captureTab(0)
            .then(function(){ return captureTab(1); })
            .then(function(){ return captureTab(2); })
            .then(function(){ return captureTab(3); })
            .then(function(){
                wraps.hide();
                currentVisible.show();
                pdf.save('reporte_' + worker.dni + '.pdf');
            });
    });
})
