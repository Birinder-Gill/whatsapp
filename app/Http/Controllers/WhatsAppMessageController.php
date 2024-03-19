<?php

namespace App\Http\Controllers;

use App\Services\MessageSendingService;
use App\Services\OpenAiAnalysisService;
use Barryvdh\Snappy\Facades\SnappyImage;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

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

    function sendMessage(Request $request)
    {
        dd(WhatsAppMessageController::class . " sendMessage");
        $body = "prod sirra \n\n\nbc ";
        $response = $this->msService->sendTestMessage($body);
        return json_decode($response->getBody());
    }

    function mickeyCalling(Request $request)
    {
        $data = request()->json()->all()['data']['message']['_data'];
        $message = $data['body'];
        $assistant = $this->aiService->createAndRun($message, "asst_sgHG5GtlW0UWg4z2zZqzvC1W");
        $this->msService->sendOpenAiResponse($assistant);
    }

    function testReceived(Request $request)
    {
        // try {
        //     $data = request()->json()->all()['data']['message']['_data'];
        //     $message = $data['body'];
        //     $assistant = $this->aiService->createAndRun($message,"asst_mHv2bINmV0mvMa3rDBTA2q2t");
        //     $this->msService->sendOpenAiResponse($assistant);
        //     // $query = $this->aiService->queryDetection($message,);
        //     // $this->msService->giveQueryResponse($query);
        // } catch (\Throwable $th) {
        //    report($th);
        // }

    }

    public function generateImage(Request $request)
    {
        // $snappy = App::make('snappy.pdf');
        // //To file
        // $html = view('greeting', compact('user'))->render();
        // $snappy->generateFromHtml($html, '/tmp/bill-123.pdf');
        // $snappy->generate('http://www.github.com', '/tmp/github.pdf');
        // //Or output:
        // return new Response(
        //     $snappy->getOutputFromHtml($html),
        //     200,
        //     array(
        //         'Content-Type'          => 'application/pdf',
        //         'Content-Disposition'   => 'attachment; filename="file.pdf"'
        //     )
        // );
        // return view('greeting');
        // dd(public_path('storage/temp'));
        $pdf = SnappyImage::loadView('greeting')->setOption('width', '920')->setOption('height', '139');
        // dd($pdf);
        // return $pdf->inline();
        $mediaUrl = generateAndStoreImage($pdf);
        // $response = $this->msService->sendTestMedia($mediaUrl);
        // deleteStoredImage($mediaUrl);
        return response('URL',200,["URL"=>$mediaUrl]);
    }

    function messageReceived(Request $request)
    {
        try {
            $data = request()->json()->all()['data']['message']['_data'];
            $message = $data['body'];
            $personName = $data['notifyName'];
            $from = $data['from'];
            $hash = $data['id']['_serialized'];

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
                    'threadId' => $this->aiService->getThreadId()
                ];
                incrementCounter($logArray);

                if ($messageNumber === 0) {
                    $this->msService->deleteMessage($hash);
                    $this->msService->sendFirstMessage($personName);
                } else {
                    $query = $this->aiService->queryDetection($message);
                    $this->msService->giveQueryResponse($query, $messageNumber == 1);
                }
            }
        } catch (\Throwable $e) {
            report($e);
            // $this->msService->sendTestMessage($e->getFile().'-> '.$e->getLine());

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
