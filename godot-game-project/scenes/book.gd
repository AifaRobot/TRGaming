extends Sprite
signal opened()
signal closed()
signal entered(text)

func open_with_message(text:String, back_button:bool= true):
    open()
    yield(self, "opened")
    show_message(text)
    $Label/back.visible = back_button

func open():
    $AnimationPlayer.play("open")
    $LineEdit.hide()
    yield($AnimationPlayer,"animation_finished")
    emit_signal("opened")
    
func close():
    $LineEdit.text = ""
    $AnimationPlayer.play_backwards("open")
    $Label.text = ""
    $Label.hide()
    yield($AnimationPlayer,"animation_finished")
    $LineEdit.show()
    emit_signal("closed")

func show_message(text:String):
    $Label.show()
    $Label.text = text

func disabled_enter(value:bool):
    $LineEdit/enter.disabled = value

func _on_back_pressed():
    close()

func _on_enter_pressed():
    emit_signal("entered", $LineEdit.text)
