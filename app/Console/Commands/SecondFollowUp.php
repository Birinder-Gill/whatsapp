<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Services\ReplyCreationService;
use App\Services\WhatsAppApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SecondFollowUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'second:followUp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected WhatsAppApiService $apiService;
    protected ReplyCreationService $rcService;

    // Inject the WhatsAppApiService into the command
    public function __construct(WhatsAppApiService $apiService, ReplyCreationService $rcService)
    {
        parent::__construct();
        $this->apiService = $apiService;
        $this->rcService = $rcService;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conversations = Conversation::whereDate('last_message_at', '=', Carbon::yesterday('Asia/Kolkata'))
        ->where('followUpCount', 1)
        ->get();
        foreach ($conversations as $conversation) {
            switch ($conversation->status) {
                case 'yes':
                case 'no':
                    $this->sendContactSaveFollowUp($conversation);
                    break;
                default:
                    $this->sendFollowUp($conversation);
                    break;
            }

            $conversation->followUpCount = 2;
            $conversation->save();
        }
        return Command::SUCCESS;
    }
    function sendContactSaveFollowUp($conversation)
    {
        $this->apiService->sendWhatsAppMessage($conversation->from, $this->rcService->getContactSaveFollowUp());
        $this->apiService->sendGemCraftVCard($conversation->from);
        $conversation->delete();
    }

    function sendFollowUp($conversation)
    {
        $this->apiService->sendWhatsAppMessage($conversation->from, $this->rcService->getFirstFollowUp());
    }
}
