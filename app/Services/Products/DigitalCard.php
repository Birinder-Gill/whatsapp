<?php

namespace App\Services\Products;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;
use Nette\NotImplementedException;

class DigitalCard extends ReplyCreationService
{
    function getQueryResponse(string $query): string
    {
        return $query;// IN THIS CASE THE CHATGPT IS KHULLA SHADDEYA HOYA TO SEE WHAT KIND OF QUERIES COME.

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
        return 'Agar aap Digital card banvana chahte hain to apne business ki sari information hamein send kar dijiye, apka card ready krke apko bhej dia jayega.';
    }

    function getFirstMessage($personName): array
    {
        $firstMessage = "🌟 Hello $personName Ji, welcome to the new era of digital business cards. 🌟

        A digital version of traditional paper visiting cards. Contains all the contact information information about your business including product gallery, store location and social media profiles. Easily shared through smartphones, email, or QR codes and stays with your customers forever.

        ✨ Benefits: Improves networking and expands your audience.

        💼 Features: Stylish, affordable, and leaves a lasting impression.

        📲 Quick Share: Share your info via SMS, email, etc.

        🔍 QR Code: Easy scanning for instant info access.

        🌐 Social Media: Boost your online presence.

        ✏️ Custom Templates: Choose from various designs.

        💰 Price: 700 INR
        ----------------------------
        🌟 नमस्ते $personName जी, डिजिटल विज़िटिंग कार्ड्स के नए युग में स्वागत है 🌟

        यह पुराने पेपर के कार्ड्स का डिजिटल वर्जन है। इसमें आपके बिजनेस की सारी जानकारी होती है, जैसे कि प्रोडक्ट गैलरी, दुकान का पता और सोशल मीडिया प्रोफाइल्स। इसे आप मोबाइल, ईमेल या QR कोड के ज़रिए आसानी से शेयर कर सकते हैं और ये आपके ग्राहकों के पास हमेशा के लिए रहता है।

        ✨ फायदे: नेटवर्किंग को बेहतर बनाता है और आपके ऑडियंस को बड़ा करता है।

        💼 खूबियां: स्टाइलिश, किफायती और लंबे समय तक याद रखने वाला प्रभाव डालता है।

        📲 फटाफट शेयर: अपनी जानकारी SMS, ईमेल आदि से शेयर करें।

        🔍 QR कोड: जानकारी तक तुरंत पहुँच के लिए आसान स्कैनिंग।

        🌐 सोशल मीडिया: अपनी ऑनलाइन उपस्थिति बढ़ाएं।

        ✏️ कस्टम टेम्पलेट्स: अलग-अलग डिज़ाइन्स में से चुनें।

        💰 कीमत: सिर्फ 700 रुपए।";

        return [
            'message' => $firstMessage,
            'media' =>  config('app.video')
        ];
    }

    function getFirstMedias(): array
    {
        return [];
    }

    function getFirstFollowUp(): string
    {
        return 'Agar aap Digital card banvana chahte hain to apne business ki sari information hamein send kar dijiye, apka card ready krke apko bhej dia jayega.';
    }

    function getContactSaveFollowUp(): string
    {
        return 'Namaste, kripya hamara contact save kar lein. Aisa karne se aap wo products aur offers, jo apke business me apki help kar sakte hain, seedhe WhatsApp stories me dekh sakte hain aur WhatsApp se hi order karke apne address par product pa sakte hain.';

    }
}
