<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

use App\Workers;
use App\Selectora;

use App\Mail\NoticeMessage;

class MessageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $workers = Workers::all()->toArray();

        $workers = Workers::where('dni', 42657584)->get();
        
        foreach($workers as $worker) {

            $workerToUpdate = Workers::where('dni', $worker['dni']);

            $selectora = new Selectora;
            $emailSelectora = $selectora->select()->where('id', '=', $worker['id_selectoras'])->first()->email;    

            switch ($worker['emailsSended']) {
                case 0:
                    Mail::to($worker['email'])->bcc($emailSelectora)->send(new NoticeMessage('alarmOne'));
                    $workerToUpdate->update(array('emailsSended' => $worker['emailsSended'] + 1));
                    break;
                case 1:
                    Mail::to($worker['email'])->bcc($emailSelectora)->send(new NoticeMessage('alarmTwo'));
                    $workerToUpdate->update(array('emailsSended' => $worker['emailsSended'] + 1));
                    break;
                case 2:
                    Mail::to($worker['email'])->bcc($emailSelectora)->send(new NoticeMessage('alarmTree'));
                    $workerToUpdate->update(array('emailsSended' => $worker['emailsSended'] + 1));
                    break;
                default:
                    echo 'Ya enviado <br>';
            }
            echo $worker['email'] . '<br>';
        }
        return 'Se ha enviado todo correctamente';
    }

    public function prueba($email)
    {
        Mail::to($email)->bcc('pablopagesreal@gmail.com')->send(new NoticeMessage('newUser'));
        return 'se ha enviado un email a la direccion' . $email;
    }
}
