<?php

namespace App\Services\Products;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;

class MagnifierLens extends ReplyCreationService
{

    function getQueryResponse(string $query): string
    {
        $language = UserLanguage::HINGLISH;
        switch ($language) {
            case UserLanguage::HINGLISH:
                return match ($query) {
                    'PRICE' => 'Sir, Ye lens sirf 899/- ke discounted price pe milega',
                    'ADDRESS' => 'Sir hamara store Mumbai, Maharashtra me hai, lekin lens apko courier ke through apke address par pahunchayenge.',
                    'MORE_DETAILS' => 'Isme 2 lens included hain. 30x and 60x and iska unique feature hai iski LED light , jo isko aur bhi valuable banata hai.',
                    'USE_CASE' => 'Ise aap Jewelery related kisi bhi chiz ki details ko clearly dekhne ke liye use kr skte hain.',
                    'DELIVERY_WAY' => 'Safe aur reliable delivery ke through aapka product pahunchayenge.',
                    'DELIVERY_TIME' => '5-7 din mein apke address pe deliver ho jayega',
                    'PINCODE_AVAILABILITY' => 'Aapke pin code pe delivery available hai. Aasani se order kare.',
                    'FOLLOW_UP_GIVEN_BY_USER' => 'Jab ready ho, bataye. Aapki har zarurat ka dhyan rakhenge.',
                    'HIGH_AS_COMPARED' => 'Sir, koi bhi product ho, har tarah ki quality me milta hai. Hamari quality me ye price unmatched hai.',
                    'HIGH_IN_GENERAL' => 'Sir, koi bhi product ho, har tarah ki quality me milta hai. Hamari quality me ye price unmatched hai.',
                    'WHOLESALE' => 'Sir wholesale quantity me extra discount milega. Wholesale quantities ke baare is number par call karein.',
                    'OK' => 'Thanks for the response sir.' . $this->getLinkMessage(),
                    'UNKNOWN' => "Kisi bhi jankari ke liye isi number pe whatsapp call kren."
                };
                #region Other languages
            case UserLanguage::HINDI:
                return match ($query) {
                    // "ADDRESS" => '',
                    // "MORE_DETAILS" => '',
                    // "USE_CASE" => '',
                    // "DELIVERY_WAY" => '',
                    // "DELIVERY_TIME" => '',
                    // "PINCODE_AVAILABILITY" => '',
                    // "FOLLOW_UP_GIVEN_BY_USER" => '',
                    // "OK" => '',
                };
            case UserLanguage::ENGLISH:
                return match ($query) {
                    // "ADDRESS" => '',
                    // "MORE_DETAILS" => '',
                    // "USE_CASE" => '',
                    // "DELIVERY_WAY" => '',
                    // "DELIVERY_TIME" => '',
                    // "PINCODE_AVAILABILITY" => '',
                    // "FOLLOW_UP_GIVEN_BY_USER" => '',
                    // "OK" => '',
                };
                #endregion
        }
    }

    function getLinkMessage(): string
    {
        return " Aap niche diye gye link se order kar sakte hain.\n\nhttps://7639cd.myshopify.com/products/jarlink-2-pack-jewelry-loupes";
    }

    function getFirstMessage($personName): array
    {
        $firstMessage =
            #region First message
            "*🔍Welcome to Gem Craft, ." . $personName . " Ji,*
We specialize in stunning Jewelry Equipment! Our Eye Loupe Magnifier Lens for Jewelers offers:

- *🔎 30x and 60x High-quality lens* for sharp, accurate details.

- *💰 Price*: ```Rs. 899 only```

- *🎯 Ideal for*: Checking gems and any other detailed jewelry work.

- *💡 Special Features*: LED light for both magnifying lenses for enhanced visibility

*🛒 To Order*:
-``` Reply with *'Interested'* or *'Yes'*```

*🌐 For more info*:
- Ask here or call us at this number.

*🔍Gem Craft में आपका स्वागत है, ." . $personName . " जी,*

हम ज्वेलरी उपकरणों में माहिर हैं! हमारा Eye Loupe Magnifier Lens ज्वेलर्स के लिए खास है:

- *🔎 30x और 60x  high-quality वाला लेंस* तेज़ और सटीक डिटेल्स के लिए।

- *💰 मूल्य*: केवल रु. 899

- *🎯 उपयोगिता के लिए*: रत्नों और किसी भी अन्य जटिल आभूषण कार्य की जाँच के लिए।

- *💡 विशेष विशेषताएं*: बेहतर दृश्यता के लिए दोनों आवर्धक लेंसों के लिए LED प्रकाश।

*🛒 ऑर्डर करने के लिए*:
- 'इंटरेस्टेड' या 'हाँ' के साथ जवाब दें

*🌐 अधिक जानकारी के लिए*:
- यहाँ पूछें या इस नंबर पर हमें कॉल करें।
";
        #endregion
        return [
            'message' => $firstMessage,
            'media' =>  config('app.video')
        ];
    }

    function getFirstMedias(): array
    {
        return [
            // config('app.picOne'),
            // config('app.picTwo'),
            // config('app.picThree')
        ];
    }

    function getFirstFollowUp(): string
    {
        return 'Do you wish to order it.\nAgar aap order karna chahte hain to aap niche diye gye link se order kar sakte hain.\n\nhttps://7639cd.myshopify.com/products/jarlink-2-pack-jewelry-loupes';
    }
    function getContactSaveFollowUp(): string
    {
        return 'Namaste, kripya hamara contact save kar lein. Aisa karne se aap jewellery se related products aur offers, jo apke business me apki help kar sakte hain, seedhe WhatsApp stories me dekh sakte hain aur WhatsApp se hi order karke apne address par product pa sakte hain.';
    }

}
