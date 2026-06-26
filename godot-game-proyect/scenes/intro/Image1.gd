extends "res://scenes/intro/Image.gd"

func play():
	$AnimationPlayer.seek(0.1)
	$AnimationPlayer.play("play")
#    call_deferred("sound")
	return $AnimationPlayer 
	
func sound():
#    yield(get_tree().create_timer(4.2),"timeout")
	$AudioStreamPlayer.play()
	emit_signal("shake", 4.0, 30, 20)
	

