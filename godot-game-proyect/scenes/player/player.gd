extends Node2D
onready var image_path = {
	front= preload("res://assets/images/player/esquiadora_frente.png"),
	back= preload("res://assets/images/player/esquiadora_detras.png"),
	left= preload("res://assets/images/player/esquiadora_perfil.png"),
	right= preload("res://assets/images/player/esquiadora_perfil.png"),
   }

#func hide_image():
#    for i in pos_image:
#        i.hide()
	
	
func fail(pos:Vector2):
	if pos.x > 0:
		$body.texture = image_path.left
		$body.flip_h = true
	elif pos.x < 0:
		$body.texture = image_path.right
		$body.flip_h = false
	elif pos.y > 0:
		$body.texture = image_path.front
		$body.flip_h = false
	elif pos.y < 0:
		$body.texture = image_path.back
		$body.flip_h = false
	$AnimationPlayer.play("fail")
		
func move(pos:Vector2):
	if pos.x > 0:
#        $body.texture = image_path.right
		$AnimationPlayer.play("walk_right")
	elif pos.x < 0:
		$AnimationPlayer.play("walk_left")  
#        $body.texture = image_path.left
	elif pos.y > 0:
		$AnimationPlayer.play("walk_front")  
#        $body.texture = image_path.back
	elif pos.y < 0:
		$AnimationPlayer.play("walk_back")  
#        $body.texture = image_path.front

func is_moving():
	return $AnimationPlayer.is_playing()
