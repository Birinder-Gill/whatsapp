<?php

namespace App\Services;

use App\Enums\GeneralQuery;
use App\Enums\PriceQuery;

class MessageAnalysisService
{
    function askingForPrice($message): bool
    {
        // Common root forms of 'price' and 'rate'
        $keywords = [
            'price', 'prac', 'rate', 'ret', 'cost', 'how much', 'kitna',
            'daam', 'kimat', 'pp',  'praice', 'prise', 'prc', 'raet',
            'reet', 'kitne', 'keemat', 'dam', 'muly', 'mahnga', 'sasta'
        ];

        return detectMessageMeaning($message, $keywords);
    }

    function discussingPrice($message): PriceQuery
    {
        // Common keywords or root forms indicating high prices
        $highPriceKeywords = ['expensive', 'costly', 'high', 'too much', 'zada', 'mahanga', 'jyada', 'mehega', 'mehanga'];

        // Check for common phrases indicating high price complaints
        $phrases = ['price is too high', 'too costly', 'bahut zada', 'bahut mahanga'];

        if (detectMessageMeaning($message, $highPriceKeywords, $phrases)) {
            return PriceQuery::HIGH_IN_GENERAL;
        }

        if ($this->isAskingForWholesaleOrBulk($message)) return PriceQuery::WHOLESALE;
        return PriceQuery::UNKNOWN;
    }

    function isAskingForWholesaleOrBulk($message)
    {
        $bulkKeywords = ['wholesale', 'bulk', 'large quantity', 'high quantity', 'badi matra', 'thok', 'jyada maatra'];
        $phrases = ['buy in bulk', 'wholesale rate', 'wholesale price', 'bulk order', 'large quantities', 'thok ke rate', 'thok mein', 'jyada maatra mein'];
        return detectMessageMeaning($message, $bulkKeywords, $phrases);
    }

    function userReadyToOrder($message): bool
    {

        $orderConfirmationKeywords = ['yes', 'ok', 'okay', 'sure', 'definitely', 'absolutely', 'confirm', 'order', 'book', 'haan', 'ha', 'ji', 'thik', 'theek', 'sahi'];
        $phrases = ['ready to order', 'place order', 'complete order', 'finalize order', 'order karna hai', 'booking kar do', 'order confirm', 'order kar do', 'order book'];
        return detectMessageMeaning($message, $orderConfirmationKeywords, $phrases);

    }
    function queryDetection($message): GeneralQuery
    {
        $addressKeywords = ['address', 'location', 'where', 'office', 'store', 'shop', 'pata', 'sthan', 'kaha', 'kahan'];
        $addressPhrases = [
            'where are you located',
            'where is your store',
            'your office address',
            'location of shop',
            'address kya hai',
            'aapka pata bataye'
        ];

        $detailsKeywords = ['details', 'info', 'information', 'specifications', 'specs', 'detail', 'vivaran', 'jankari', 'soochna'];
        $detailsPhrases = [
            'tell me more about',
            'product details',
            'more information on',
            'need more info',
            'product ki jankari',
            'vistar se batao'
        ];

        $useCaseKeywords = ['use', 'usage', 'how to use', 'application', 'utility', 'upayog', 'prayog', 'kaise use kare', 'upyogita'];
        $useCasePhrases = [
            'how do I use',
            'how to operate',
            'usage instructions',
            'where can I use',
            'product ka upyog kaise',
            'isteemal kaise kare'
        ];

        $deliveryMethodKeywords = ['how', 'deliver', 'delivery', 'ship', 'shipping', 'courier', 'reach', 'transport', 'kaise', 'pahuchega', 'bhejna', 'shipment'];
        $deliveryMethodPhrases = [
            'how do you deliver',
            'delivery process',
            'shipping method',
            'how will it be sent',
            'product kaise milega',
            'kis tarah bhejenge'
        ];

        $deliveryTimeKeywords = ['when', 'time', 'long', 'duration', 'receive', 'delivery time', 'kitna samay','kitna time', 'samay', 'kab tak', 'avadhi'];
        $deliveryTimePhrases = [
            'how long will it take',
            'delivery duration',
            'when will I get',
            'expected delivery time',
            'kitne din mein milega',
            'delivery mein kitna samay lagega'
        ];

        $pincodeKeywords = ['pincode', 'postal code', 'zip code', 'serviceable', 'deliverable', 'pin code', 'zipcode', 'area code', 'postal', 'zip'];
        $pincodePhrases = [
            'do you deliver to',
            'can you ship to',
            'serviceable pincode',
            'pincode check',
            'mere pincode pe delivery',
            'kya aap is pincode mein bhej sakte hai'
        ];

        $confirmationKeywords = ['ok', 'okay', 'yes', 'sure', 'alright', 'fine', 'confirm', 'agreed', 'thik hai', 'haan', 'jee', 'sahi'];
        $confirmationPhrases = [
            'I agree',
            'sounds good',
            'that works for me',
            'I am fine with that',
            'mujhe manzoor hai',
            'thik hai, aage badho'
        ];

        if(detectMessageMeaning($message,$addressKeywords,$addressPhrases)) {
            return GeneralQuery::ADDRESS;
            //  Address
        }
        if(detectMessageMeaning($message,$detailsKeywords,$detailsPhrases)) {
            return GeneralQuery::MORE_DETAILS;
            //  More details.
        }
        if(detectMessageMeaning($message,$useCaseKeywords,$useCasePhrases)) {
            return GeneralQuery::USE_CASE;
            //  Use case.
        }
        if(detectMessageMeaning($message,$deliveryMethodKeywords,$deliveryMethodPhrases)) {
            return GeneralQuery::DELIVERY_WAY;
            //  Delivery Way.
        }
        if(detectMessageMeaning($message,$deliveryTimeKeywords,$deliveryTimePhrases)) {
            return GeneralQuery::DELIVERY_TIME;
            //  Delivery time
        }
        if(detectMessageMeaning($message,$pincodeKeywords,$pincodePhrases)) {
            return GeneralQuery::PINCODE_AVAILABILITY;
            //  Pincode availability
        }

        if(detectMessageMeaning($message,$confirmationKeywords,$confirmationPhrases)) {
           return GeneralQuery::OK;
            //  Follow Up given by user.
        }
        if (userAsksForPincodeAvailability($message)) {
            return GeneralQuery::FOLLOW_UP_GIVEN_BY_USER;
            //  Follow Up given by user.
        }
        return GeneralQuery::UNKNOWN;
    }
}
