<?php

namespace App\Services\Products;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;
use Nette\NotImplementedException;

class TV extends ReplyCreationService
{
    function getQueryResponse(string $query): string
    {
        if (str_contains($query, "ADDRESS_DETECTED")) {
            $output = $this->processString($query);
            if (isset($output['address'])) {
                $address = $output['address'];
            }
            if (isset($output['remainingMessage'])) {
                return $output['remainingMessage'];
            } else {
                return "Order confirm karne ke liye shukriya. You'll get a call from our sales associate.";
            }
        }
        if (str_contains($query, "MEDIA_REQUESTED")) {
            
        }
        return $query;
    }
    /**
     * Function to process the input string and extract address and remaining message.
     *
     * @param string $input
     * @return array
     */
    function processString($input)
    {
        $pattern = '/ADDRESS_DETECTED \[([^\]]+)\]\. ?(.*)/';
        preg_match($pattern, $input, $matches);

        if (count($matches) >= 2) {
            $address = $matches[1];
            $remainingMessage = isset($matches[2]) ? $matches[2] : '';
            return [
                'address' => $address,
                'remainingMessage' => $remainingMessage
            ];
        }

        return [];
    }
    function getLinkMessage(): string
    {
        return "";
    }

    function getFirstMessage($personName): array
    {
        $firstMessage = "Welcome to MKM Enterprise! 🎉

Best Imported TVs for Your Home!

*Choose Your Size:*

32 inch 40 inch & 43 inch: 1080p 📺
50 inch, 55 inch & 65 inch: 4K 📺
*TV Versions:*

Web OS
Standard Smart TV
Android TV
Special Features:

Magic Remote 🪄
Voice Command 🎤
*Prices:*

24 inch: ₹5500
32 inch: ₹8500
40 inch: ₹13000
43 inch: ₹17,000
43 inch 4k: ₹19500
50 inch: ₹25,000
55 inch: ₹32,000
65 inch: ₹52,000
*Why Choose Us?*

1-Year Replacement Guarantee ✅
Pay After Installation 🏠
Visit MKM Enterprise today and find your perfect TV! 🛍️✨

-------------------------------------------------------------

MKM Enterprise में आपका स्वागत है! 🎉

आपके घर के लिए सबसे अच्छे आयातित टीवी!

आकार चुनें:

32 इंच, 40 इंच और 43 इंच: 1080p 📺
50 इंच, 55 इंच और 65 इंच: 4K 📺
टीवी वर्शन:

Web OS
स्टैंडर्ड स्मार्ट टीवी
एंड्रॉइड टीवी
विशेष विशेषताएं:

मैजिक रिमोट 🪄
वॉइस कमांड 🎤
कीमतें:

24 इंच: ₹5500
32 इंच: ₹8500
40 इंच: ₹13000
43 इंच: ₹17,000
43 इंच 4k: ₹19500
50 इंच: ₹25,000
55 इंच: ₹32,000
65 इंच: ₹52,000

1-वर्ष रिप्लेसमेंट गारंटी ✅
इंस्टॉलेशन के बाद भुगतान 🏠
आज ही MKM Enterprise आएं और अपने लिए परफेक्ट टीवी पाएं! 🛍️✨";

        return [
            'message' => $firstMessage,
            'media' =>  config('app.video')
        ];
    }

    function getFirstMedias(): array
    {
        return [
            config('app.picOne'),
            config('app.picTwo'),
            // config('app.picThree')
        ];
    }


    function getFirstFollowUp(): string
    {
        return "";
    }

    function getContactSaveFollowUp(): string
    {
        return "";
    }
}
