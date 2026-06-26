<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Workers;
use App\Registry;
use App\Selectora;
use App\Mail\NoticeMessage;

class RegistryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $dni)
    {
        $worker = Workers::where('dni',explode('=', $dni)[1]);
        $input = $request->all();
        
		$playedLevel = $input['playedLevel']; 
        if (!is_numeric($playedLevel))
            $playedLevel=0;
        
        if ($playedLevel == 3){
            try {
                $selectora = new Selectora;
                $emailSelectora = $selectora->select()->where('id', '=', $worker->first()->id_selectoras)->first()->email;
                
                Mail::to($worker->first()->email)->bcc($emailSelectora)->send(new NoticeMessage());
            } catch (Exception $e){
                //Hacer algo en caso de excepcion
            }
        }

        if ($playedLevel>($worker->first()->played))
		{
            $worker->update(
                array(
                    'played' => $playedLevel, 
                    'win' => $input['win'], 
                    'observation' => $input['observation'], 
                    'anwers' => $input['anwers'], 
                    'question1' => $input['question1'], 
                    'question2' => $input['question2'], 
                    'question3' => $input['question3'],
                    'countHelpViewPath' => $input['countHelpViewPath'], 
                    'countHelpAddTime' => $input['countHelpAddTime']
                )
            );
            $data = $input['registries'];
            Registry::insert($data);

            switch($playedLevel){
                case 1:
                    return response()->json([
                        'message' => "¡Completaste la demo!",
                    ]); 
                case 2:
                    return response()->json([
                        'message' => "¡Completaste el juego!",
                    ]);
                default:
                    return response()->json([
                        'message' => "Muchas gracias",
                    ]);
            }
        }

        return response()->json([
            'message' => "Este jugador ya ha jugado",
        ]);
    }
}
