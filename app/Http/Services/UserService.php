<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Validator;

class UserService
{

    public function validateUserRequests($request) : \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'id_no' => 'required|int|unique:users', //validate id_no to be unique
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'gender' => 'required|string|max:1'
        ]);
    }
}
