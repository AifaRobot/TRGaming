extends TextEdit

var last_text = ""
export (int) var max_text_lenght = 200

func _on_Commend_text_changed():
	if text.length() >= max_text_lenght:
		text = last_text
	
	last_text = text
	
