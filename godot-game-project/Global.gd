extends Node

signal winding()
signal wind_ready()
signal ok_message()
signal sended()
onready var swipe_instruction = $CanvasLayer/SwipeInstruction
onready var snow = $CanvasLayer/snow
enum point_massage_postion {top, botton, left, right, topright, bottonright, topleft, bottonleft, center}

var flag_values = {going=1, error = 2 ,going_back=4}
var playedLevel = 0	#2022 Ene: guardado parcial
var attemptsPressedButtonViewPath = 0
var attemptsPressedButtonAddTime = 0

func snow_visible(value:bool):
	snow.visible = value

func play_main_music():
	$MainMusic.play()

func _on_ForWind_timeout():
	$WindEffect.play()
	randomize()
	$ForWind.wait_time = rand_range(20, 70)
	$ForWind.start()
	emit_signal("winding")
	$Tween.interpolate_property(snow, "orbit_velocity", 0, 5, 2,Tween.TRANS_EXPO,Tween.EASE_IN)
	$Tween.start()
	
func stop_main_main():
	$MainMusic.stop()
	$WindEffect.stop()
	$CanvasLayer/snow.emitting = false

func _on_MainMusic_finished():
	yield(get_tree().create_timer(1),"timeout")
	if not $WindEffect.playing:
		$WindEffect.play()


func _on_Sound_toggled(button_pressed):
	AudioServer.set_bus_mute(0, button_pressed)

func show_swipe_instruction():
	swipe_instruction.show()

func _on_SwipeOk_pressed():
	swipe_instruction.hide()


func _on_WindEffect_finished():
	emit_signal("wind_ready")
	$Tween.interpolate_property(snow, "orbit_velocity", snow.orbit_velocity, 0, 2,Tween.TRANS_EXPO,Tween.EASE_IN_OUT)
	$Tween.start()
	
func show_message(text):
	$CanvasLayer/message/window/messageText.text = str(text)
	$CanvasLayer/message.show()
	
func show_arrow_message(text:String, rotation:float, pos=point_massage_postion.center):
	var message_node = $CanvasLayer/ArrowMessage/window
	$CanvasLayer/ArrowMessage/window/arrow.rect_rotation = rotation
	$CanvasLayer/ArrowMessage/window/messageText.text = str(text)
	$CanvasLayer/ArrowMessage.show()
	match pos:
		point_massage_postion.center:
			message_node.rect_position = Vector2(379, 193)
		
		point_massage_postion.top:
			message_node.rect_position = Vector2(379, 25)        
			
		point_massage_postion.botton:
			message_node.rect_position = Vector2(379, 337)        
		
		point_massage_postion.left:
			message_node.rect_position = Vector2(27, 193)        
		
		point_massage_postion.topleft:
			message_node.rect_position = Vector2(27, 25)        
			
		point_massage_postion.bottonleft:
			message_node.rect_position = Vector2(27, 337)        
		
		point_massage_postion.right:
			message_node.rect_position = Vector2(739, 193)        
		
		point_massage_postion.topright:
			message_node.rect_position = Vector2(739, 25)        
			
		point_massage_postion.bottonright:
			message_node.rect_position = Vector2(739, 337)

func _on_OK_pressed():
	$CanvasLayer/message.hide()
	emit_signal("ok_message")

func ArrowMessage_on_OK_pressed():
	$CanvasLayer/ArrowMessage.hide()
	emit_signal("ok_message")
	
func send_info():
	$CanvasLayer/Loading.show()
	Events.send_registry()
	yield(Events,"request_completed")
	print(Events.parsed_result)
	if Events.parsed_result == null:
		Global.show_message("Ocurrio un error al conectar. Reconectando...")
		yield(get_tree().create_timer(3.0),"timeout")
		_on_OK_pressed()
		send_info()
		return
	else:
		Global.show_message(Events.parsed_result.message)
		
	$CanvasLayer/Loading.hide()
	emit_signal("sended")
		
	
