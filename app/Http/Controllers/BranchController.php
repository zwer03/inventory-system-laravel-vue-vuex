<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Branch::orderBy($request->column, $request->order)->paginate($request->items_per_page);
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
     * Store a newly created resource in branch.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'address' => 'required'
        ]);
        $branch = Branch::create($validatedData);
        return Branch::where('id',$branch->id)->first();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return $branch;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        return $branch;
    }

    /**
     * Update the specified resource in branch.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branch $branch)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'address' => 'required'
        ]);
        
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->enabled = $request->enabled;
        $branch->save();
        return Branch::where('id', $branch->id)->first();
    }

    /**
     * Remove the specified resource from branch.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return 'Branch deleted';
    }

    public function get()
    {
        $model = Branch::select('id','name')->where('enabled', 1)->get();
        $formatted_model = array();
        foreach($model as $key=>$value){
            $formatted_model[$key]['value'] = $value['id'];
            $formatted_model[$key]['text'] = $value['name'];
        }
        return $formatted_model;
    }
}
