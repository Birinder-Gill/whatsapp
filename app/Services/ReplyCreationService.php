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
                    GeneralQuery::ADDRESS => 'Sir hamara store Worli, Mumbai, Maharashtra me hai.',
                    GeneralQuery::MORE_DETAILS => 'Iska unique feature hai iski LED light , jo isko aur bhi valuable banata hai.',
                    GeneralQuery::USE_CASE => 'Ji han Sir, Ise aap Jewelery related kisi bhi chiz ke liye use kr skte hain. Try kijiye, aapko zaroor pasand aayega.',
                    GeneralQuery::DELIVERY_WAY => 'Safe aur reliable delivery ke through aapka product pahunchayenge.',
                    GeneralQuery::DELIVERY_TIME => '5-7 din mein deliver ho jayega',
                    GeneralQuery::PINCODE_AVAILABILITY => 'Aapke pin code pe delivery available hai. Aasani se order kare.',
                    GeneralQuery::FOLLOW_UP_GIVEN_BY_USER => 'Jab ready ho, bataye. Aapki har zarurat ka dhyan rakhenge.',
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


         return
"*🔍Welcome to Gem Craft, .".$personName." Ji,*

We specialize in stunning Jewelry Equipment! Our Eye Loupe Magnifier Lens for Jewelers offers:

- *🔎 30x and 60x High-quality lens* for sharp, accurate details.

- *💰 Price*: ```Rs. 899 only```

- *🎯 Ideal for*: Checking gems and any other detailed jewelry work.

- *💡 Special Features*: LED light for both magnifying lenses for enhanced visibility

*🛒 To Order*:
-``` Reply with *'Interested'* or *'Yes'*```

*🌐 For more info*:
- Ask here or call us at this number.

*🔍Gem Craft में आपका स्वागत है, .".$personName." जी,*

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
                    PriceQuery::HIGH_AS_COMPARED => 'Sir, koi bhi product ho, har tarah ki quality me milta hai. Hamari quality me ye price unmatched hai.',
                    PriceQuery::HIGH_IN_GENERAL => 'Sir, koi bhi product ho, har tarah ki quality me milta hai. Hamari quality me ye price unmatched hai.',
                    PriceQuery::WHOLESALE => 'Sir wholesale quantity me extra discount milega. Wholesale quantities ke baare is number call krlen',
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
            UserLanguage::HINGLISH => 'Sir, Ye lens sirf 899/- ke discounted price pe milega',
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
