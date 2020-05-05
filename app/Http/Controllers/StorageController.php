<?php

namespace App\Http\Controllers;

use App\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class StorageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $storages = Storage::query();
        $storages->with('warehouse');
        if(request('search'))
            $storages->where('id', request('search'));
        if(request('sort_by') && request('sort_desc'))
            $storages->orderBy(request('sort_by'), (request('sort_desc') === "true"?"desc":"asc"));
        
        return (request('items_per_page') == -1?$storages->paginate():$storages->paginate(request('items_per_page')));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'warehouse_id' => 'required'
        ]);
        $storage = Storage::create($validatedData);
        return Storage::where('id',$storage->id)->with('warehouse')->first();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function show(Storage $storage)
    {
        return $storage;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function edit(Storage $storage)
    {
        return $storage;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Storage $storage)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'warehouse_id' => 'required'
        ]);
        
        $storage->name = $request->name;
        $storage->warehouse_id = $request->warehouse_id;
        $storage->enabled = $request->enabled;
        $storage->save();
        return $storage;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Storage $storage)
    {
        $storage->delete();
        return 'Storage deleted';
    }

    public function get()
    {
        $model = Storage::with('warehouse')->where('enabled', 1)->get();
        $formatted_model = array();
        foreach($model as $key=>$value){
            $formatted_model[$key]['value'] = $value['id'];
            $formatted_model[$key]['text'] = $value['warehouse']['short_name'].' '.$value['name'];
        }
        return $formatted_model;
    }
}
