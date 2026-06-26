extends Node2D
export (Events.menu_name) var menu_name
export (float) var playback_speed = 1
signal shake(duration, frequency, amplitude)
export (float) var shake_duration = 0
export (float) var shake_frequency = 0
export (float) var shake_amplitude = 0


func play():
	$AnimationPlayer.playback_speed = playback_speed
	$AnimationPlayer.play("play")
	emit_signal("shake", shake_duration, shake_frequency, shake_amplitude)
	return $AnimationPlayer 
	
func stop():
	$AnimationPlayer.stop()
	emit_signal("shake", 0, 0, 0)
	
func init():
	$AnimationPlayer.play("init")
