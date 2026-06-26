extends Node2D

onready var image = $sprite

func direction(dir:Vector2):
	print(dir)
	if dir.x == 1:
		image.rotation_degrees = 0
	elif dir.x -1:
		image.rotation_degrees = 180    
		
	if dir.y == -1:
		image.rotation_degrees = 270    
		
	elif dir.y == 1:
		image.rotation_degrees = 90
