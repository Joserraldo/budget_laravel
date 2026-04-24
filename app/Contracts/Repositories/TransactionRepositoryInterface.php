<?php

namespace App\Contracts\Repositories;

interface TransactionRepositoryInterface
{
    public function getWeeklyBalance(string $year): array;
    public function getTransactionsByYearMonth(array $filterBy = []);
}
