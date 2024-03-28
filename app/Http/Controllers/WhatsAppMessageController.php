<?php

namespace App\Http\Controllers;

use App\Models\AllWapiChats;
use App\Models\WapiUser;
use App\Services\MessageSendingService;
use App\Services\OpenAiAnalysisService;
use Barryvdh\Snappy\Facades\SnappyImage;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use Illuminate\Support\Facades\File;

class WhatsAppMessageController extends Controller
{
    protected $to = '917009154010@c.us';
    protected MessageSendingService $msService;
    protected OpenAiAnalysisService $aiService;

    public function __construct(MessageSendingService $msService, OpenAiAnalysisService $aiService)
    {
        $this->msService = $msService;
        $this->aiService = $aiService;
    }

    function orderReceived(Request $request)
    {
        $phone =  $order['phone'] ?? $order['customer']['phone'] ?? $order['billing_address']['phone'] ?? $order['shipping_address']['phone'] ?? null;
        if ($phone) {
            updateStatus(formatPhoneNumber($phone), 'yes');
        }
    }

    function fetchMessages()
    {
        $all = WapiUser::all();
        $all->each(function ($value, $key) {
        });
    }

    function sendMessage(Request $request)
    {
        $body = "prod sirra \n\n\nbc ";
        $response = $this->msService->sendTestMessage($body);
        return json_decode($response->getBody());
    }

    function mickeyCalling(Request $request)
    {
        $data = request()->json()->all()['data']['message']['_data'];
        $message = $data['body'];
        $from = $data['from'];
        $assistant = $this->aiService->createAndRun($message, "asst_sgHG5GtlW0UWg4z2zZqzvC1W");
        $this->msService->sendOpenAiResponse($assistant, $from);
    }

    function getAiService(Request $request) {
        $serviceFilePath = app_path('Services/OpenAiAnalysisService.php');

        if (!File::exists($serviceFilePath)) {
            return response()->json(['error' => 'Service file does not exist.'], 404);
        }

        $contents = File::get($serviceFilePath);

        // Return the contents as a plain text response
        return response($contents)->header('Content-Type', 'text/plain');
    }

    function testReceived(Request $request)
    {

        $result = AllWapiChats::where(['type' => 'chat'])->get();
        $grouped = $result->groupBy(function (AllWapiChats $item, int $key) {
            return $item->fromMe ? $item->to : $item->from;
        })->toArray();
        $final = [];
        foreach ($grouped as $key => $value) {
            $oneConvo = ['role' => 'system', 'content' => ''];
            foreach ($value as $key => $message) {
                array_push($oneConvo, [
                    'role' => $message["fromMe"] ? "assistant" : "user",
                    "content" => $message['message']
                ]);
            }
            array_push($final, $oneConvo);
        }
        dd($final);
    }

    public function generateImage(Request $request)
    {
        if (!$request->image)
            return view('greeting');
        $image = SnappyImage::loadView('greeting')->setOption('width', '920')->setOption('height', '139');
        $pMedia = generateAndStoreImage($image);
        $this->msService->sendTestMedia($pMedia);
        deleteStoredFile($pMedia);



        $pdf = SnappyPdf::loadView('greeting');
        $iMedia = generateAndStorePdf($pdf);
        $this->msService->sendTestMedia($iMedia);
        deleteStoredFile($iMedia);

        return response("Image => $iMedia <br> Pdf => $pMedia", 200, [
            "image" => $iMedia,
            "pdf" => $pMedia
        ]);
    }

    function messageReceived(Request $request)
    {
        try {
            $data = request()->json()->all()['data']['message']['_data'];
            $message = $data['body'];
            $personName = $data['notifyName'];
            $from = $data['from'];
            $hash = $data['id']['_serialized'];

            $gptActive = $this->aiService->initialise($from);
            if ($gptActive) {
                $messageNumber = detectManualMessage($from, $message);
                if ($messageNumber > -1) {
                    $logArray = [
                        'from' => $from,
                        'displayName' => $personName,
                        'to' => $data['to'],
                        'counter' => $messageNumber + 1,
                        'messageText' => $message,
                        'messageId' => $data['id']['id'],
                        'messageHash' => $hash,
                    ];
                    incrementCounter($logArray);
                    if ($messageNumber === 0) {
                        $this->msService->deleteMessage($hash);
                        $this->msService->sendFirstMessage($personName, $from);
                    } else {
                        $query = $this->aiService->queryDetection($message);
                        $this->msService->giveQueryResponse($query, $from, $messageNumber == 1);
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    function runATest($from)
    {
        $message = "https://api.whatsapp.com/send?phone=" . substr($from, 2, 10) . "&text=Hello, How may I help you";
        $this->msService->sendTestMessage($message);
    }

    function sendMediaApi(Request $request)
    {
        $mediaUrl = 'https://drive.google.com/file/d/1sP3zfH4nIznkGX65Jtbh6WhQd1X0WWPS/view?usp=sharing';
        $to =  '917009154010@c.us';
        $response = $this->msService->sendTestMedia($mediaUrl);
        return $response->getBody();
    }

    /**
     * if ($useOpenAi) {  ALWAYS FALSE
     * assistant = $this->aiService->createAndRun($message);
     * this->msService->sendOpenAiResponse($assistant);
     * else
     *     function makeSubs(Request $request)
     *   {
     *       // Generate and output the SRT content
     *       $srtContent = $this->generateSRTContent();
     *       return $srtContent;
     *   }
     *
     *   function generateSRTContent()
     *   {
     *       // Set the initial timestamp using Carbon
     *       $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', '2024-02-18 16:00:24');
     *       $rt = Carbon::createFromFormat('Y-m-d H:i:s', '2024-02-18 00:00:00');
     *
     *       // Generate the SRT content
     *       $content = "1<br>";
     *       $content .= $this->formatRT($rt) . " --> " . $this->formatRT($rt->addSeconds(1)) . "<br>";
     *       $content .= "18 February 2024" . "<br>";
     *       $content .= $this->formatTime($timestamp->addSeconds(1)) . "<br>";
     *       $content .= "3715 NB-145, Shediac Bridge, NB E4R 1R9, Canada<br><br>";
     *
     *
     *       // Loop for 10 more seconds
     *       for ($i = 2; $i <= 3747; $i++) {
     *           $content .= $i . "<br>";
     *           $content .= $this->formatRT($rt) . " --> " . $this->formatRT($rt->addSeconds(1)) . "<br>";
     *           $content .= "18 February 2024" . "<br>";
     *           $content .= $this->formatTime($timestamp->addSeconds(1)) . "<br>";
     *           $content .= "3715 NB-145, Shediac Bridge, NB E4R 1R9, Canada<br><br>";
     *
     *       }
     *
     *       return $content;
     *   }
     *
     *   function formatTime($timestamp)
     *   {
     *       return $timestamp->format("g:i:s A");
     *   }
     *   function formatRT($timestamp) {
     *       return $timestamp->format("H:i:s") . ",000";
     *   }
     *
     *
     */
}
