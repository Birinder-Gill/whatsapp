<?php

namespace App\Services;

use App\Enums\GeneralQuery;
use App\Enums\PriceQuery;
use App\Enums\UserLanguage;
use Illuminate\Http\Request;

class ReplyCreationService
{
    protected $from;

    public function __construct(Request $request)
    {
        try {
            $this->from =  request()->json()->all()['data']['message']['_data']['from'];
        } catch (\Throwable $th) {

        }
    }
    function getFrom()
    {
        // return "917009154010@c.us";
        return $this->from;
    }

    function getQueryResponse(GeneralQuery $query): string
    {
        $language = $this->getUserLanguage();
        switch ($language) {
            case UserLanguage::HINDI:
                return match ($query) {
                    GeneralQuery::ADDRESS => '',
                    GeneralQuery::MORE_DETAILS => '',
                    GeneralQuery::USE_CASE => '',
                    GeneralQuery::DELIVERY_WAY => '',
                    GeneralQuery::DELIVERY_TIME => '',
                    GeneralQuery::PINCODE_AVAILABILITY => '',
                    GeneralQuery::FOLLOW_UP_GIVEN_BY_USER => '',
                    GeneralQuery::OK => '',
                };
            case UserLanguage::HINGLISH:
                return match ($query) {
                    GeneralQuery::ADDRESS => 'Sir we have 2 branches, one in sector 35, chandigarh & one in pragati maidan, delhi
                    सर हमारी 2 ब्रांचेज हैं, एक सेक्टर 35 चंडीगढ़ में और दूसरी प्रगति मैदान दिल्ली में',

                    GeneralQuery::MORE_DETAILS => 'Sir kisi bhi jaan kari ke liye aap is number pe WhatsApp call kr skte hain',

                    GeneralQuery::USE_CASE => '',
                    GeneralQuery::DELIVERY_WAY => 'Sir Via courier aati hai ji delivery.3-4 days mein aapke store pe deliver hojega',
                    GeneralQuery::DELIVERY_TIME => 'Sir Via courier aati hai ji delivery. 3-4 days mein aapke store pe deliver ho jayega',
                    GeneralQuery::PINCODE_AVAILABILITY => '',
                    GeneralQuery::FOLLOW_UP_GIVEN_BY_USER => '',
                    GeneralQuery::OK => 'Thanks for the response sir. Aap niche diye gye link se order kar sakte hain.

                    https://7639cd.myshopify.com/products/jarlink-2-pack-jewelry-loupes'};
            case UserLanguage::ENGLISH:
                return match ($query) {
                    GeneralQuery::ADDRESS => '',
                    GeneralQuery::MORE_DETAILS => '',
                    GeneralQuery::USE_CASE => '',
                    GeneralQuery::DELIVERY_WAY => '',
                    GeneralQuery::DELIVERY_TIME => '',
                    GeneralQuery::PINCODE_AVAILABILITY => '',
                    GeneralQuery::FOLLOW_UP_GIVEN_BY_USER => '',
                    GeneralQuery::OK => '',
                };
        }
    }

    function getFirstMessage($personName): string
    {


         return "Hi, " . $personName . ", \nThank you for your interest in our Eye Loupe Magnifier lens for jewelers! Our LED magnifying glass is great for looking closely at jewelry details, helping jewelers see small things better, like gemstones and delicate pieces.

         If you want to buy one, just say \"Interested,\" and we'll guide you through the process. This fantastic tool is currently available at a discounted price of 990 rupees for a limited time. If you have any questions, feel free to ask here on WhatsApp.

         हमारे Eye Loupe Magnifier लेंस के लिए आपकी रुचि के लिए धन्यवाद! हमारा LED magnifying glass ज्वेलरी के छोटे विवरणों को ध्यान से देखने के लिए बहुत उपयुक्त है, जो ज्वेलर्स को जेमस्टोन्स और नाजुक टुकड़ों को बेहतर तरीके से देखने में मदद करता है।

         यदि आप एक खरीददारी करना चाहते हैं, तो बस \"Interested\" कहें, और हम आपको प्रक्रिया के माध्यम से मार्गदर्शन करेंगे। यह शानदार टूल अब एक सीमित समय के लिए 990 रुपये में उपलब्ध है। अगर आपके पास कोई सवाल है, तो WhatsApp पर यहां पूछें।";
    }

    function getPriceDiscussion(PriceQuery $priceQuery): string
    {
        $language = $this->getUserLanguage();
        switch ($language) {
            case UserLanguage::HINDI:
                return match ($priceQuery) {
                    PriceQuery::HIGH_AS_COMPARED => 'Your order is pending.',
                    PriceQuery::HIGH_IN_GENERAL => 'Your order is being processed.',
                    PriceQuery::WHOLESALE => 'Your order has been completed.',
                };
            case UserLanguage::HINGLISH:
                return match ($priceQuery) {
                    PriceQuery::HIGH_AS_COMPARED => '',
                    PriceQuery::HIGH_IN_GENERAL => '',
                    PriceQuery::WHOLESALE => '',
                };
            case UserLanguage::ENGLISH:
                return match ($priceQuery) {
                    PriceQuery::HIGH_AS_COMPARED => 'Your order is pending.',
                    PriceQuery::HIGH_IN_GENERAL => 'Your order is being processed.',
                    PriceQuery::WHOLESALE => 'Your order has been completed.',
                };
        }
    }

    function getDiscountedPriceMessage(): string
    {
        $language = $this->getUserLanguage();

        return match ($language) {
            UserLanguage::HINDI => 'Your order is pending.',
            UserLanguage::HINGLISH => 'Sir, Ye lens sirf 990/- ke discounted price pe milega',
            UserLanguage::ENGLISH => 'Your order has been completed.',
        };
    }

    //ORDER CONFIRMATION
    function createOrderConfirmation()
    {
        $language = $this->getUserLanguage();

        return match ($language) {
            UserLanguage::HINDI => 'Your order is pending.',
            UserLanguage::HINGLISH => '',
            UserLanguage::ENGLISH => 'Your order has been completed.',
        };
    }

    function getUserLanguage(): UserLanguage
    {
        return UserLanguage::HINGLISH;
    }
}
