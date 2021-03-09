<?php

namespace App\Listeners;

use App\Models\Inventory;
use Illuminate\Support\Facades\Log;
use App\Events\TransactionValidated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateOrCreateInventory
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TransactionValidated  $event
     * @return void
     */
    public function handle(TransactionValidated $event)
    {
        Log::info('from updateOrCreate listener');

        $inventory = Inventory::where(
            [
                'product_id' => $event->product->id,
                'storage_id' => $event->inventory['storage_id'],
            ]
        )->first();

        if ($event->transaction->transaction_type == 'transfer') {
            // Minus quantity from original storage
            $old_inventory = Inventory::where(
                [
                    'product_id' => $event->product->id,
                    'storage_id' => $event->inventory['old_storage_id']
                ]
            )->first();
            Log::info('old iv');
            Log::info($old_inventory);
            $old_inventory->quantity = $old_inventory->quantity - $event->inventory['quantity'];
            Log::info('before save');
            Log::info($old_inventory);
            if (!empty($event->product->alert_level) && isset($event->product->alert_level)) {
                $old_inventory->alert = (
                    $old_inventory->quantity > $event->product->alert_level
                        ? false
                        : true
                );
            }
                
            $old_inventory->save();
            Log::info('after save');
            Log::info($old_inventory);
        }
        if ($inventory) {
            Log::info('start of updating inventory');
            Log::info($inventory);
            if ($event->transaction->transaction_type == 'in') {
                $inventory->storage_id = $event->inventory['storage_id'];
                $inventory->quantity = $inventory->quantity + $event->inventory['quantity'];
            } elseif ($event->transaction->transaction_type == 'out') {
                $inventory->storage_id = $event->inventory['storage_id'];
                $inventory->quantity = $inventory->quantity - $event->inventory['quantity'];
            } elseif (
                $event->transaction->transaction_type == 'adjustment'
                || $event->transaction->transaction_type == 'lost'
            ) {
                $inventory->storage_id = $event->inventory['storage_id'];
                $inventory->quantity = $event->inventory['quantity'];
            } elseif ($event->transaction->transaction_type == 'transfer') {
                // Add quantity to new storage
                $inventory->storage_id = $event->inventory['storage_id'];
                $inventory->quantity = $inventory->quantity + $event->inventory['quantity'];
            }
            if (!empty($event->product->alert_level) && isset($event->product->alert_level)) {
                $inventory->alert = (
                    $inventory->quantity > $event->product->alert_level
                        ? false
                        : true
                );
            }
                
            $inventory->save();
        } else {
            $new_inventory = new Inventory;
            $new_inventory->product_id = $event->product->id;
            $new_inventory->storage_id = $event->inventory['storage_id'];
            $new_inventory->quantity = $event->inventory['quantity'];
            if (!empty($event->product->alert_level) && isset($event->product->alert_level)) {
                $new_inventory->alert = (
                    $new_inventory->quantity > $event->product->alert_level
                        ? false 
                        : true
                );
            }
            $new_inventory->save();
        }
        Log::info('end updateOrCreate listener');
    }
}
