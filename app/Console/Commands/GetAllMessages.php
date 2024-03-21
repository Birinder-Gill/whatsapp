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

    public function handle()
    {
        try {
            $all = WapiUser::where('messagesFetched', false)->get();
            $all->each(function ($value, $key) {
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

                        $entries[$messageId] = $entry->message;
                    }

                    if (count($entries) === $count) {
                        WapiUser::where("chatId", $message->message->from)->update([
                            "messagesFetched" => true
                        ]);
                    }

                    $this->line("Added " . count($entries) . " messages for " . $value->name ?? $value->number);
                });
            });

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            return Command::FAILURE;
        }
    }

    private function retry($callback, $maxAttempts = 3, $waitMilliseconds = 5000)
    {
        return retry($maxAttempts, function () use ($callback) {
            try {
                $callback();
            } catch (\Throwable $e) {
                // Log the error or handle it accordingly
                $this->error("Error occurred: " . $e->getMessage());
                throw $e; // Rethrow the exception to retry
            }
        }, $waitMilliseconds);
    }


}
