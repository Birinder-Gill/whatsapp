<?php

namespace App\Console\Commands;

use App\Models\LeadRecord;
use App\Models\MessageLog;
use App\Models\WhatsAppLead;
use App\Services\WhatsAppApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LeadSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';



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
        $leads = LeadRecord::where('last_message_at', '<=', Carbon::now('Asia/Kolkata')->subMinutes(4))
            ->where('leadSent', false)
            ->get();
        foreach ($leads as $lead) {
            $this->makeAndSendContent($lead);
            $lead->followUpCount = 1;
            $lead->save();
        }
        return Command::SUCCESS;
    }

    function makeAndSendContent($lead)
    {
        $messages = MessageLog::where('from', $lead->from)->get();
        if ($messages->isNotEmpty()) {
            $content = '*From:* ' . substr(explode("@", $lead->from)[0], -10) . "\n";
            $content = $content . '*Name: ' . ($messages->first()->displayName) . "*\n" . "------------------------";
            $content = $content . "\n\n";
            foreach ($messages as $message) {
                $content =  $content . "\n*" . (($message->fromMe) ? "Reply:" : "Message:") . "*\n```" . $message->messageText . (($message->fromMe) ? "```\n\n" : "```");
            }
            $this->apiService->sendWhatsAppMessage(config('app.myNumber'), $content);
        }
    }
}
