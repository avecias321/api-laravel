<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Recoger post
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if (!is_null($email) && !is_null($password) && !is_null($name)) {

            //crear el usuario
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            //comprobar usuario duplicado
            $isset_user = User::where('email', '=', $email)->first();

            if (is_null($isset_user)) {
                //guardar el usuario
                $user->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario registrado correctamente'
                );
            } else {
                //No guardarlo porque ya existe
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Usuario duplicado, no se puede registrar'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'usuario no creado'
            );
        }
        return response()->json($data, 200);
    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();

        //recibir POST
        $json = $request->input('json', null);
        //dd($json);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;
        //dd($params);
        // dd($password);
        //cifrar la contraseña
        $pwd = hash('sha256', $password);

        //dd([$email,$pwd]);
        if (!is_null($email) && !is_null($password)) {
            $signup = $jwtAuth->signup($email, $pwd);

            //dd($signup);
        } elseif ($getToken != null) {
            $signup = $jwtAuth->signup($email, $pwd, $getToken);
            //dd($signup);

        } else {
            $signup = array(
                'status' => 'error',
                'message' => 'Envia tus datos por post'
            );
        }

        return response()->json($signup, 200);
    }


}
