<?php

namespace App\Console\Commands;

use App\Models\AllWapiChats;
use App\Models\WapiUser;
use App\Services\MessageSendingService;
use Illuminate\Console\Command;
use App\Services\WhatsAppApiService;
use Carbon\Carbon;

class GetAllMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wapi:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loops through WapiUsers table and fetch and store all the messages in AllWapiChats';

    protected MessageSendingService $msService;
    protected $fromScheduler = false;
    public function __construct(MessageSendingService $msService)
    {
        parent::__construct();
        $this->msService = $msService;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    // public function handle()
    // {
    //     try {
    //         $all = WapiUser::where('messagesFetched', false)->get();
    //         $all->each(function ($value, $key) {
    //             $body = $this->msService->callEndpoint("fetch-messages", ["chatId" => $value->chatId]);
    //             $entries = [];
    //             $count = count($body->data->data);
    //             foreach ($body->data->data as $key => $message) {
    //                 $messageId = $message->message->id->_serialized;
    //                 $entry = AllWapiChats::updateOrCreate([
    //                     "messageId" => $messageId
    //                 ], [
    //                     "from" => $message->message->from,
    //                     "messageId" => $messageId,
    //                     "message" => $message->message->type === "chat" ? $message->message->body : $message->message->type,
    //                     "type" => $message->message->type,
    //                     "to" => $message->message->to,
    //                     "fromMe" => $message->message->id->fromMe,
    //                     "messageTime" => Carbon::createFromTimestamp($message->message->timestamp, 'Asia/Kolkata'),
    //                 ]);
    //                 $entries[$messageId] = $entry->message;
    //             }
    //             if (count($entries) === $count) {
    //                 WapiUser::where("chatId", $message->message->from)->update([
    //                     "messagesFetched" => true
    //                 ]);
    //             }

    //             $this->line("Added " . count($entries) . " messages for " . $value->name ?? $value->number);
    //         });
    //         return Command::SUCCESS;
    //     } catch (\Throwable $th) {
    //         $this->error($th->getMessage());
    //         return Command::FAILURE;
    //     }
    // }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // $all = WapiUser::where('chatId', '919326062015@c.us')->get();
            // if(true){

            $query = WapiUser::where('messagesFetched', false);
            if ($query->exists()) {
                $all = $query->orderBy('id', 'desc')->get();

                $all->each(function ($value, $key) {
                    $this->logCommand("Current user: " . ($value->name ?? $value->number), 'comment');
                    $query = AllWapiChats::where('from',  $value->chatId)->orWhere('to',  $value->chatId);
                    if ($query->exists()) {
                        $this->logCommand("Already found " . ($query->count()) . " messages in table.");
                    }

                    $this->retry(function () use ($value) {
                        $body = $this->msService->callEndpoint("fetch-messages", ["chatId" => $value->chatId]);
                        $entries = [];
                        $count = count($body->data->data);
                        foreach ($body->data->data as $key => $message) {
                            $messageId = $message->message->id->_serialized;
                            $entry = AllWapiChats::updateOrCreate([
                                "messageId" => $messageId
                            ], [
                                "from" => $message->message->from,
                                "messageId" => $messageId,
                                "message" => $message->message->type === "chat" ? $message->message->body : $message->message->type,
                                "type" => $message->message->type,
                                "to" => $message->message->to,
                                "fromMe" => $message->message->id->fromMe,
                                "messageTime" => Carbon::createFromTimestamp($message->message->timestamp, 'Asia/Kolkata'),
                            ]);
                            if ($entry) {
                                $entries[$messageId] = $entry->message;
                            }
                        }

                        $updateData = [
                            "messagesAdded" => count($entries)
                        ];
                        $allDone = (count($entries) === $count);

                        if ($allDone) {
                            $updateData["messagesFetched"] = true;
                        }
                        WapiUser::where("chatId", $value->chatId)->update($updateData);

                        $info = "Added " . count($entries) . " messages. Api count: $count. Final row count in table: " . (AllWapiChats::where('from',  $value->chatId)->orWhere('to',  $value->chatId)->count());
                        if ($allDone) {
                            $this->logCommand($info, 'info');
                        } else {
                            $this->logCommand($info, 'error');
                        }
                        $this->logCommand("");
                    });
                });
                $this->logCommand("All chats are done.", 'info');
            }
            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->logCommand($th->getMessage().' at '.$th->getLine(), 'error');
            return Command::FAILURE;
        }
    }

    private function logCommand(string $message, string $level = 'info')
    {
        switch ($level) {
            case 'info':
                $this->info($message);
                break;
            case 'line':
                $this->line($message);
                break;
            case 'error':
                $this->error($message);
                break;
            case 'comment':
                $this->comment($message);
                break;
            default:
                # code...
                break;
        }
        commandLog(
            "GetAllMessages",
            $message,
            $this->fromScheduler,
            $level
        );
    }

    private function retry($callback, $maxAttempts = 3, $waitMilliseconds = 5000)
    {
        return retry($maxAttempts, function () use ($callback) {
            try {
                $callback();
            } catch (\Throwable $e) {
                // Log the error or handle it accordingly
                $this->logCommand($e->getMessage(), 'error');
                throw $e; // Rethrow the exception to retry
            }
        }, $waitMilliseconds);
    }
}
