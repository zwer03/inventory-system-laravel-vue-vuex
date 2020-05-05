<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = Company::query();
        if(request('sort_by') && request('sort_desc'))
            $companies->orderBy(request('sort_by'), (request('sort_desc') === "true"?"desc":"asc"));
        
        return (request('items_per_page') == -1?$companies->paginate():$companies->paginate(request('items_per_page')));
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
     * Store a newly created resource in company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'role' => 'required'
        ]);
        $company = Company::create($validatedData);
        return Company::where('id',$company->id)->first();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return $company;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        return $company;
    }

    /**
     * Update the specified resource in company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'role' => 'required'
        ]);
        
        $company->name = $request->name;
        $company->address = $request->address;
        $company->role = $request->role;
        $company->enabled = $request->enabled;
        $company->save();
        return $company;
    }

    /**
     * Remove the specified resource from company.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return 'Company deleted';
    }

    public function get()
    {
        $role = (request('type')=='in'?'Supplier':'Customer');
        $model = Company::select('id','name')->where('enabled', 1)->where('role', $role)->get();
        $formatted_model = array();
        foreach($model as $key=>$value){
            $formatted_model[$key]['value'] = $value['id'];
            $formatted_model[$key]['text'] = $value['name'];
        }
        return $formatted_model;
    }
}
