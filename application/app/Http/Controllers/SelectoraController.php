<?php

namespace App\Http\Controllers;

use App\Selectora;

use Illuminate\Http\Request;

class SelectoraController extends Controller
{
    public function selectoras() {
        return response()->json([
            'selectoras' => Selectora::all()->toArray(),
        ]);
    }

    public function update(Request $request, $id) {
        $selectora = Selectora::where('id', $id);
        $input = $request->all();
        $data = $input['selectora'];

        $selectora->update(array(
            'name' =>  $data['name'],
            'email' =>  $data['email']
        ));

        return response()->json([
            'message' => "Usuario Actualizado",
        ]);
    }

    public function delete($id) {
        $selectora = Selectora::where('id',$id)->delete();

        return response()->json([
            'message' => "La selectora ha sido eliminada exitosamente",
        ]);
    }

    public function save(Request $request) {
        $input = $request->all();
        
        $data = $input['selectora'];

        $selectora = new Selectora;
        $selectora->name = $data['name'];
        $selectora->email = $data['email'];
        
        $selectora->save();

        return response()->json([
            'message' => "Selectora Creado con exito",
        ]);
        
        return response()->json([
            'message' => "Error",
            'data' => $input['selectora'],
        ]);
    }
}
