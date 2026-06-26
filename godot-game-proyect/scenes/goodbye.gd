extends Control

export (Events.menu_name) var menu_name

func _ready():
	Events.add_registry(Events.events_names.INIT, menu_name)
	Global.playedLevel +=1
	print("Game goodbye at level: ", Global.playedLevel, " name: ", menu_name)
	Global.send_info()
	

func _on_TRLink_pressed():
	if JavaScript:
		JavaScript.eval("window.open('https://trconsultores.com.ar/', '_self')")
		
	else:
		OS.shell_open("https://trconsultores.com.ar/")
