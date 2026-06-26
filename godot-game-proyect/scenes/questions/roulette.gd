extends Node2D


func _process(delta):
    if int($images.rotation_degrees) in [180, 360]:
        $rouletteAudio.play()


