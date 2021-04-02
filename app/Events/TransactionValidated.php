<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionValidated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $product;
    public $inventory;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transaction, $product, $inventory)
    {
        $this->transaction = $transaction;
        $this->product = $product;
        $this->inventory = $inventory;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('transaction-validated');
    }
}
