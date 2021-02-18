<?php
declare(strict_types=1);

namespace Edde\Service\Transaction;

use Edde\Transaction\ITransaction;

trait Transaction {
    /** @var ITransaction */
    protected $transaction;

    /**
     * @param ITransaction $transaction
     */
    public function injectTransaction(ITransaction $transaction): void {
        $this->transaction = $transaction;
    }
}
