<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if nothing is appended on the index route then it will retrieve all active records of users
        $validator = Validator::make($request->all(), [
            'id_no' => 'nullable|int', // if provided on the url it will search by id_no e.g. /users?id_no=12345
            'firstname' => 'nullable|string',  // if provided on the url it will search by firstname e.g. /users?firstname=Api
            'lastname' => 'nullable|string', //if provided on the url it will search by lastname e.g. /users?lastname=Api
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $id_no = $request->id_no;
        $firstname = $request->firstname;
        $lastname = $request->lastname;


        $users = User::when($id_no, function ($q, $id_no) {
            $q->where('id_no', $id_no);
        })->when($firstname, function ($q, $firstname){
            $q->where('firstname', 'LIKE', "%$firstname%");
        })->when($lastname, function ($q, $lastname){
            $q->where('lastname', 'LIKE', "%$lastname%");
        }, function ($query) {
            return $query->orderBy('id');
        })->get();

        if ($users->count() === 0){
            return response()->json(['message' => "No match found!"], 201);
        }

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->userService->validateUserRequests($request);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = new User([
            'id_no' => $request->id_no,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'gender' => $request->gender
        ]);

        $user->save();

        return response()->json([
            'message' => "Successfully created user: {$user->firstname}!"
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = $this->userService->validateUserRequests($request);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::findOrFail($id);
        $user->id_no = $request->id_no;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->gender = $request->gender;
        $user->save();

        return response()->json([
            'message' => "Successfully updated user record: $id!"
        ], 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findorFail($id);

        $user->delete();

        return response()->json([
            'message' => "Successfully deleted user record: $id!"
        ], 201);
    }
}
