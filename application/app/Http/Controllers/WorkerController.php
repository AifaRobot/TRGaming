<?php

namespace App\Http\Controllers;

use App\Workers;
use App\Registry;
use App\Selectora;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Validator;
use DateTime;

use Illuminate\Support\Facades\Mail;

use App\Mail\NoticeMessage;


class WorkerController extends Controller
{
    public function index()
    {
        $workers = Workers::all()->toArray();

        $i = 0;
        foreach ($workers as $worker) {
            $registries = Registry::where('worker_dni', $worker['dni'])->get();
            $tiene_registros = count($registries) > 0;
            $workers[$i]['tiene_registros'] = $tiene_registros;
            $i++;
        }

        return response()->json([
            'workers' => $workers,
        ]);
    }

    public function show($dni)
    {
        $worker = Workers::where('dni',$dni)->first();
        return response()->json([
            'worker' => $worker,
        ]);
    }

    public function report($dni)
    {
        try {
            return response()->json(self::getRegistry($dni));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al procesar el reporte: ' . $e->getMessage()], 500);
        }
    }

    public function metrics()
    {
        $all_registries = array();
        $keys = array("total_time", "init_move_game", "game_time_average", "large_secuences_num_game", "time_help", "route_help", "game_secuences_num", "game_first_error_time", "game_errors");
        foreach (Workers::all()->toArray() as $worker){
            
            if ($worker['played'] == 3){
        
                $registry = self::getRegistry($worker['dni']);
                foreach ($keys as $key){
                    if (!array_key_exists($key, $all_registries)){
                        $all_registries[$key] = array(0, 0);
                    }
                    $all_registries[$key] = self::min_max($registry[$key], $all_registries[$key]);
                }
            }
        }
        return response()->json($all_registries);
    }

    private function min_max($registry, $selected_registry)
    {
        if ($registry < $selected_registry[0]){
            $selected_registry[0] = $registry;

        }
        if ($registry > $selected_registry[1]){
            $selected_registry[1] = $registry;

        }
        return $selected_registry;
    }

    public function getRegistry($dni)
    {
        $registries = Registry::where('worker_dni', $dni)->get();

        $registriesCount = count($registries); //mk

        if($registriesCount == 0){
            return response()->json([
                'message' => "Registries not found",
            ]);
        }
        $game_errors = [];
        $demo_errors = [];
        $error_counter = 0;
        $assert = 0;
        $total_time = strtotime($registries[$registriesCount-1]->created_at) - strtotime($registries[0]->created_at);
        
        $demo_times = array();
        $game_times = array();
        $time_help = 0;
        $route_help = 0;

        $first_demo_error = ["", ""];
        $first_game_error = ["", ""];

        $error_demo_flag = false;
        $back_demo_time = [];
        $back_demo_time_counter = [];

        $init_move_demo = null;
        $init_move_game = null;

        $error_game_flag = false;
        $back_game_time = [];
        $back_game_time_counter = [];

        $questions_time = [];

        $worker = Workers::where('dni',$dni)->first();
        $answers_num = explode(" ",$worker["anwers"]);

        $user_type = "liderazgo";
        if ($worker["type"] == 0){
            $user_type = "individual";
        }

        $questions = Storage::disk('local')->get('questions.json');
        $questions = json_decode($questions, true);
        $questions = $questions[$user_type];
        $answers = [];

        for ($i=0; $i < count($answers_num); $i++){
            array_push($answers, ["question"=>$questions[$i]["question"], "answer"=>$questions[$i]["answers"][(int)$answers_num[$i]], "points"=>$questions[$i]["points"][(int)$answers_num[$i]]]);
        }

        $game_secuence = [];
        $demo_secuence = [];
        $current_secuence = [];
        $game_secuence_ready = false;
        $demo_secuence_ready = false;

        $new_error_sequence = false;

        foreach ($registries as $key => $registry) {
            if ($registry->flag == 6  && $registry->menu == "DEMO"){
                $error_counter += 1;
            }

            if ($registry->flag == 6  && $registry->menu == "GAME"){
                $error_counter += 1;
            }

            if ($registry->flag == 1 && $registry->menu == "DEMO"){
                array_push($current_secuence, $registry);
                if ($new_error_sequence){
                    $new_error_sequence = false;
                    array_push($demo_errors, $error_counter);
                    $error_counter = 0;
                }

            }
            if ($registry->flag == 3 && $registry->menu == "DEMO"){
                if (!empty($current_secuence)){
                    array_push($demo_secuence, $current_secuence);
                    $current_secuence = [];
                    $new_error_sequence = true;
                }
            }
            if ($registry->menu == "GAME" && !$demo_secuence_ready){
                $demo_secuence_ready = true;
                array_push($demo_secuence, $current_secuence);
                array_push($demo_errors, $error_counter);
                $error_counter = 0;
                $current_secuence = [];
            }
            
            if ($registry->flag == 1 && $registry->menu == "GAME"){
                array_push($current_secuence, $registry);
                if ($new_error_sequence){
                    $new_error_sequence = false;
                    array_push($game_errors, $error_counter);
                    $error_counter = 0;
                 }
            }
            if ($registry->flag == 3 && $registry->menu == "GAME"){
                if (!empty($current_secuence)){
                    array_push($game_secuence, $current_secuence);
                    $current_secuence = [];
                    $new_error_sequence = true;
                }
            }
            if ($registry->menu == "QUESTIONS" && !$game_secuence_ready){
                $game_secuence_ready = true;
                array_push($game_secuence, $current_secuence);
                array_push($game_errors, $error_counter);
                $error_counter = 0;
                $current_secuence = [];
            }

            if ($init_move_demo == null && ($registry->menu == "DEMO" && (strpos($registry->event, "MOVE") !== false) )){
                $init_move_demo = isset($registries[$key-2]) ? strtotime($registry->created_at) - strtotime($registries[$key-2]->created_at) : 0;
            }
            if ($init_move_game == null && ($registry->menu == "GAME" && (strpos($registry->event, "MOVE") !== false ))){
                $init_move_game = isset($registries[$key-2]) ? strtotime($registry->created_at) - strtotime($registries[$key-2]->created_at) : 0;
            }

            if ($registry->menu == "DEMO" && $registry->event == "INIT"){
                array_push($demo_times, $registry->created_at);
            }
            if ($registry->menu == "GAME" && $registry->event == "INIT"){
                array_push($game_times, $registry->created_at);
                if (isset($registries[$key-1])) array_push($demo_times, $registries[$key-1]->created_at);
            }
            if ($registry->menu == "QUESTIONS" && $registry->event == "INIT"){
                if (isset($registries[$key-1])) array_push($game_times, $registries[$key-1]->created_at);
                array_push($questions_time, $registry->created_at);
            }
            if ($registry->menu == "OBSERVATION" && $registry->event == "INIT"){
                if (isset($registries[$key-1])) array_push($questions_time, $registries[$key-1]->created_at);
            }

            if ($registry->event == "TIME_HELP"){
                $time_help += 1;
            }
            if ($registry->event == "ROUTE_HELP"){
                $route_help += 1;
            }

            if ($registry->menu == "DEMO" && (in_array($registry->flag, array(3, 6))) && $first_demo_error[0] == ""){
                $first_demo_error[0] = $registry->created_at;
            }
            if ($registry->menu == "DEMO" && (in_array($registry->flag, array(3, 6))) && $first_demo_error[0] != "" && $first_demo_error[1] == ""){
                $first_demo_error[1] = $registry->created_at;
            }

            if ($registry->menu == "GAME" && (in_array($registry->flag, array(3, 6))) && $first_game_error[0] == ""){
                $first_demo_error[0] = $registry->created_at;
            }
            if ($registry->menu == "GAME" && (in_array($registry->flag, array(3, 6))) && $first_game_error[0] != "" && $first_game_error[1] == ""){
                $first_game_error[1] = $registry->created_at;
            }

            if ($registry->menu == "DEMO" && (in_array($registry->flag, array(6, 3))) && !$error_demo_flag){
                array_push($back_demo_time, $registry->created_at);
                $error_demo_flag = true;
            }
            if ($registry->menu == "DEMO" && (in_array($registry->flag, array(1))) && $error_demo_flag){
                array_push($back_demo_time_counter,  strtotime($registry->created_at) - strtotime(array_pop($back_demo_time)));
                $error_demo_flag = false;
            }

            if ($registry->menu == "GAME" && (in_array($registry->flag, array(6, 3))) && !$error_game_flag){
                array_push($back_game_time, $registry->created_at);
                $error_game_flag = true;
            }
            if ($registry->menu == "GAME" && (in_array($registry->flag, array(1))) && $error_game_flag){
                array_push($back_game_time_counter, strtotime($registry->created_at) - strtotime(array_pop($back_game_time)));
                $error_game_flag = false;
            }
            
        }

        $large_secuences_num_game = 0;
        $all_game_secuence_move = [];
        $all_game_secuence_average = [];

        foreach ($game_secuence as $key => $secuence){
            if (count($secuence) <= 0){
                continue;
            }
            $time = strtotime($secuence[count($secuence)-1]->created_at) - strtotime($secuence[0]->created_at);
            if ($time == 0){
                $time = 1;
            }
            array_push($all_game_secuence_average, $time);

            array_push($all_game_secuence_move, count($secuence));

            if (count($secuence) >= 5){
                $large_secuences_num_game += 1;
            }

        }

        $large_secuences_num_demo = 0;
        $all_demo_secuence_move = [];
        $all_demo_secuence_average = [];

        foreach ($demo_secuence as $key => $secuence){
            if (count($secuence) <= 0) continue;
            $time = strtotime($secuence[count($secuence)-1]->created_at) - strtotime($secuence[0]->created_at);
            if ($time == 0){
                $time = 1;
            }
            array_push($all_demo_secuence_average, $time);

            array_push($all_demo_secuence_move, count($secuence));

            if (count($secuence) >= 5){
                $large_secuences_num_demo += 1;
            }

        }

        
        $demo_total_time = isset($demo_times[1], $demo_times[0]) ? strtotime($demo_times[1]) - strtotime($demo_times[0]) : 0;
        $game_total_time = isset($game_times[1], $game_times[0]) ? strtotime($game_times[1]) - strtotime($game_times[0]) : 0;
        $question_total_time = isset($questions_time[1], $questions_time[0]) ? strtotime($questions_time[1]) - strtotime($questions_time[0]) : 0;


        $demo_firts_error_time = strtotime($first_demo_error[0]) - strtotime($first_demo_error[1]);
        $game_firts_error_time = strtotime($first_game_error[0]) - strtotime($first_game_error[1]);

        $average_back_game_time = 0;
        foreach ($back_game_time_counter as $key => $time) {
            $average_back_game_time += $time;
        }
        $average_back_game_time = count($back_game_time_counter) ? $average_back_game_time/count($back_game_time_counter) : 0;

        $average_back_demo_time = 0;
        foreach ($back_demo_time_counter as $key => $time) {
            $average_back_demo_time += $time;
        }
        $average_back_demo_time = count($back_demo_time_counter) ? $average_back_demo_time/count($back_demo_time_counter) : 0;
       
        return [
            'game_secuences_num' => count($game_secuence),
            'demo_secuences_num' => count($demo_secuence),
            'all_game_secuence_move' => $all_game_secuence_move,
            'all_demo_secuence_move' => $all_demo_secuence_move,
            'all_game_secuence_average' => $all_game_secuence_average,
            'all_demo_secuence_average' => $all_demo_secuence_average,
            'large_secuences_num_game' => $large_secuences_num_game,
            'large_secuences_num_demo' => $large_secuences_num_demo,
            'game_move_average' => count($all_game_secuence_move) ? array_sum($all_game_secuence_move)/count($all_game_secuence_move) : 0,
            'demo_move_average' => count($all_demo_secuence_move) ? array_sum($all_demo_secuence_move)/count($all_demo_secuence_move) : 0,
            'game_first_error_average' => $all_game_secuence_average[0] ?? 0,
            'demo_first_error_average' => $all_demo_secuence_average[0] ?? 0,
            'game_time_average' => count($all_game_secuence_average) ? array_sum($all_game_secuence_average)/count($all_game_secuence_average) : 0,
            'demo_time_average' => count($all_demo_secuence_average) ? array_sum($all_demo_secuence_average)/count($all_demo_secuence_average) : 0,
            'answers' => $answers,
            'total_time' => $total_time,
            'asserts' => $assert,
            'demo_errors' => array_sum($demo_errors),
            'game_errors' => array_sum($game_errors),
            'demo_errors_list' => $demo_errors,
            'game_errors_list' => $game_errors,
            'game_time' => $game_total_time,
            'demo_time' => $demo_total_time,
            'question_time' => $question_total_time,
            'game_first_error_time' => $demo_firts_error_time,
            'demo_first_error_time' => $demo_firts_error_time,
            'average_back_demo_time' => $average_back_demo_time,
            'average_back_game_time' => $average_back_game_time,
            'init_move_demo' => $init_move_demo,
            'init_move_game' => $init_move_game,
            'time_help' => $time_help,
            'route_help' => $route_help,
            'registries' => $registries,
        ];
    }

    private function cleanTime($time)
    {
        $time = ($time<1)? 1 : $time;
        $tokens = array (
            31536000 => 'Años',
            2592000 => 'Mes',
            604800 => 'Sem',
            86400 => 'D',
            3600 => 'h',
            60 => 'Min',
            1 => 'Seg'
        );
    
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            $text_time = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'':'');
            $left_time = $time - $numberOfUnits*$unit;
            if ($left_time != 0 && $unit != 1){
                $text_time .= " ".self::cleanTime($left_time);
            }
            return $text_time;
        }
    
    }

    public function obtener_edad_segun_fecha($fecha_nacimiento)
    {
        $nacimiento = new DateTime($fecha_nacimiento);
        $ahora = new DateTime(date("Y-m-d"));
        $diferencia = $ahora->diff($nacimiento);
        return $diferencia->y;
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $data = $input['worker'];

        if (Workers::where('dni', $data['dni'])->exists()) {
            return response()->json(['message' => "Ya existe un usuario con el DNI {$data['dni']}"], 422);
        }

        $worker = new Workers;
        $worker->name = $data['name'];
        $worker->dni = $data['dni'];
        $worker->type = $data['type'];
        $worker->email = $data['email'];
        $worker->education_level = $data['education_level'];
        $worker->date_of_birth = $data['date_of_birth'];
        $worker->age = $this->obtener_edad_segun_fecha($data["date_of_birth"]);
        $worker->in_charge = $data['in_charge'];
        $worker->search_company = $data['search_company'];
        $worker->pos_to_apply = $data['pos_to_apply'];
        $worker->countHelpViewPath = 0;
        $worker->countHelpAddTime = 0;
        $worker->emailsSended = 0;
        $worker->id_selectoras = $data['id_selectoras'];

        try {
            $worker->save();
        } catch (\Exception $e) {
            return response()->json(['message' => "Error al guardar el usuario: " . $e->getMessage()], 500);
        }

        try {
            $selectora = new Selectora;
            $emailSelectora = $selectora->select()->where('id', '=', $worker->id_selectoras)->first()->email;
            Mail::to($data['email'])->bcc($emailSelectora)->send(new NoticeMessage('newUser'));
            $worker->emailsSended = 1;
            $worker->save();
        } catch (\Exception $e) {
            return response()->json(['message' => "Usuario creado, pero no se pudo enviar el email: " . $e->getMessage()], 207);
        }

        return response()->json(['message' => "Usuario creado con éxito"]);
    }

    public function gameData(){
        return response()->json([
            'data' => [
                'totalUsuarios' => Workers::select()->count(),
                'usuariosJugaron' => Workers::select()->where('played', '=', '3')->count(),
                'usuariosNoTerminaron' => Workers::select()->where('played', '!=', '3')->where('played', '!=', '0')->count(),
                'usuariosNoJugaron' => Workers::select()->where('played', '!=', '2')->where('played', '!=', '1')->where('played', '!=', '3')->count()
            ]
        ]);
    }

    public function listUsersFinished(){
        $workers = Workers::where('played', '=', '3')->get();
        $result = array();
        
        foreach($workers as $worker) {
            $registries = Registry::where('worker_dni', $worker['dni'])->get();

            if (count($registries) > 0) {
                $worker['tiene_registros'] = true;
                $result[] = $worker;
            }
        }

        return response()->json([
            'workers' => $result
        ]);
    }

    public function listUsersNotFinished(){
        return response()->json([
            'workers' => Workers::where('played', '!=', '3')->where('played', '!=', '0')->get()
        ]);
    }

    public function listUsersNotPlayed(){
        return response()->json([
            'workers' => Workers::where('played', '!=', '2')->where('played', '!=', '1')->where('played', '!=', '3')->get()
        ]);
    }

    public function update(Request $request, $dni)
    {
        $worker = Workers::where('dni',$dni);
        $input = $request->all();
        $data = $input['worker'];
        $worker->update(array(
            'name' =>  $data['name'],
            'dni' =>  $data['dni'],
            'type' =>  $data['type'],
            'email' =>  $data['email'],
            'education_level' =>  $data['education_level'],
            'date_of_birth' =>  $data['date_of_birth'],
            'age' => $this->obtener_edad_segun_fecha($data["date_of_birth"]),
            'in_charge' =>  $data['in_charge'],
            'search_company' =>  $data['search_company'],
            'id_selectoras' => $data['id_selectoras']
        ));

        return response()->json([
            'message' => "Usuario Actualizado",
        ]);
    }

    public function delete($dni)
    {
        self::cleanRegister($dni);
        $worker = Workers::where('dni',$dni) -> delete();

        return response()->json([
            'message' => "El usuario ha sido eliminado exitosamente",
        ]);
    }
    public function cleanRegister($dni)
    {
        $registry = Registry::where('worker_dni',$dni) -> delete();
        $worker = Workers::where('dni',$dni);
        $worker->update(array('played' => false, 'win' => false, 'observation' => " ", 'anwers' => " "));

        return response()->json([
            'message' => "Registros eliminados",
        ]);
    }
}
