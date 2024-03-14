<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\KillSwitch;
use App\Models\MessageLog;
use App\Models\OpenAiThread;
use App\Models\WhatsAppLead;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AboutMyNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'about:me {number? : The phone number you want to know about.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows all the information about a number.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $argument = $this->argument('number') ?? '7009154010';
        $number =  '91' . $argument . '@c.us';
        $this->line('');
        $this->line('===============================');
        $this->info("ABOUT: " . $number);
        $this->line('===============================');
        $this->line('');

        $query = WhatsAppMessage::where("from", $number);
        if ($query->exists()) {
            $result = $query->orderBy('counter', 'desc')->first();
            $message = $result->messageText;
            if (strlen($message) > 15) {
                $message = substr($message, 0, 15) . '...';
            }
            $this->info($result->displayName . " has counter " . $result->counter . " with last message " . $message . " at " . Carbon::parse($result->created_at)->format('M d, H:i'));
            $this->line('');

            $query = WhatsAppLead::where("from", $number);
            if ($query->exists()) {
                $result = $query->first();
                $this->info($result->from . " is a " .(( $result->hotLead) ? "hot" : "cold") . " lead and info is " . $result->infoSent ? "" : "not " . "sent with latest activity "  . " at " . Carbon::parse($result->created_at)->format('M d, H:i'));
                $this->line('');
            }

            $query = MessageLog::where("from", $number);
            if ($query->exists()) {
                $count = $query->count();
                $result = $query->orderBy('counter', 'desc')->get();
                $this->info("Message log for " . $number . " has " . $count . " message" .( ($count > 1) ? "s" : "") . " and biggest counter is " . $result->first()->counter . " and last message is created at " . Carbon::parse($result->first()->created_at)->format('M d, H:i'));
                foreach ($result as $key => $value) {
                    $this->line($value->messageText);
                    $this->line('------------------------------------------------------');
                    $this->line('');
                }

            }


            $query = OpenAiThread::where("from", $number);
            if ($query->exists()) {
                $result = $query->first();
                $this->info("OpenAiThread is created at " . Carbon::parse($result->created_at)->format('M d, H:i'));
                $this->line('');
            }


            $query = KillSwitch::where("from", $number);
            if ($query->exists()) {
                $result = $query->first();
                $this->info("KillSwitch is activated by ".($result->kill_message??"-/-")." at " . Carbon::parse($result->created_at)->format('M d, H:i'));
                $this->line('');
            }else{
                $this->info("Kill switch does not exist.");
            }


            $query = Conversation::where("from", $number);
            if ($query->exists()) {
                $result = $query->first();
                $this->info("Followup conversation for ".$number." is created and Follow up count is ".($result->followUpCount).", status is ".$result->status." and last message was created at " . Carbon::parse($result->last_message_at)->format('M d, H:i'));
                $this->line('');
            }
            $this->line('');
            $this->line('===============================');
            $this->info("X=============================X:");
            $this->line('===============================');
            $this->line('');
            return Command::SUCCESS;
        }
    }
}
