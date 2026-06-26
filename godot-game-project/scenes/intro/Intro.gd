extends Node
export (Events.menu_name) var menu_name
export (bool) var fast_intro
onready var images = $images
onready var camera = $Camera2D
onready var book = $images/Image11/main/book
onready var next = $CanvasLayer/Next
onready var back = $CanvasLayer/Back
var zoom_map = [1.2, 0.8, 0.9, 1.2, 1.2, 1.4, 1.4, 0.8, 0.8, 1.4]
var current_image = 0

func _ready():
	
	if current_image == 0:
		back.disabled = true
		next.disabled = true
		play(0)
	Events.add_registry(Events.events_names.INIT, menu_name)
	for image in images.get_children():
		image.hide()
	images.get_children()[0].show()



func play(num:int):
	var node_images = images.get_children()
	if num > node_images.size():
		return    
	
	if not num+1 >= node_images.size():
		node_images[num+1].hide()
	
	node_images[num].init()
	$CanvasLayer/Next.disabled = true
	if fast_intro:
		node_images[num].playback_speed = 5
	focus_camera(node_images[num].global_position, zoom_map[num])
	yield($Tween,"tween_completed")
	var anim_play = node_images[num].play()
	node_images[num].show()
	yield(anim_play, "animation_finished")
	_on_Next_pressed()
	next.disabled = false
	$NextTimeout.start()

	
func focus_camera(new_pos:Vector2, zoom:float=1.2):
	$Tween.interpolate_property(camera, "global_position", camera.global_position, new_pos, 0.4,Tween.TRANS_LINEAR,Tween.EASE_IN)
	$Tween.interpolate_property(camera, "zoom", camera.zoom, Vector2(zoom, zoom), 0.4,Tween.TRANS_LINEAR,Tween.EASE_IN)
	$Tween.start()
	
func focus_camera_book(new_pos:Vector2, zoom:float=1.2):
	focus_camera(new_pos, zoom)
	$images/Image11/main/Tween.interpolate_property($images/Image11/main/character, "self_modulate", Color(1,1,1,1), Color(1,1,1,0.1), 1.2, Tween.TRANS_LINEAR,Tween.EASE_OUT)
	$images/Image11/main/Tween.start()    
	yield($Tween,"tween_completed")
	$images/Image11/main/Tween.interpolate_property(book, "offset", book.offset, Vector2(book.offset.x, 0), 0.3, Tween.TRANS_LINEAR,Tween.EASE_OUT)
	$images/Image11/main/Tween.start()
	$images/Image11/main/character.hide()
	book.show()
	yield($images/Image11/main/Tween,"tween_completed")
#    $images/Image11/LineEdit.show()
#    $images/Image11/EnterGame.show()
#    $images/Image11/main/book/LineEdit.rect_position = Vector2(-204, 93)


func stop_sound():
	$mountain.stop()


func _on_EnterGame_pressed(text):
	Events.add_registry(Events.events_names.BUTTON_CLICK, menu_name)
	Events.get_user_by_id(text)
#    $images/Image11/EnterGame.disabled = true
	book.disabled_enter(true)
	yield(Events,"request_completed")
	if Events.parsed_result == null:
		book.open_with_message("Ups. Parece que hubo un error vuelve a intentarlo")
	else:
		if not Events.parsed_result.worker:
			book.open_with_message("No es un DNI registrado")
		else:   
			if Events.parsed_result.worker.played:
				book.open_with_message("Ya ha jugado")
			else:
#                if $Formulario/Nombre.text != "":
				if Events.parsed_result.worker.type == 0:
					Events.player_type = "individual"
				else:
					Events.player_type = "liderazgo"
				book.open_with_message("Bienvenido(a) "+Events.parsed_result.worker.name, false)
				yield(get_tree().create_timer(4),"timeout")
				Global.play_main_music()
				get_tree().change_scene("res://scenes/game/demo.tscn")

	book.disabled_enter(false)
	Events.player_dni = text

func _process(delta):
	back.self_modulate.a = 0.4 if back.disabled else 1
	next.self_modulate.a = 0.4 if next.disabled else 1
	
func _on_Back_pressed():
	$NextTimeout.stop()
	current_image -= 1
	if current_image == 0:
		back.disabled = true
	play(current_image)


func _on_Next_pressed():
	$NextTimeout.stop()
	current_image += 1 
	back.disabled = false
	if current_image >= images.get_child_count():
		Global.play_main_music()
		get_tree().change_scene("res://scenes/Instruction1.tscn") 
		return      
	play(current_image)

func _on_Skip_pressed():
	get_tree().change_scene("res://scenes/Instruction1.tscn") 
	return      


func _on_NextTimeout_timeout():
	next.disabled = true
	_on_Next_pressed()
