<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Services\WhatsAppApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FollowUpConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:followUp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $followUp1 = 'Pehla Follow up';

    protected WhatsAppApiService $apiService;

    // Inject the WhatsAppApiService into the command
    public function __construct(WhatsAppApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conversations = Conversation::where('last_message_at', '<=', Carbon::now('Asia/Kolkata')->subMinutes(4))
            ->where('followUpCount', 0)
            ->get();
        foreach ($conversations as $conversation) {
            switch ($conversation->status) {
                //TODO:: MAKE FOLLOWUP LOGIC
                case 'yes':
                    break;
                case 'no':
                    break;
                default:
                $this->sendFollowUp($conversation);
                    break;
            }

            $conversation->followUpCount = 1;
            $conversation->save();
        }
        return Command::SUCCESS;
    }
    function sendFollowUp($conversation) {
        $this->apiService->sendWhatsAppMessage($conversation->from,$this->followUp1 );
    }
}
