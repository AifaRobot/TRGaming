extends Node
signal finish_tutorial()

export (PackedScene) var next_scene
export (Events.menu_name) var menu_name
export (bool) var extra_questions
export (Vector2) var grid_size = Vector2(14, 10)
export (int) var path_size = 20
export (String) var win_message = ""
export (String) var lost_message = ""
export (bool) var tutorial
var currend_player_coords:Vector2
var path:PoolVector2Array
onready var grid_map = $background/grid/gridMap
onready var cottage = $background/grid/cottage
onready var player = $background/grid/player
var currend_player_pos = 0
var more_far_player_pos = 0
var moving_direction = 1
var tutorial_error_ready = false
var tutorial_move_count = 0
var time_button_count = 0
var current_flag = 0
var initial_position:Vector2
var attemptsCount = 0

onready var arrow = preload("res://scenes/arrow.tscn")
onready var arrows_node =$background/grid/arrows

func _ready():
	
	Global.playedLevel+=1
	print("Game Ready At Level: ", Global.playedLevel, " name: ", menu_name)
	
	Global.snow_visible(true)
	Events.add_registry(Events.events_names.INIT, menu_name)
	generate_path()
	cottage.global_position = grid_map.to_global(grid_map.map_to_world(path[path.size()-1]))
	player.global_position = grid_map.to_global(grid_map.map_to_world(path[0]))
#    show_path()

	if tutorial:
		show_tutorial()
   

func show_tutorial():
	set_physics_process(false)
	Global.show_message("%s, ya nos encontramos en la demo.\n A continuación te vamos a detallar las reglas del juego."%Events.parsed_result.worker.name) 
	yield(Global,"ok_message")
	Global.show_arrow_message("Este cronómetro señala el tiempo en que debés completar el juego. Como estamos en un tutorial no debés preocuparte por él.", -90, Global.point_massage_postion.center) 
	yield(Global,"ok_message")
	Global.show_arrow_message("Tu objetivo es llevar a la esquiadora al punto de llegada.\n Lo podrás hacer con las flechas de tu teclado.", -345, Global.point_massage_postion.topleft)
	yield(Global,"ok_message")
	Global.show_arrow_message("Recordá que sólo hay un camino y es lo que debés descubrir.\n Si no es el camino correcto, volverás al punto de partida.", -345, Global.point_massage_postion.topleft)
	yield(Global,"ok_message")
	if OS.has_touchscreen_ui_hint():
		Global.show_arrow_message("Vamos!, ahora a probar los controles", -345, Global.point_massage_postion.topleft)
		yield(Global,"ok_message")        
		Global.show_swipe_instruction()       
	
	else:
		Global.show_arrow_message("¡Vamos! Ahora, a probar los controles. Podés moverte con las flechas de dirección de tu teclado", -345, Global.point_massage_postion.topleft)
		yield(Global,"ok_message")
	
	set_physics_process(true)
	yield(self,"finish_tutorial")
	set_physics_process(false)
	Global.show_arrow_message("La idea es que llegues a la bandera por tus propios medios. Podés utilizar este botón de ayuda como recurso donde encontrarás dos opciones.", -15, Global.point_massage_postion.top)
	yield(Global,"ok_message")
	Global.show_arrow_message("La primera ayuda te mostrará el camino que ya llevás recorrido y la segunda ayuda añade un 1 minuto más al tiempo disponible.", -15, Global.point_massage_postion.top)
	yield(Global,"ok_message")
	Global.show_message("¡Adelante!")
	set_physics_process(true)

func _physics_process(delta):
	if player.is_moving():
		return
	current_flag = 0
	var key_move = Vector2()
	key_move.x = int(Input.is_action_just_pressed("ui_right")) - int(Input.is_action_just_pressed("ui_left"))
	key_move.y = int(Input.is_action_just_pressed("ui_down")) - int(Input.is_action_just_pressed("ui_up"))
	if currend_player_pos > more_far_player_pos:
		more_far_player_pos = currend_player_pos
	
	if currend_player_pos ==  path_size-1:
		win()
		set_physics_process(false)
		return
		
	if key_move.x:
		key_move.y = 0
	
	if key_move:           
		if tutorial:
			tutorial_move_count += 1
			if tutorial_move_count >= 30:
				emit_signal("finish_tutorial")
#                tutorial = false
				
		player.move(key_move)
		var current_pos = path[currend_player_pos]
		var next_pos = path[currend_player_pos+moving_direction]
		current_pos += key_move
		current_flag += Global.flag_values.going if moving_direction == 1 else Global.flag_values.going_back
		if current_pos == next_pos:
			player.global_position = grid_map.to_global(grid_map.map_to_world(next_pos))
			currend_player_pos += moving_direction
			
		else:
			player.fail(key_move)
			player.global_position = grid_map.to_global(grid_map.map_to_world(path[0]))
			#moving_direction = 0
			currend_player_pos = 0
			Global.show_message("Ups. Fallaste. Vuelve al inicio")
			#here play error animation
			current_flag += Global.flag_values.error 
			if currend_player_pos != 0 and moving_direction != -1:
				if path[currend_player_pos-1] != current_pos:
					#moving_direction = -1
					if tutorial and not tutorial_error_ready and currend_player_pos > 0:
						Global.show_message("Mientras avanzás si te equivocás en el camino, deberás volver sobre tus pasos desandando exactamente el mismo camino.")
						tutorial_error_ready = true
				
					
			
		if currend_player_pos == 0:
			moving_direction = 1
			
		match key_move:
			Vector2(1, 0):
				Events.add_registry(Events.events_names.MOVE_RIGHT, menu_name, current_flag)
						
			Vector2(-1, 0):
				Events.add_registry(Events.events_names.MOVE_LEFT, menu_name, current_flag)
					
			Vector2(0, -1):
				Events.add_registry(Events.events_names.MOVE_UP, menu_name, current_flag) 
					
			Vector2(0, 1):
				Events.add_registry(Events.events_names.MOVE_DOWN, menu_name, current_flag)     
		
	if tutorial:        
		view_path_permanent(false)

func show_path():
	for p in path:
#        yield(get_tree().create_timer(0.5),"timeout")
		grid_map.set_cellv(p, 0)
		
func hide_path():
	for p in path:
#        yield(get_tree().create_timer(0.5),"timeout")
		grid_map.set_cellv(p, -1)
	
	
func generate_path():
	path = PoolVector2Array()
	randomize() 
	path.append(Vector2(randi()%int(grid_size.x),randi()%int(grid_size.y)))
	var coords = ["x", "y"]
	var pos = [-1, 1]
	
	var rand_coord = ""
	var rand_pos = 1
	var last_pos = Vector2()
	var new_pos = Vector2()
	var retries = 0
	
	while path.size() < path_size:
		randomize()
		rand_coord = coords[randi()%coords.size()]
		rand_pos = pos[randi()%pos.size()]
		last_pos = path[path.size()-1]
		new_pos = last_pos
		new_pos[rand_coord] += rand_pos
		if new_pos[rand_coord] < 0 or new_pos[rand_coord] > grid_size[rand_coord] or new_pos in path:
			retries += 1
			if retries >= 14:
				generate_path()
				return          
			continue
		retries = 0
		path.append(new_pos)
#        grid_map.set_cellv(new_pos, 0)
#        yield(get_tree().create_timer(0.2),"timeout")
		
#    print("fin")
#    hide_path()
#    generate_path()
	
func win():

	Events.player_won = 100		
	print("Game win at level: ", Global.playedLevel, " name: ", menu_name)
	Global.send_info()

	#Global.show_message(win_message)
	$background/Clock.set_paused(true)
	yield(Global,"ok_message")
	if extra_questions:
		$book/Tween.interpolate_property($book, "global_position", $book.global_position, Vector2(515, 238), 1,Tween.TRANS_LINEAR,Tween.EASE_IN_OUT)
		$book/Tween.start()
		return
#        yield($book/Next,"pressed")
	get_tree().change_scene_to(next_scene)
	
func lost():

	Events.player_won = (100/(path_size-1))*currend_player_pos	
	print("Game lost at level: ", Global.playedLevel, " name: ", menu_name)
	Global.send_info()		

	Global.show_message(lost_message)
	yield(Global,"ok_message")
	if extra_questions:
		$book/Tween.interpolate_property($book, "global_position", $book.global_position, Vector2(515, 238), 1,Tween.TRANS_LINEAR,Tween.EASE_IN_OUT)
		$book/Tween.start()
		return
	get_tree().change_scene_to(next_scene)
	
func _on_ShowPath_pressed():
	view_path()
	attemptsCount += 1
	if(Global.playedLevel != 1):
		Global.attemptsPressedButtonViewPath += 1
	
func view_path(add_log=true):
	if add_log:    
		Events.add_registry(Events.events_names.ROUTE_HELP, menu_name)
	$background/helpPanel2.hide()
	for a in arrows_node.get_children():
		a.queue_free()
	for i in range(currend_player_pos+1):
		if i >= path_size:
			break
		
		grid_map.set_cellv(path[i], 0)
		if i < currend_player_pos:
			var a = arrow.instance()
			arrows_node.add_child(a)
			a.direction(path[i+1]-path[i])
			a.global_position = grid_map.to_global(grid_map.map_to_world(path[i]))
			
func view_path_permanent(add_log=true):
	if add_log:    
		Events.add_registry(Events.events_names.ROUTE_HELP, menu_name)
	#$background/helpPanel2.hide()
	for a in arrows_node.get_children():
		a.queue_free()
	for i in range(more_far_player_pos+1):
		if i >= path_size:
			break
		
		grid_map.set_cellv(path[i], 0)
		if i < more_far_player_pos:
			var a = arrow.instance()
			arrows_node.add_child(a)
			a.direction(path[i+1]-path[i])
			a.global_position = grid_map.to_global(grid_map.map_to_world(path[i]))


func _on_MoreTime_pressed():
	Events.add_registry(Events.events_names.TIME_HELP, menu_name)
	$background/helpPanel2.hide()
	$background/Clock.add_time(60)
	attemptsCount += 1
	if(Global.playedLevel != 1):
		Global.attemptsPressedButtonAddTime += 1
	time_button_count += 1
	if time_button_count >= 30:
		$background/HelpButtons/MoreTime.disabled = true

func _on_ShowHelp_pressed():
	$background/helpPanel.hide()
	$background/helpPanel2.show()

func _on_CloseHelp_pressed():
	$background/helpPanel2.hide()

func _on_HelpButton_pressed():
	Events.add_registry(Events.events_names.HELP_BUTTON, menu_name)
	if attemptsCount < 2:
		$background/helpPanel.show()
	if attemptsCount == 2:
		if Global.playedLevel == 2:
			Global.show_message("Ya usaste las ayudas")
	if Global.playedLevel == 1:
		$background/helpPanel.show()

func _on_Next_pressed(): 
	var question1 =  $book/questions/question1/Commend.text
	var question2 = $book/questions/question2/Commend.text
	var question3 = $book/questions/question3/Commend.text
	
	Events.question1 = question1 if question1 != "" else "Sin respuesta"
	Events.question2 = question2 if question2 != "" else "Sin respuesta"
	Events.question3 = question3 if question3 != "" else "Sin respuesta"
	get_tree().change_scene_to(next_scene)
