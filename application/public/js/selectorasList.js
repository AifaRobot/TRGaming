$(document).ready(function(){
    const ENDPOINT = '/api/selectoras';
    
    let selectoraSelected = {};
    let = selected = -1;
    let = uSelected = -1;
    let = vSelected = -1;
    let selectoras = [];
    
    function Selectora(id, name, email) {
        this.id = id,
        this.name = name,
        this.email = email
    }

    let productTable = $('#product_table').DataTable({
        searching: true,
        pageLength: 5,
        columnDefs: [{ "width": "35%", "targets": 3 }],
        columns: [
            { title: "ID" },
            { title: "Nombre" },
            { title: "Correo Electronico" },
            { title: "" },
        ],
        language: {
            emptyTable:  '<div class="has-text-centered py-4"><i class="fas fa-address-book fa-2x has-text-grey-light"></i><p class="has-text-grey mt-2">No hay selectoras registradas</p></div>',
            zeroRecords: '<div class="has-text-centered py-4"><i class="fas fa-search fa-2x has-text-grey-light"></i><p class="has-text-grey mt-2">No se encontraron resultados</p></div>',
            info:         "Mostrando _START_ a _END_ de _TOTAL_ selectoras",
            infoEmpty:    "Sin selectoras",
            lengthMenu:   "Mostrar _MENU_ registros",
            search:       "Buscar:",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
            paginate:     { previous: "Anterior", next: "Siguiente" },
        }
    });

    $('#product_table_wrapper .dataTables_filter').append(
        '<a class="button is-link save ml-3" title="Agregar selectora"><span class="icon"><i class="fas fa-plus"></i></span><span>Agregar Selectora</span></a>'
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

    let getAllProducts = function(){
        $('#table_spinner').show();
        $('#product_table_wrapper').hide();
		$.ajax({
			url: ENDPOINT,
			method: 'GET',
			dataType: 'json',
			success: function(result){
                $('#table_spinner').hide();
                $('#product_table_wrapper').show();
                selectoras = result['selectoras'];
                productTable.clear();
                let tableButtons = "";
                for(i=0; i <= result['selectoras'].length; i++){
                    let selectora = result['selectoras'][i];
                    if (selectora == null)
                        continue;

                    tableButtons = '<div style="display:flex;justify-content:flex-end;align-items:center;gap:4px;">';
                    tableButtons += '<a id="u_'+i+'" href="#edit" title="Editar" class="my-1 button is-warning update"><span class="icon"><i class="fas fa-pen"></i></span></a>';
                    tableButtons += '<a id="delete_'+i+'" title="Eliminar" class="delete_button my-1 button is-danger"><span class="icon"><i class="fas fa-trash"></i></span></a>';
                    tableButtons += '</div>';

                    productTable.row.add([selectora.id, selectora.name, selectora.email, tableButtons]).draw();
                }
			},
			error:function(result){
                $('#table_spinner').hide();
                $('#product_table_wrapper').show();
                notification("Error al obtener usuarios", 2);
			}
            
		})
		
    }
    
    let closeModal = function(){
        $("#edit_modal").removeClass("is-active");
    };

    let createSelectora = function(selectora, func){
		$.ajax({
			url: ENDPOINT,
			method:'POST',
            dataType:'json',
            data: {'selectora': selectora},
			success:function(result){
                notification(result["message"], 1)
                func();
                closeModal();
			},
			error:function(result){
                notification("Error al crear usuario", 2);
			},

		})

    };

    let updateSelectora = function(selectora, func){
        $.ajax({
            url: `${ENDPOINT}/${selectora.id}`,
			method: 'PUT',
            data: {'selectora': selectora},
            dataType:'json',
			success:function(result){
                notification(result["message"], 1)
                func();
                closeModal();
			},
			error:function(result){
				notification("Error al actualizar usuario", 2);
			}
		})
    };
    let deleteSelectora = function(selectora, func){
        $.ajax({
            url: `${ENDPOINT}/${selectora.id}`,
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
    $("#view_delete_dialog, #x_view_dialog").click(function(){
        $("#view_dialog").removeClass("is-active");
    })
    $("body").keydown(function(e)
    {
        if (e.keyCode == 27)
            $("#delete_dialog").removeClass("is-active");
    })

    $("#accept_delete_dialog").click(function(){
        $("#delete_dialog").removeClass("is-active") 
        deleteSelectora(selected, getAllProducts)
    })

    $("#accept_clean_dialog").click(function(){
        $("#clean_dialog").removeClass("is-active") 
        cleanRegisters(selected, getAllProducts)
    })

    $("body").on("click", "a.update", function(event){
        $("#edit_title").show();
        $("#create_title").hide();
        uSelected = $(this).attr('id').split("_")[1];
        let p = selectoras[uSelected];
        selectoraSelected = {
            id: p.id,
            name: p.name,
            email: p.email
        }
        $("#name").val(p.name);
        $("#email").val(p.email);
        $("#create_button").hide();
        $("#update_button").show();
        $("#edit_modal").addClass("is-active");
    })

    $("body").on("click", "a.delete_button", function(event){
        selected = $(this).attr('id').split("_")[1];
        let p = selectoras[selected];
        selectoraSelected = {
            id: p.id,
            name: p.name,
            email: p.email
        }
        $("#delete_dialog").addClass("is-active") ;
        deleteSelectora(selectoraSelected, getAllProducts)
    });

    $("body").on("click", "a.save", function(event){
        $("#edit_title").hide();
        $("#create_title").show();
        $("#name").val('');
        $("#email").val('');
        $("#update_button").hide();
        $("#create_button").show();
        $("#edit_modal").addClass("is-active");
    })

    $("#create_button").click(function(){
        const SELECTORA = new Selectora('' ,$("#name").val(), $("#email").val());
        createSelectora(SELECTORA, getAllProducts);
    })

    $("#update_button").click(function(){
        let p = new Selectora(selectoraSelected.id ,$("#name").val(), $("#email").val());
        updateSelectora(p, getAllProducts);
    })

    $("#close_modal, #cancel_modal, #modal_background").click(function(){
        closeModal();
    });

    $("body").keydown(function(e){
        if (e.keyCode == 27) closeModal();
    });

    $("#x_navegation").click(function(){
        $("#notification_container").hide()
    })

})
