<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GainToTrain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wapi:export {--fromScheduler=no}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fetches all the messages from AllWapiChats table and store them as assistant training json in storage.';

    protected $systemContent = 'You are a virtual sales assistant trained to sell Jewelery tags.';
    protected $fromScheduler = false;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $trainingData = [];

        $this->fromScheduler = $this->option('fromScheduler') === "yes";

        // todo: BAREEKI AALE KAM TYPE FILETERING AND REPLY CONVERSATION SETTING.
        //$messages = GetAllMessages::all();

        return Command::SUCCESS;
    }

    function storeDataInStorage(array $trainingData) {
        $disk = "public";
        $now = Carbon::now("Asia/Kolkata")->timestamp;
        $myContact = config('app.myNumber');
        $filename = "chats_".$myContact."_$now.json";
        // Use loadHtml for flexibility
        $filePath = 'exports/' . $filename;
        Storage::disk($disk)->put($filePath, json_encode($trainingData));
    }
}
