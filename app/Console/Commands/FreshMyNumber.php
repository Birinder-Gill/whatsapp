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
            $total = 0;
            $deleted = WhatsAppMessage::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " WhatsAppMessage entries");
            $total+=$deleted;

            $deleted = WhatsAppLead::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " WhatsAppLead entries");
            $total+=$deleted;

            $deleted = OpenAiThread::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " OpenAiThread entries");
            $total+=$deleted;

            $deleted = MessageLog::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " MessageLog entries");
            $total+=$deleted;

            $deleted = KillSwitch::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " KillSwitch entries");
            $total+=$deleted;

            $deleted = Conversation::where('from', $number)->delete();
            if ($deleted) $this->info("Deleted " . $deleted . " Conversation entries");
            $total+=$deleted;

            if($total)
            {$this->info("Sucessfully freshened up " . $number);}else{
                $this->info("$argument is already a fresh number.");
            }
        } catch (\Throwable $th) {
            $this->info($th->getMessage());
        }


        return Command::SUCCESS;
    }
}
