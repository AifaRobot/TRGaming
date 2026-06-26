extends TextureRect

signal out_of_time

export (float) var timeout
var start_time
var paused = false setget set_paused

func _ready():
	start_time = OS.get_ticks_msec()
	
func add_time(value):
	timeout += value

func set_paused(value):
	paused = value
#    if paused:
#        $AnimationPlayer.stop()
#    else: 
#        $AnimationPlayer.play("crono_animation")

func _process(delta):
	if paused:
		start_time += delta * 1000
		
	$Label.text = "%s" % format_time()
	if OS.get_ticks_msec() / 1000 > timeout + start_time / 1000:
		set_process(false)
#        Events.action("OUT_OF_TIME", timeout)
		emit_signal("out_of_time")

func format_time():
	var delta = (OS.get_ticks_msec() - start_time) / 1000
	var minutes = max(floor((timeout - delta) / 60), 0)
	var seconds = max(int(timeout - delta) % 60, 0)
	
	return "%02d:%02d" % [minutes, seconds]
