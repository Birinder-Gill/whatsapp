<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Services\ReplyCreationService;
use App\Services\WhatsAppApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ContactSaveFollowUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        $conversations = Conversation::whereDate('last_message_at', '=', Carbon::yesterday('Asia/Kolkata')->subDay())
            ->where('followUpCount', 2)
            ->get();

        foreach ($conversations as $conversation) {
            $this->sendContactSaveFollowUp($conversation);
            $conversation->followUpCount = 3;
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
}
