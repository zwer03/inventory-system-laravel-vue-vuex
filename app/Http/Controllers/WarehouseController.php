<?php

namespace App\Http\Controllers;

use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $warehouses = Warehouse::query();
        if(request('sort_by') && request('sort_desc'))
            $warehouses->orderBy(request('sort_by'), (request('sort_desc') === "true"?"desc":"asc"));
        
        return (request('items_per_page') == -1?$warehouses->paginate():$warehouses->paginate(request('items_per_page')));
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
            'name' => 'required',
            'short_name' => 'required|max:5',
        ]);
        $warehouse = Warehouse::create($validatedData);
        return Warehouse::where('id',$warehouse->id)->first();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        return $warehouse;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        return $warehouse;
    }

    /**
     * Update the specified resource in warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'short_name' => 'required|max:5',
        ]);
        
        $warehouse->name = $request->name;
        $warehouse->short_name = $request->short_name;
        $warehouse->enabled = $request->enabled;
        $warehouse->save();
        return $warehouse;
    }

    /**
     * Remove the specified resource from warehouse.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return 'Warehouse deleted';
    }

    public function get()
    {
        $model = Warehouse::select('id','name')->where('enabled', 1)->get();
        $formatted_model = array();
        foreach($model as $key=>$value){
            $formatted_model[$key]['value'] = $value['id'];
            $formatted_model[$key]['text'] = $value['name'];
        }
        return $formatted_model;
    }
}
