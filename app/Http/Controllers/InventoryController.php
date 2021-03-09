<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\TransactionValidated;
use App\Http\Repositories\ModelRepository;

class InventoryController extends Controller
{
    protected $model;

    public function __construct(Inventory $model)
    {
        $this->model = new ModelRepository($model);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->model->with(['product', 'storage'])->getPaginatedRecord($request);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        // Log::info($request);
        $data = array('result' => true, 'message' => null);
        DB::beginTransaction();
        try {
            // Check if transaction is adjustment or lost
            $isAdjustment = false;
            $inventory->with('product')->where(
                [
                    'product_id' => $request->product_id,
                    'storage_id' => $request->storage_id,
                ]
            )
            ->first();

            if ($inventory->quantity < $request->quantity) $isAdjustment = true;
            
            $inventory->quantity = $request->quantity;
            
            $transaction = new Transaction;
            $transaction->transaction_type = ($isAdjustment ? 'adjustment' : 'lost');
            $transaction->status = 10;
            $transaction->processed_by = auth()->user()->id;
            $transaction->validated_by = auth()->user()->id;
            $transaction->save();

            $transaction_detail = new TransactionDetail;
            $transaction_detail->transaction_id = $transaction->id;
            $transaction_detail->product_id = $request->product_id;
            $transaction_detail->quantity = $request->quantity;
            $transaction_detail->storage_id = $request->storage_id;
            $transaction_detail->save();

            event(new TransactionValidated($transaction, $inventory->product, $inventory));
            
            $data['result'] =  $inventory;
            $data['message'] = 'Transaction has been saved!';

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $data['success'] = false;
            $data['message'] = 'Transaction post failed! Stacktrace: (Message: ' . 
                $e->getMessage() .
                '; Line: ' . $e->getLine() . ')';
        }
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }

    public function checkCurrentQty()
    {
        $inventory['product_id'] = request('product_id');
        $inventory['quantity'] = 0;

        $existing_inv = Inventory::select('product_id', 'quantity')
            ->where('product_id', request('product_id'))
            ->where('storage_id', request('storage_id'))
            ->first();
        if ($existing_inv) $inventory = $existing_inv;

        return $inventory;
    }

    public function getCriticalLevelProducts(){
        return Inventory::where('alert', 1)->orderByDesc('updated_at')->get();
    }
}
