<?php

namespace VanguardLTE\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VanguardLTE\Transaction;

class DepositSuccessedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $transaction;
    public function __construct(Transaction $transaction)
    {
        //
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        // $user = \VanguardLTE\User::where('id', $this->transaction->user_id)->first();
        $user = $this->transaction->user()->first();
        $user->notify(new \VanguardLTE\Notifications\DepositSuccessed($this->transaction));
    }
}
