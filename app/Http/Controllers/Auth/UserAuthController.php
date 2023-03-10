<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ConsumerData;
use Illuminate\Http\Request;
use Validator;


class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);
        $data['password'] = bcrypt($request->password);
        $user = User::create($data);
        $token = $user->createToken('API Token')->accessToken;
        return response([ 'user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if($validator->passes()){
                $data = $request->validate([
                    'username' => 'required',
                    'password' => 'required'
                ]);
    
                if (!auth()->attempt($data)) {
                    return response(['error_message' => 'Incorrect Details. 
                    Please try again']);
                }
                $token = auth()->user()->createToken('API Token')->accessToken;
                return response(['user' => auth()->user(), 'token' => $token]);
            }else{
                return response($validator->errors());

            }
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function getUser(Request $request)
    {
        return response($request->user());
    }

    public function getConsumerData(Request $request)
    {
        $all_user = ConsumerData::all();
        return response($all_user);
    }

    public function addConsumerData(Request $request)
    {
        try {
            $input = $request->all();
            $all_user = ConsumerData::insert($input);
            return response(array('message'=>'Consumer add successfully'));
        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

    public function getConsumerDataById(Request $request,$id)
    {
        try {
            $data = ConsumerData::where('id', $id)->orWhere('consumer_code', $id)->orWhere('demand_no', $id)->first();
            return response($data);
        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }
}