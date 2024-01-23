<?php


if (!function_exists('detectMessageMeaning')) {
    function detectMessageMeaning($message, array $keywords, array $phrases = [],$threshold = 2): bool
    {
        $input = strtolower(trim($message));
        $tokens = preg_split('/\s+/', $input);

        foreach ($tokens as $token) {
            foreach ($keywords as $keyword) {
                // Using a fuzzy matching algorithm (like Levenshtein)
                if (levenshtein($token, $keyword) <= $threshold) { // Threshold can be adjusted
                    return true;
                }

                // Additional checks can be added here (e.g., regex, other algorithms)
            }
        }

        if (count($phrases) > 0) {
            foreach ($phrases as $phrase) {
                if (strpos($input, $phrase) !== false) {
                    return true;
                }
            }
        }

        return false;
    }
}
