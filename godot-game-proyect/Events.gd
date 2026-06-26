extends HTTPRequest

signal finished
signal error

var ENDPOINT = JavaScript.eval("window.GAME_ENDPOINT || window.location.origin")

var user_id
var start_time
var current_session_id

var last_body = null
var parsed_result = null

var player_dni = ""
var answer_text = "1_2_3_4"
var player_type = "individual"
var observation = "Sin observación"
var question1 = "Sin comentario"
var question2 = "Sin comentario"
var question3 = "Sin comentario"
var events_registry = []

var player_won = 0

enum menu_name {INTRO, SPLASH, DEMO, GAME, QUESTIONS, OBSERVATION, INSTRUCTIONS1, INSTRUCTIONS2}
enum events_names {
#    SWIPE_UP, SWIPE_DOWN, SWIPE_LEFT, SWIPE_RIGHT,
	MOVE_UP, MOVE_DOWN, MOVE_LEFT, MOVE_RIGHT,
	BUTTON_CLICK, ROULETTE_CLICK, INIT, TIME_HELP, ROUTE_HELP, HELP_BUTTON
   }

func add_registry(event, menu, flag=0):
	var current_time = OS.get_datetime()
	var formated_time = "{Y}-{M}-{D} {h}:{m}:{s}".format({
		"Y":current_time.year,
		"M":current_time.month,
		"D":current_time.day,
		"h":current_time.hour,
		"m":current_time.minute,
		"s":current_time.second,
		})
	
#    print({event=events_names.keys()[event], menu=menu_name.keys()[menu], created_at=formated_time, worker_dni=player_dni, flag=flag})
	events_registry.append({event=events_names.keys()[event], menu=menu_name.keys()[menu], created_at=formated_time, worker_dni=player_dni, flag=flag})
	
func update_dni():
	for event in events_registry:
		event.worker_dni = player_dni
	

# Called when the node enters the scene tree for the first time.
func _ready():
	start_time = OS.get_ticks_msec()

func post(action, parameters = {}):
	pass
#    var headers = ["Content-Type: application/json"]
#    request(ENDPOINT + action, headers, false, HTTPClient.METHOD_POST, JSON.print(parameters))
	
func new_player(_name):
	pass
#    post("player", {"name": _name})
#    user_id = yield(self, "finished")
#    print("new_player - %s" % _name)

func new_level(level):
	post("session", {"user_id": user_id, "level":level})
	current_session_id = yield(self, "finished")
	start_time()
#    print("new_level - %d (session-id=%s) " % [level, current_session_id])

#func observation(text):
#    pass
#    post("observation", {"user_id": user_id, "text": text})
#    print("observation - %s" % text)

func action(type, metadata={}, time = -1):
	if time == -1:
		time = (OS.get_ticks_msec() - start_time) / 1000.0
	
#    post("event", {"session_id": current_session_id, "action": type, "metadata":metadata, "time": time})
#    print("action - %s" % type)


func send_registry():
	
	var query = JSON.print({'playedLevel':Global.playedLevel, 'win':player_won, 'observation':'*', 'anwers':'*', 'question1':'*', 'question2':'*', 'question3':'*', 'registries':events_registry, 'countHelpViewPath': Global.attemptsPressedButtonViewPath, 'countHelpAddTime': Global.attemptsPressedButtonAddTime})	
	if Global.playedLevel>=3:
		query = JSON.print({'playedLevel':Global.playedLevel, 'win':player_won, 'observation':observation, 'anwers':answer_text, 'question1':question1, 'question2':question2, 'question3':question3, 'registries':events_registry, 'countHelpViewPath': Global.attemptsPressedButtonViewPath, 'countHelpAddTime': Global.attemptsPressedButtonAddTime})	
	
	print(query)
	
	#limpio
	events_registry.clear()
	var headers = ["Content-Type: application/json"]		
	request(ENDPOINT+"/api/registry/dni="+str(player_dni), headers, true, HTTPClient.METHOD_POST, query)


func get_user_by_id(id):
	var headers = ["Content-Type: application/json"]
	request(ENDPOINT+"/api/workers/"+str(id), headers)
#    yield(self,"request_completed")
#    if last_body:
#        var json = JSON.parse(last_body.get_string_from_utf8())
#        parsed_result = json.result
#        return
#
#    parsed_result = null
	
func handler(result, response_code, headers, body):
	#var json
	last_body = body
	if last_body:
		var json = JSON.parse(last_body.get_string_from_utf8())
		parsed_result = json.result
	else:
		parsed_result = null
		
	match response_code:
		200:
			#json = JSON.parse(body.get_string_from_utf8())
			#emit_signal("finished", json)
			emit_signal("finished", body.get_string_from_utf8())
		_:
			emit_signal("error", response_code)

func start_time():
	start_time = OS.get_ticks_msec()
