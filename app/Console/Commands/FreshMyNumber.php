<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\KillSwitch;
use App\Models\MessageLog;
use App\Models\OpenAiThread;
use App\Models\WhatsAppLead;
use App\Models\WhatsAppMessage;
use Illuminate\Console\Command;

class FreshMyNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fresh:me {number? : The phone number you want to freshen.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a number fresh i.e. new again.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $argument = $this->argument('number') ?? '7009154010';
            $number =  '91' . $argument . '@c.us';
            $this->info("Freshing up " . $number);
            $this->info("..........................................................");

            $deleted = WhatsAppMessage::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " WhatsAppMessage entries");

            $deleted = WhatsAppLead::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " WhatsAppLead entries");

            $deleted = OpenAiThread::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " OpenAiThread entries");

            $deleted = MessageLog::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " MessageLog entries");

            $deleted = KillSwitch::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " KillSwitch entries");

            $deleted = Conversation::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " Conversation entries");

            $this->info("Sucessfully freshened up " . $number);
        } catch (\Throwable $th) {
            $this->info($th->getMessage());
        }


        return Command::SUCCESS;
    }
}
