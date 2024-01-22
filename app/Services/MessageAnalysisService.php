<?php

namespace App\Services;

use App\Enums\GeneralQuery;
use App\Enums\PriceQuery;

class MessageAnalysisService
{
    function askingForPrice($message): bool
    {
        $input = strtolower(trim($message));

        // Common root forms of 'price' and 'rate'
        $keywords = [
            'price', 'prac', 'rate', 'ret', 'cost', 'how much', 'kitna',
            'daam', 'kimat', 'pp',  'praice', 'prise', 'prc', 'raet',
            'reet', 'kitne', 'keemat', 'dam', 'muly', 'mahnga', 'sasta'
        ];

        // Tokenize the input into words
        $tokens = preg_split('/\s+/', $input);

        foreach ($tokens as $token) {
            foreach ($keywords as $keyword) {
                // Using a fuzzy matching algorithm (like Levenshtein)
                if (levenshtein($token, $keyword) <= 2) { // Threshold can be adjusted
                    return true;
                }

                // Additional checks can be added here (e.g., regex, other algorithms)
            }
        }

        return false;
    }

    function discussingPrice($message): PriceQuery
    {
        // Normalize the input
        $input = strtolower(trim($message));

        // Common keywords or root forms indicating high prices
        $highPriceKeywords = ['expensive', 'costly', 'high', 'too much', 'zada', 'mahanga', 'jyada', 'mehega', 'mehanga'];

        // Tokenize the input into words
        $tokens = preg_split('/\s+/', $input);

        foreach ($tokens as $token) {
            foreach ($highPriceKeywords as $keyword) {
                if (levenshtein($token, $keyword) <= 2) { // Adjust the threshold as needed
                    return PriceQuery::HIGH_IN_GENERAL;
                }
                // Additional checks (like regex patterns) can be added here
            }
        }

        // Check for common phrases indicating high price complaints
        $phrases = ['price is too high', 'too costly', 'bahut zada', 'bahut mahanga'];
        foreach ($phrases as $phrase) {
            if (strpos($input, $phrase) !== false) {
                return PriceQuery::HIGH_IN_GENERAL;
            }
        }

        if ($this->isAskingForWholesaleOrBulk($message)) return PriceQuery::WHOLESALE;
        return false;
    }
    function isAskingForWholesaleOrBulk($input)
    {
        $input = strtolower(trim($input));
        $bulkKeywords = ['wholesale', 'bulk', 'large quantity', 'high quantity', 'badi matra', 'thok', 'jyada maatra'];
        $tokens = preg_split('/\s+/', $input);

        foreach ($tokens as $token) {
            foreach ($bulkKeywords as $keyword) {
                if (levenshtein($token, $keyword) <= 2) {
                    return true;
                }
            }
        }

        $phrases = ['buy in bulk', 'wholesale rate', 'wholesale price', 'bulk order', 'large quantities', 'thok ke rate', 'thok mein', 'jyada maatra mein'];
        foreach ($phrases as $phrase) {
            if (strpos($input, $phrase) !== false) {
                return true;
            }
        }

        return false;
    }
    function userReadyToOrder($message): bool
    {
            $input = strtolower(trim($message));
            $orderConfirmationKeywords = ['yes', 'ok', 'okay', 'sure', 'definitely', 'absolutely', 'confirm', 'order', 'book', 'haan', 'ha', 'ji', 'thik', 'theek', 'sahi'];
            $tokens = preg_split('/\s+/', $input);

            foreach ($tokens as $token) {
                foreach ($orderConfirmationKeywords as $keyword) {
                    if (levenshtein($token, $keyword) <= 2) {
                        return true;
                    }
                }
            }

            $phrases = ['ready to order', 'place order', 'complete order', 'finalize order', 'order karna hai', 'booking kar do', 'order confirm', 'order kar do', 'order book'];
            foreach ($phrases as $phrase) {
                if (strpos($input, $phrase) !== false) {
                    return true;
                }
            }

            return false;

    }
    function queryDetection($message): GeneralQuery
    {
        $addressKeywords = ['address', 'location', 'where', 'office', 'store', 'shop', 'pata', 'sthan', 'kaha', 'kahan'];

        $detailsKeywords = ['details', 'info', 'information', 'specifications', 'specs', 'detail', 'vivaran', 'jankari', 'soochna'];
        $useCaseKeywords = ['use', 'usage', 'how to use', 'application', 'utility', 'upayog', 'prayog', 'kaise use kare', 'upyogita'];
        $deliveryMethodKeywords = ['how', 'deliver', 'delivery', 'ship', 'shipping', 'courier', 'reach', 'transport', 'kaise', 'pahuchega', 'bhejna', 'shipment'];

        $deliveryTimeKeywords = ['when', 'time', 'long', 'duration', 'receive', 'delivery time', 'kitna samay', 'samay', 'kab tak', 'avadhi'];
        $pincodeKeywords = ['pincode', 'postal code', 'zip code', 'serviceable', 'deliverable', 'pin code', 'zipcode', 'area code', 'postal', 'zip'];
        $confirmationKeywords = ['ok', 'okay', 'yes', 'sure', 'alright', 'fine', 'confirm', 'agreed', 'thik hai', 'haan', 'jee', 'sahi'];

        //  TODO: THIS METHOD SHOULD RETURN THE REPLY STRINGS AS PER CONDITION
        if (userAsksForDemo($message)) {
            return GeneralQuery::ADDRESS;
            //  Address
        }
        if (userAsksForAddress($message)) {
            return GeneralQuery::MORE_DETAILS;
            //  More details.
        }
        if (userAsksForMoreDetails($message)) {
            return GeneralQuery::USE_CASE;
            //  Use case.
        }
        if (userAsksForUseCase($message)) {
            return GeneralQuery::DELIVERY_WAY;
            //  Delivery Way.
        }
        if (userAsksForDeliveryWay($message)) {
            return GeneralQuery::DELIVERY_TIME;
            //  Delivery time
        }
        if (userAsksForDeliveryTime($message)) {
            return GeneralQuery::PINCODE_AVAILABILITY;
            //  Pincode availability
        }
        if (userAsksForPincodeAvailability($message)) {
            return GeneralQuery::FOLLOW_UP_GIVEN_BY_USER;
            //  Follow Up given by user.
        }
        if (userSaysOk($message)) {
            GeneralQuery::OK;
            //  Follow Up given by user.
        }
        return GeneralQuery::OK;
    }
}


