extends Node2D

var swipe_start = null
var minimum_drag = 100

func _input(event):
	if event is InputEventScreenTouch:
		if event.pressed:
			swipe_start = get_global_mouse_position()
		else:
			_calculate_swipe(get_global_mouse_position())
		
func _calculate_swipe(swipe_end):
	if swipe_start == null: 
		return
	var swipe = swipe_end - swipe_start
	if abs(swipe.x) > abs(swipe.y):
		if abs(swipe.x) > minimum_drag:
			if swipe.x > 0:
				Input.action_press("ui_right")
			else:
				Input.action_press("ui_left")
	else:
		if abs(swipe.y) > minimum_drag:
			if swipe.y > 0:
				Input.action_press("ui_down")
			else:
				Input.action_press("ui_up")
