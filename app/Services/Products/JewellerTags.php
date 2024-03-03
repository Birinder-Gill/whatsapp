<?php

namespace App\Services\Products;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;

class JewellerTags extends ReplyCreationService
{
    function getQueryResponse(string $query): string
    {

        $language = UserLanguage::HINGLISH;
        switch ($language) {
            case UserLanguage::HINGLISH:
                return match ($query) {
                    "HIGH_IN_GENERAL" => '',
                    "PRICE" => '',
                    "DISCOUNT" => '',
                    "ADDRESS" => 'Sir we have 2 branches, one in sector 35, chandigarh & one in pragati maidan, delhi\nसर हमारी 2 ब्रांचेज हैं, एक सेक्टर 35 चंडीगढ़ में और दूसरी प्रगति मैदान दिल्ली में',
                    "DELIVERY_WAY" =>'Sir Via courier aati hai ji delivery.7-10 days mein tags print hojaate hain & 3-4 days mein aapke store pe deliver',
                    "DELIVERY_TIME" =>'Sir Via courier aati hai ji delivery.7-10 days mein tags print hojaate hain & 3-4 days mein aapke store pe deliver',
                    "PINCODE_AVAILABILITY" => '',
                    "FOLLOW_UP_GIVEN_BY_USER" => '',
                    "OK" =>'Thanks for the response sir.For more information Kindly contact 9872997978',
                    "MINIMUM_QUANTITY" => '',
                    "TOTAL_PRICE" => '',
                    "Size" => '',
                    "CASH_ON_DELIVERY" => '',
                    "PAYMENT_METHOD" => '',
                    "QUANTITY_OBJECTION" => '',
                    "UNKNOWN" => '',
                };
                #region Other languages
            case UserLanguage::HINDI:
                return match ($query) {
                    // "HIGH_IN_GENERAL" => '',
                    // "PRICE" => '',
                    // "DISCOUNT" => '',
                    // "ADDRESS" => '',
                    // "DELIVERY_WAY" => '',
                    // "DELIVERY_TIME" => '',
                    // "PINCODE_AVAILABILITY" => '',
                    // "FOLLOW_UP_GIVEN_BY_USER" => '',
                    // "OK" => '',
                    // "MINIMUM_QUANTITY" => '',
                    // "TOTAL_PRICE" => '',
                    // "Size" => '',
                    // "CASH_ON_DELIVERY" => '',
                    // "PAYMENT_METHOD" => '',
                    // "QUANTITY_OBJECTION" => '',
                    // "UNKNOWN" => '',
                };
            case UserLanguage::ENGLISH:
                return match ($query) {
                    // "HIGH_IN_GENERAL" => '',
                    // "PRICE" => '',
                    // "DISCOUNT" => '',
                    // "ADDRESS" => '',
                    // "DELIVERY_WAY" => '',
                    // "DELIVERY_TIME" => '',
                    // "PINCODE_AVAILABILITY" => '',
                    // "FOLLOW_UP_GIVEN_BY_USER" => '',
                    // "OK" => '',
                    // "MINIMUM_QUANTITY" => '',
                    // "TOTAL_PRICE" => '',
                    // "Size" => '',
                    // "CASH_ON_DELIVERY" => '',
                    // "PAYMENT_METHOD" => '',
                    // "QUANTITY_OBJECTION" => '',
                    // "UNKNOWN" => '',
                };
                #endregion
        }




        return '';
    }
    function getLinkMessage(): string
    {
        return '';
    }
    function getFirstMessage($personName): array
    {
        $firstMessage = "Welcome to Custom Print, an expert in stunning Jewelry Tags! Our tags offer:

        Quality:
        - Customizable designs for a unique touch.
        - Option to display gross and net weight.
        - Durable plastic-coated material for long-lasting elegance. ✨💎

        Price
        Price Per Sheet:-18Rs
        Tags‎ Per‎ Sheet:-‎ 40 Tags
        Price Per‎ Tag:-45 Paise
        Minimum‎‎ Order:-150‎ Sheets

        How to order:
        - Send your store name for free customization!

        Perfect for showcasing weight details and adding a professional edge to your jewelry. Any questions or ready to order? Reach out! 😊

        Custom Print में आपका स्वागत है, ज्वेलरी टैग्स के एक माहिर! हमारे टैग्स ये विशेषताएँ प्रदान करते हैं:

        क्वालिटी:
        - आपकी ज्वैलरी के लिए कस्टमाइज्ड डिजाइंस
        - Gross और net weight दिखाने का विकल्प.
        - लांग टर्म सौंदर्य के लिए टिकाऊ प्लास्टिक-कोटेड मैटेरियल . ✨💎

        मूल्य:
        रेट:– 18/–‎ प्रति‎ शीत
        प्रत्येक टैग‎ एक‎ शीट‎ में:- 40 टैग्स
        रेट प्रति‎ टैग:-‎ 45 पैसे
        कम‎‎ से‎ कम‎‎ ऑर्डर:–‎‎ 150‎ शीट्स";

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
}
