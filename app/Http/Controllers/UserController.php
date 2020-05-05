<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::query();
        if(request('sort_by') && request('sort_desc'))
            $users->orderBy(request('sort_by'), (request('sort_desc') === "true"?"desc":"asc"));
        
        return (request('items_per_page') == -1?$users->paginate():$users->paginate(request('items_per_page')));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255|string',
            'email' => 'required|max:255|email|string',
            'password' => 'required|min:8|confirmed',
        ]);
        $user = User::create($validatedData);
        return User::where('id',$user->id)->first();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|max:255|string',
            'email' => 'required|max:255|email|string',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->enabled = $request->enabled;
        $user->save();
        return $user;
    }

    /**
     * Remove the specified resource from warehouse.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return 'User deleted';
    }

    public function get()
    {
        $model = User::select('id','name')->where('enabled', 1)->get();
        $formatted_model = array();
        foreach($model as $key=>$value){
            $formatted_model[$key]['value'] = $value['id'];
            $formatted_model[$key]['text'] = $value['name'];
        }
        return $formatted_model;
    }

    public function allow(Request $request)
    {
        
        $user = User::find($request->id);

        $user->enabled = $request->enabled;

        $user->save();
        return $user;
    }
}
