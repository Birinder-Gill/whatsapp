<?php

namespace App\Services\Products;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;
use Nette\NotImplementedException;

class Optical extends ReplyCreationService
{
    function getQueryResponse(string $query): string
    {
        $language = UserLanguage::HINGLISH;
        switch ($language) {
            case UserLanguage::HINGLISH:
                return match ($query) {
                    "UNKNOWN" => '',
                };
                #region Other languages
            case UserLanguage::HINDI:
                return match ($query) {
                };
            case UserLanguage::ENGLISH:
                return match ($query) {
                };
                #endregion
        }

        return '';
    }

    function getLinkMessage(): string
    {
        throw new NotImplementedException();
    }

    function getFirstMessage($personName): array
    {
        $firstMessage = "We’re excited to introduce our new Optical Labels, designed to streamline your inventory management and enhance your store’s organization!

Product Details:
Price :- 20Rs Per Sheet 
Labels Per Sheet:- 40 
Cost Per Label:- 50 Paise
 Minimum Order:- 150 Sheets 

These labels are perfect for writing codes and prices for each product. What’s even better? You can customize your labels with a design of your choice—free of charge!

How to Order:

 1. Choose a design number from our collection. 
 2. Send us your optical store’s name. 

Don’t miss this opportunity to make your product tagging easier and more efficient. For more information or to get started, reply to this email or contact us directly.

Looking forward to helping you enhance your store's organization!

हम आपके लिए पेश करते हैं हमारे नए ऑप्टिकल लेबल्स, जो आपके स्टोर की इन्वेंट्री मैनेजमेंट और संगठन को आसान बनाएंगे!

प्रोडक्ट डिटेल्स:—

 मूल्य: 20 Rs प्रति शीट 
 प्रति शीट लेबल्स: 40 
 प्रति लेबल लागत: लगभग 50 पैसे";

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
            config('app.picThree')
        ];
    }

    function getFirstFollowUp(): string
    {
        throw new NotImplementedException();
    }

    function getContactSaveFollowUp(): string
    {
        throw new NotImplementedException();
    }
}
