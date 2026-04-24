<?php

namespace App\Events;

use App\Models\Earning;
use App\Models\Activity;
use App\Models\Spending;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Auth;

class TransactionCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $transaction;
    public $userId;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;

        if (Auth::check()) {
            $this->userId = Auth::user()->id;
        } elseif (request()->get('apiKey')) {
            $this->userId = request()->get('apiKey')->user_id;
        }
    }
}
