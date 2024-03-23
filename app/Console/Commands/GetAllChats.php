<?php

namespace App\Console\Commands;

use App\Models\WapiUser;
use App\Services\MessageSendingService;
use Illuminate\Console\Command;
use App\Services\WhatsAppApiService;
use Illuminate\Support\Facades\Log;

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
            $fromScheduler = false;
            $body = $this->msService->callEndpoint('get-chats');
            $i = 0;
            $count = count($body->data->data);
            $inTable = WapiUser::count();
            $this->logCommand("Wapi gave $count users, We have $inTable users", $fromScheduler);
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
                    $this->logCommand($result->name . " -> " . $result->number, $fromScheduler);
                }
            }
            $this->logCommand("Succesfully added " . $i . " numbers", $fromScheduler, 'info');
            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->logCommand($th->getMessage(), $fromScheduler, 'error');
            return Command::FAILURE;
        }
    }

    private function logCommand(string $message, bool $fromScheduler, string $level = 'line')
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
            "GetAllChats",
            $message,
            $fromScheduler,
            $level
        );
    }
}
