$(document).ready(function(){	

    let = selected = -1;
    let = uSelected = -1;
    let = vSelected = -1;

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

    const getAllSelectoras = () => {
        $.ajax({
			url: '/api/selectoras',
			method: 'GET',
			dataType: 'json',
			success: function(result){
                selectoras = result['selectoras'];
                console.log(selectoras);
                let tableButtons = "";
                for(i=0; i <= result['selectoras'].length; i++){
                    
                    let selectora = result['selectoras'][i];
                    
                    if (selectora == null)
                        continue;
                    
                    let option = `<option value='${selectora.id}'>${selectora.name}</option>`;
                    console.log(selectora.name);

                    
                    tableButtons += '</div>';

                    $('#selectora').append(option);
                }
			},
			error:function(result){
                notification("Error al obtener usuarios", 2);
			}
            
		})
    }

    getAllSelectoras();
    
    
    let createWorker = function(worker){
        console.log(worker);
		$.ajax({
			url:'/api/workers',
			method:'POST',
            dataType:'json',
            data: {'worker':worker},
			success:function(result){
                notification(result["message"], 1)
			},
			error:function(result){
                notification("Error al crear usuario", 2);
			},
            
		})
		
    };    


    $('#in_charge').hide();
 
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
        deleteProduct(selected)
    })

    $("#accept_clean_dialog").click(function(){
        $("#clean_dialog").removeClass("is-active") 
        cleanRegisters(selected)
    })

    $("#new_product").click(function(){
        $("#edit_title").html("Crear");
        $("#update_button").hide();
        $("#create_button").fadeIn(300);
    })


    $("#create_button").click(function(){
        const WORKER = {
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
        createWorker(WORKER);
    })
    

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

})
