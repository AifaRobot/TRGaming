extends LineEdit


func _ready():
    connect("focus_entered", self, "js_text_entry")


func js_text_entry():
    if not OS.has_touchscreen_ui_hint():
        return
    var texto = JavaScript.eval(
            "prompt('%s', '%s');" % ["Ingresá tu DNI:", text], 
            true
            )
            
    if texto:
        text = texto
        release_focus()
