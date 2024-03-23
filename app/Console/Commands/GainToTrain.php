<?php

namespace App\Console\Commands;

use App\Models\AllWapiChats;
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
        $this->fromScheduler = $this->option('fromScheduler') === "yes";

        $result = AllWapiChats::where(['type' => 'chat'])->get();
        $grouped = $result->groupBy(function (AllWapiChats $item, int $key) {
            return $item->fromMe ? $item->to : $item->from;
        })->toArray();
        $final = [];
        foreach ($grouped as $key => $value) {
            $oneConvo = [];
            array_push($oneConvo, ['role' => 'system', 'content' => $this->systemContent]);
            foreach ($value as $key => $message) {
                array_push($oneConvo, [
                    'role' => $message["fromMe"] ? "assistant" : "user",
                    "content" => mb_strimwidth($message['message'], 0, 53, '...')
                ]);
            }
            array_push($final, ["messages" => array_values($oneConvo)]);
        }
        $this->storeDataInStorage($final);
        return Command::SUCCESS;
    }

    function storeDataInStorage(array $trainingData)
    {
        $disk = "public";
        $now = Carbon::now("Asia/Kolkata")->timestamp;
        $myContact = config('app.myNumber');
        $filename = "chats_" . $myContact . "_$now.json";
        // Use loadHtml for flexibility
        $filePath = 'exports/' . $filename;
        Storage::disk($disk)->put($filePath, json_encode($trainingData));
    }
}
