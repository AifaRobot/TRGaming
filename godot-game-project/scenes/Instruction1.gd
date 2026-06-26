extends Control
export (Events.menu_name) var menu_name
export (PackedScene) var next

func _ready():
	Events.add_registry(Events.events_names.INIT, menu_name)

func _on_TextureButton_pressed():
	get_tree().change_scene_to(next)
