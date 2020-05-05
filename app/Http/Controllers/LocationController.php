<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Location::latest('id')->paginate($request->items_per_page);
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
     * Store a newly created resource in Location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);
        return Location::create($validatedData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        return $location;
    }

    /**
     * Update the specified resource in Location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);
        
        $location->name = $request->name;
        $location->description = $request->description;
        $location->enabled = $request->enabled;
        $location->save();
        return $location;
    }

    /**
     * Remove the specified resource from Location.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $location->delete();
        return 'Location deleted';
    }
    public function get()
    {
        $locations = Location::select('id','name')->where('enabled', 1)->get();
        $formatted_loc = array();
        foreach($locations as $key=>$location){
            $formatted_loc[$key]['value'] = $location['id'];
            $formatted_loc[$key]['text'] = $location['name'];
        }
        return $formatted_loc;
        // return Location::pluck('name', 'id');
    }
}
