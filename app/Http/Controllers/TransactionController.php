<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\TransactionValidated;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tx = Transaction::query();
        $tx->with('company', 'processed_by', 'validated_by');

        $type = $request->type;
        $warehouse_id = $request->warehouse_id;
        $status = $request->status;
        $start_date = date('Y-m-d H:i:s', strtotime($request->start_date . ' ' . '00:00:00'));
        $end_date = date('Y-m-d H:i:s', strtotime($request->end_date . ' ' . '23:59:59'));

        $tx->where(function ($whereClause) use ($type, $warehouse_id, $status, $start_date, $end_date) {
            if ($type) {
                $whereClause->where('transactions.transaction_type', $type);
            }
            if ($warehouse_id) {
                $whereClause->where('transactions.warehouse_id', $warehouse_id);
            }
            if ($status) {
                $whereClause->where('transactions.status', $status);
            }
            if ($start_date && $end_date) {
                $whereClause->whereBetween(
                    'transactions.created_at',
                    [new Carbon($start_date), new Carbon($end_date)]
                );
            }
        });

        if ($request->sort_by && $request->sort_desc) {
            $tx->orderBy($request->sort_by, ($request->sort_desc === "true" ? "desc" : "asc"));
        }      

        return (
        $request->items_per_page == -1
            ? $tx->paginate()
            : $tx->paginate($request->items_per_page)
        );
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
     * Store a newly created resource in product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info($request);
        $data = array('result' => true, 'message' => null);
        DB::beginTransaction();
        try {
            $transaction = new Transaction;
            $transaction->transaction_type = $request->transaction_type;
            $transaction->warehouse_id = $request->warehouse_id;
            $transaction->status = $request->status;

            if ($request->transaction_type == 'adjustment' || $request->status == 10) {
                $transaction->processed_by = auth()->user()->id;
                $transaction->validated_by = auth()->user()->id;
            }

            if ($request->status == 5) $transaction->processed_by = auth()->user()->id;
            if ($request->has('dr_number')) $transaction->dr_number = $request->dr_number;
            if ($request->has('company_id')) $transaction->company_id = $request->company_id;

            $transaction->save();

            if (isset($request->inventories) && !empty($request->inventories)) {
                foreach ($request->inventories as $inventory_details) {
                    $product_id = (isset($inventory_details['id']) ? $inventory_details['id'] : null);
                    Log::info('Update/Save new product');
                    $product = Product::updateOrCreate(
                        ['id' => $product_id],
                        [
                            'name' => $inventory_details['product'],
                            'unit' => $inventory_details['unit'],
                        ]
                    );

                    Log::info($product);
                    Log::info('Save new TransactionDetail');
                    $transaction_detail = new TransactionDetail;
                    $transaction_detail->transaction_id = $transaction->id;
                    $transaction_detail->product_id = $product->id;
                    if ($inventory_details['old_storage_id'])
                        $transaction_detail->old_storage_id = $inventory_details['old_storage_id'];
                    $transaction_detail->storage_id = $inventory_details['storage_id'];
                    $transaction_detail->quantity = $inventory_details['quantity'];
                    $transaction_detail->save();

                    Log::info($transaction_detail);
                    Log::info('Save new Inventory');
                    if ($request->status == 10) {
                        event(new TransactionValidated($transaction, $product, $inventory_details));
                    }
                    
                    $data['result'] = Transaction::with('processed_by', 'company')
                        ->where('id', $transaction->id)
                        ->first();;
                    $data['message'] = 'Transaction has been saved!';
                }
            } else {
                $data['success'] = false;
                $data['message'] = 'Please provide Inventory Details.';
            }

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
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        $transaction = Transaction::where('id', $transaction->id)
            ->with(
                'transaction_details.product',
                'transaction_details.storage',
                'transaction_details.old_storage'
            )
            ->first();
        return $transaction;
    }

    /**
     * Update the specified resource in product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        Log::info($request);
        $data = array('result' => true, 'message' => null);
        DB::beginTransaction();
        try {
            $tx = Transaction::where('id', $transaction->id)->first();
            Log::info($tx);
            $tx->transaction_type = $request->transaction_type;
            $tx->status = $request->status;
            if ($request->transaction_type == 'adjustment') {
                $tx->processed_by = auth()->user()->id;
                $tx->validated_by = auth()->user()->id;
            }

            if ($request->status == 5) $tx->processed_by = auth()->user()->id;
            elseif ($request->status == 10) $tx->validated_by = auth()->user()->id;

            if ($request->has('dr_number')) $tx->status = $request->dr_number;
            if ($request->has('company_id')) $tx->company_id = $request->company_id;
            $tx->save();

            if (isset($request->inventories) && !empty($request->inventories)) {
                $current_txd = array();
                foreach ($request->inventories as $inventory_details) {
                    $product_id = (isset($inventory_details['id']) ? $inventory_details['id'] : null);
                    Log::info('Update/Save new product');
                    $product = Product::updateOrCreate(
                        ['id' => $product_id],
                        [
                            'name' => $inventory_details['product'],
                            'unit' => $inventory_details['unit'],
                        ]
                    );

                    Log::info($product);
                    Log::info('Save new TransactionDetail');
                    $transaction_detail = TransactionDetail::where(
                        [
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                        ]
                    )
                    ->first();

                    if ($transaction_detail) {
                        $transaction_detail->quantity = $inventory_details['quantity'];
                        if ($inventory_details['old_storage_id'])
                            $transaction_detail->old_storage_id = $inventory_details['old_storage_id'];
                        $transaction_detail->storage_id = $inventory_details['storage_id'];
                        $transaction_detail->save();
                    } else {
                        $new_transaction_detail = new TransactionDetail;
                        $new_transaction_detail->transaction_id = $transaction->id;
                        $new_transaction_detail->product_id = $product->id;
                        $new_transaction_detail->quantity = $inventory_details['quantity'];
                        $new_transaction_detail->storage_id = $inventory_details['storage_id'];
                        $new_transaction_detail->save();
                    }

                    $current_txd[] = ($transaction_detail ? $transaction_detail->id : $new_transaction_detail->id);
                    Log::info('Save new Inventory');
                    if ($request->status == 10) event(new TransactionValidated($transaction, $product, $inventory_details));
                    
                    TransactionDetail::whereNotIn('id', $current_txd)
                        ->where('transaction_id', $transaction->id)
                        ->delete();
                    $data['result'] = Transaction::with('processed_by', 'company')
                        ->where('id', $transaction->id)
                        ->first();
                    $data['message'] = 'Transaction has been saved!';
                }
            } else {
                $data['success'] = false;
                $data['message'] = 'Please provide Inventory Details.';
            }

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
     * Remove the specified resource from product.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function get()
    {
        $data = array();
        $data['notFresh'] = !empty(Transaction::first());
        $data['transactions'] = DB::table('transactions')
            ->select('warehouse_id', 'transaction_type', DB::raw('count(*) as total'))
            ->where('status', 5)
            ->groupBy('warehouse_id', 'transaction_type')
            ->get();
        return $data;
    }

    public function getProductTransactions(Request $request)
    {
        return TransactionDetail::with(['transaction' => function ($query) {
            $query->with('processed_by');
        }])
        ->with('storage')
        ->where('product_id','=', $request->product_id)
        ->where('storage_id','=', $request->storage_id)
        ->orderBy('created_at', 'desc')
        ->paginate($request->items_per_page);
    }
}
