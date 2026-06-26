extends Control
export (Events.menu_name) var menu_name
onready var progress = $CanvasLayer/TextureRect/Progress
onready var progressMark = $CanvasLayer/TextureRect/Progress/q1
onready var anterior = $CanvasLayer/TextureRect/HBoxContainer2/Anterior
onready var next = $CanvasLayer/TextureRect/HBoxContainer2/Siguiente
onready var answer = $CanvasLayer/TextureRect/answer
onready var ready = $CanvasLayer/TextureRect/HBoxContainer2/Ready
onready var question_flag = $CanvasLayer/TextureRect/QuestionBanner/Label
onready var answers = $CanvasLayer/TextureRect/ScrollContainer2/answers
onready var quiestion_text = $CanvasLayer/TextureRect/ScrollContainer/question
onready var pointer = $roulette/pointer
onready var wheel = $roulette/images

export (float) var rulette_speed = 0.3

var avalible_questions = range(7)
var current_question:int = 0

var questions_map1 = []  
var b_group: ButtonGroup        
	   

func _ready():
	Events.add_registry(Events.events_names.INIT, menu_name)
#    get_tree().change_scene("res://game/Observaciones.tscn")
	$happy.play()
	Global.stop_main_main()
	var file = File.new()
	if file.open("res://questions.json", File.READ) == OK:
		var content = file.get_as_text()
		file.close()
		var result = JSON.parse(content)
		if result.error == OK:
			questions_map1 = result.result[Events.player_type]

	b_group = ButtonGroup.new()
	set_current_question(0)
	for answer in answers.get_children():
		answer.group = b_group
		
	nex_question()
		
	
func nex_question():
	wheel.rotation_degrees = 0
	$roulette.show()
	$roulette/Push.disabled = false
	randomize()
	avalible_questions.shuffle()
	current_question = avalible_questions.pop_back()
	set_current_question(current_question)
	
	
func set_question_flag(num):
	question_flag.text = "Pregunta %s"%num

func set_new_selected(num):
	questions_map1[num].current_selected = b_group.get_buttons().find(b_group.get_pressed_button())

func _on_Siguiente_pressed():
	var current_num =  progress.get_children().find(progressMark) 
	set_new_selected(current_num)
	anterior.disabled = false
	if not current_num >= progress.get_child_count()-1:
		current_num += 1
		progress.move_child(progressMark, current_num)
	if current_num == progress.get_child_count()-1:
		next.hide()
		ready.show()
	set_question_flag(current_num+1)
	set_current_question(current_num)

func set_current_question(num):
	var question = questions_map1[num]
	quiestion_text.text = question.question
	for i in range(3):
		answers.get_children()[i].set_new_text(question.answers[i])
	
	if question.current_selected > -1:
		answers.get_children()[question.current_selected].pressed = true
		next.disabled = false
	
	else:
		var b_pressed = b_group.get_pressed_button()
		if b_pressed:
			b_pressed.pressed = false
		next.disabled = true
	
	

func _on_Anterior_pressed():
	var current_num =  progress.get_children().find(progressMark) 
	if current_num > 0:
		anterior.disabled = false
		current_num -= 1
		progress.move_child(progressMark, current_num)
	
	ready.hide()
	next.show()
	set_question_flag(current_num+1)
	set_current_question(current_num)
	if not current_num:
		anterior.disabled = true


func _on_Ready_pressed():
	get_tree().change_scene("res://game/Observaciones.tscn")


func _on_CheckBox_pressed():
	next.disabled = false
	answer.disabled = false
	$click.play()

func _on_Push_pressed():
	$roulette/arrow.hide()
	Events.add_registry(Events.events_names.ROULETTE_CLICK, menu_name)
	$click.play()
	$roulette/AnimationPlayer.stop()
	pointer.scale = Vector2(1,1)
	$roulette/Push.disabled = true
	var round_counts = 4+(randi()%4)
	var current_speed = rulette_speed
	for i in range(round_counts):
		$roulette/Tween.interpolate_property(wheel, "rotation_degrees", 0, 360, current_speed,Tween.TRANS_LINEAR,Tween.EASE_OUT_IN)
		$roulette/Tween.start()        
		yield($roulette/Tween, "tween_completed")
		current_speed += rulette_speed/(i+1)
	   
	$roulette/Tween.interpolate_property(wheel, "rotation_degrees", 0, 51.42*current_question, current_speed,Tween.TRANS_BACK, Tween.EASE_OUT_IN)
	$roulette/Tween.start()
	yield($roulette/Tween, "tween_completed")
	$positive.play()
	$roulette/images.get_children()[current_question].self_modulate = Color(0.7, 0.7, 0.7)
	yield(get_tree().create_timer(2.0),"timeout")
	$roulette.hide()
	$CanvasLayer/TextureRect.show()
	$roulette/AnimationPlayer.play("apretar")
	


func _on_answer_pressed():
	Events.add_registry(Events.events_names.BUTTON_CLICK, menu_name)
	$CanvasLayer/TextureRect.hide()
	answer.disabled = true
	set_new_selected(current_question)
	$select.play()
	questions_map1
	if not avalible_questions:
		Events.answer_text = ""
		for i in range(questions_map1.size()):
			Events.answer_text += str(questions_map1[i].current_selected)+" "
		get_tree().change_scene("res://scenes/goodbye.tscn")
		return
	nex_question()
