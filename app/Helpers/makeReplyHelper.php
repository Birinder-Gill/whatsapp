<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // For interacting with storage

if (!function_exists('generateAndStorePdf')) {

    function generateAndStorePdf($pdf,$disk = 'public')
    {
        $now = Carbon::now("Asia/Kolkata")->timestamp;
        $filename = "pdf_$now.pdf";
        // Use loadHtml for flexibility
        $pdfPath = 'images/' . $filename; // Adjust the storage path as needed

      return generateAndStoreFile($pdf,$pdfPath,$disk);
    }
}

if (!function_exists('generateAndStoreImage')) {

    function generateAndStoreImage($image,$disk = 'public')
    {
        $now = Carbon::now("Asia/Kolkata")->timestamp;
        $filename = "dp_$now.jpg";
        // Use loadHtml for flexibility
        $imagePath = 'images/' . $filename; // Adjust the storage path as needed

       return generateAndStoreFile($image,$imagePath,$disk);
    }
}

if (!function_exists('generateAndStoreFile')) {

    function generateAndStoreFile($file,$filePath,$disk = 'public')
    {
        Storage::disk($disk)->put($filePath, $file->output());

        return Storage::disk($disk)->url($filePath);
    }
}

function deleteStoredFile($imageUrlOrPath, $disk = 'public')
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
