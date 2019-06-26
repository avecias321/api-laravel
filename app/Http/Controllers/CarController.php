<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Helpers\JwtAuth;
use App\Car;


class CarController extends Controller
{
    public function index(){
        $cars = Car::all()->load('user');
        return response()->json(array(
            'cars' => $cars,
            'status' => 'success',
        ), 200);
    }

    public function show($id){
        $car = Car::find($id);

        if(is_object($car))
            return response()->json(array('car' => $car, 'status' => 'success'),200);
    }

    public function store(Request $request){
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //recoger datos por Post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true);

            //validacion
            $request->merge($params_array);
            try{
                $validate = $this->validate($request,[
                    "title" => 'required|min:5',
                    "description" => 'required',
                    "price" => 'required',
                    "status" => 'required'
                ]);

            }catch(\Illuminate\Validation\ValidationException $e){
                return $e ->getResponse();

            }


            /*$errors = $validate->errors();
            if($errors){return $errors->toJson();}*/

            /*if($validate->fails()){
                return response()->json($validate->errors(),400);
            }*/

            //guardar el coche
                $car = new Car();
                $car->user_id = $user->sub;
                $car->title = $params->title;
                $car->description = $params->description;
                $car->price = $params->price;
                $car->status = $params->status;
                $car->save();

                $data = array(
                    'car' => $car,
                    'status' => 'success',
                    'code' => 200,
                );

        }else{
            //devolver error
                $data = array(
                    'message' => 'Login incorrecto',
                    'status' => 'error',
                    'code'=> 300,
            );
        }

        return response()->json($data, 200);
    }
    public function update($id, Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken) {
            //Recoger parametros POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //Validar datos

            $request->merge($params_array);
            try{
                $validate = $this->validate($request,[
                    "title" => 'required|min:5',
                    "description" => 'required',
                    "price" => 'required',
                    "status" => 'required'
                ]);

            }catch(\Illuminate\Validation\ValidationException $e){
                return $e ->getResponse();

            }
            //Actualizar el REGISTRO
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            $car = Car::where('id', $id)->update($params_array);

            $data= array(
                'car' =>$params,
                'status' => 'success',
                'code' => 200
            );

        }else{
            //devolver error
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code'=> 300,
            );

        }
        return response()->json($data, 200);
    }

    public function destroy($id, Request $request){
        $hash = $request->header('Authorization', null);



        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            //comprobar que existe el registro
            $car = Car::find($id);

            //borrarlo
            $car->delete();

            //devolverlo
            $data = array(
                'car'=> $car,
                'status'=>'success',
                'code' => 200
            );
        }else{
            $data =array(
                'status'=> 'error',
                'code' =>400,
                'message'=> 'Login incorrecto!!'
            );
        }
        return response()->json($data, 200);


    }
}
