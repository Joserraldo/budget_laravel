<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Models\Activity;
use App\Models\Earning;
use App\Models\Spending;

class LogTransactionActivity
{
    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;
        $userId = $event->userId;
        $entityType = null;

        if ($transaction instanceof Earning) {
            $entityType = 'earning';
        }

        if ($transaction instanceof Spending) {
            $entityType = 'spending';
        }

        Activity::create([
            'space_id' => $transaction->space_id,
            'user_id' => $userId,
            'entity_id' => $transaction->id,
            'entity_type' => $entityType,
            'action' => 'transaction.created'
        ]);
    }
}
