<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\MessageLog;
use App\Models\WhatsAppLead;
use App\Models\WhatsAppMessage;
use App\Services\ReplyCreationService;
use App\Services\WhatsAppApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

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
        $conversations = Conversation::where('last_message_at', '<=', Carbon::now('Asia/Kolkata')->subMinutes(4))
            ->where('followUpCount', 0)
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

            $conversation->followUpCount = 1;
            $conversation->save();
        }
        return Command::SUCCESS;
    }

    function sendContactSaveFollowUp($conversation)
    {
        $this->apiService->sendWhatsAppMessage($conversation->from, $this->rcService->getContactSaveFollowUp());
        $this->apiService->sendGemCraftVCard($conversation->from,  "Gem", "Craft");
        $conversation->delete();
    }

    function sendFollowUp($conversation)
    {
        switch (config('app.product')) {
            case 'Tags':
                $this->leadSystem($conversation);
                break;
            case 'Lens':
                $this->lensFollowUp($conversation);
                break;

            default:
                # code...
                break;
        }
    }

    function lensFollowUp($conversation)
    {
        $this->apiService->sendWhatsAppMessage($conversation->from, $this->rcService->getFirstFollowUp());
    }


    function leadSystem($conversation)
    {
        // Log::info("CHAL AYA TA SAHI");
        try {
            $lead = WhatsAppLead::where('from', $conversation->from)->first();
            if ($lead) {
                if ($lead->hotLead == 1) {
                    $messages = MessageLog::where('from', $conversation->from)->get();
                    if ($messages->isNotEmpty()) {
                        $content = '*From:* ' . substr(explode("@", $conversation->from)[0], -10) . "\n";
                        $content = $content . '*Name: ' . "Birinder Gill*\n" . "--------";
                        $content = $content . "\nMessages:\n\n";
                        foreach ($messages as $message) {
                            $content =  $content . "\n```" . $message->fromMe?"Reply:":"Message:" . "```\n" . $message->messageText."\n";
                        }
                        $this->apiService->sendWhatsAppMessage(config('app.myNumber'), $content);
                    }
                }
            }
        } catch (\Throwable $th) {
            // Log::info("FOLLOWUP");
            report($th);
        }
    }

    function tagFollowUp($conversation)
    {

        $lead = WhatsAppLead::where('from', $conversation->from)->first();
        if ($lead) {
            if ($lead->hotLead == 0) {
                //This is the followup only for cold leads
                $this->apiService->sendWhatsAppMessage($conversation->from, $this->rcService->getFirstFollowUp());
            } else {
                $messages = WhatsAppMessage::where('from', $conversation->from)->get();
                $content = 'From: ' . substr(explode("@", $conversation->from)[0], -10) . "\n\nMessages:\n\n";
                foreach ($messages as $message) {
                    $content =  Carbon::parse($message->created_at)->format('Y-m-d H:i:s') . "\n" . $content . $message->messageText . "\n------------\n";
                }
                $this->apiService->sendWhatsAppMessage(config('app.myNumber'), $content);
            }
        }
    }
}
