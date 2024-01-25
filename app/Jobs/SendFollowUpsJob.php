<?php

namespace App\Jobs;

use App\Services\MessageSendingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFollowUpsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $msService;
    /**
     * Create a new job instance.
     *
     * @return void
     */


    // Injecting the service into the constructor
    public function __construct(MessageSendingService $msService)
    {
        $this->msService = $msService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->msService->sendTestMessage('From job');
    }
}
