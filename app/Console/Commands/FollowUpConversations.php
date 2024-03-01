<?php

namespace App\Console\Commands;

use App\Models\Conversation;
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Conversation::create([
            'from'=>'917009154010@c.us',
            'last_message_at' => Carbon::now(),
            'fromMe' => true,
        ]);
        return Command::SUCCESS;
    }
}
