extends Control
export (Events.menu_name) var menu_name


func _ready():
	pass


func _on_Enter_pressed():
	
	$background/TextureRect/Enter.disabled = true
#    Events.add_registry(Events.events_names.BUTTON_CLICK, menu_name)
	var dni = $background/TextureRect/LineEdit.text
	Events.get_user_by_id(dni)

	yield(Events,"request_completed")
	if Events.parsed_result == null:
		Global.show_message("Ups. Parece que hubo un error vuelve a intentarlo")
	else:
		if not Events.parsed_result.worker:
			Global.show_message("No es un DNI registrado")
		else:   
			if Events.parsed_result.worker.played:
				Global.show_message("Ya ha jugado")
			else:
#                if $Formulario/Nombre.text != "":
				if Events.parsed_result.worker.type == 0:
					Events.player_type = "individual"
				else:
					Events.player_type = "liderazgo"
				$background.hide()
				$background2.show()
				
				$background2/banner/Label.text = """HOLA %s EL JUEGO CONSTARÁ DE DOS INSTANCIAS:
	
	
	.LA ESQUIADORA
	.LA RUEDA MÁGICA
	
ANTES DE INICIAR CADA JUEGO TE DAREMOS LAS INSTRUCCIONES SOBRE CÓMO AVANZAR EN ELLOS


¡¡ADELANTE!!
""" % Events.parsed_result.worker.name.to_upper()
				
#                yield(Global,"ok_message")
#                get_tree().change_scene("res://scenes/intro/Intro.tscn")

	Events.player_dni = dni
	$background/TextureRect/Enter.disabled = false
	


func _on_Next_pressed():
	get_tree().change_scene("res://scenes/intro/Intro.tscn")
