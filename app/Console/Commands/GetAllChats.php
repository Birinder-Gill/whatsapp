<?php

namespace App\Console\Commands;

use App\Models\WapiUser;
use App\Services\MessageSendingService;
use Illuminate\Console\Command;
use App\Services\WhatsAppApiService;



class GetAllChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wapi:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all chat users and store them in WapiUser table.';


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
    public function handle()
    {
        try {
            $body = $this->msService->callEndpoint('get-chats');
            $i = 0;
            $count = count($body->data->data);
            foreach ($body->data->data as $key => $user) {
                $i++;
                $number =  $user->id->user;
                if (!(WapiUser::where("number", $number)->exists())) {
                    $result = WapiUser::create(
                        [
                            "number" => $number,
                            "chatId" => $user->id->_serialized,
                            "isGroup" => $user->isGroup,
                            "name" => $user->name,
                            "lastMessage" => $user->lastMessage->body,
                        ]
                    );
                    $this->line($result->name . " -> " . $result->number);
                }
            }
            $this->info("Succesfully added " . $i . " numbers");
            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            return Command::FAILURE;
        }
    }
}
