<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // For interacting with storage

if (!function_exists('generateAndStoreImage')) {

    function generateAndStoreImage($pdf,$disk = 'public')
    {
        $now = Carbon::now("Asia/Kolkata")->timestamp;
        $filename = "dp_$now.jpg";
        // Use loadHtml for flexibility
        $imagePath = 'images/' . $filename; // Adjust the storage path as needed

        Storage::disk($disk)->put($imagePath, $pdf->output());

        return Storage::disk($disk)->url($imagePath);
    }
}

function deleteStoredImage($imageUrlOrPath, $disk = 'public')
{
    $imagePath = Str::after($imageUrlOrPath, Storage::disk($disk)->url('/'));
    return Storage::disk($disk)->delete($imagePath);
}


if (!function_exists('detectMessageMeaning')) {
    function detectMessageMeaning($message, array $keywords, array $phrases = [],$threshold = 1): bool
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
