tool
extends CPUParticles2D
export (bool) var pause setget set_pause
export (float) var speed = 0.2

func set_pause(value):
	pause = value
	speed_scale = speed if !pause else 0
	
	


