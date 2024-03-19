<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains settings for PDF generation.
    |
    | Enabled:
    |
    |    Whether to load PDF / Image generation.
    |
    | Binary:
    |
    |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
    |
    | Timout:
    |
    |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
    |    Setting this to false disables the timeout (unlimited processing time).
    |
    | Options:
    |
    |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
    |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
    |
    | Env:
    |
    |    The environment variables to set while running the wkhtmltopdf process.
    |
    */

    'pdf' => [
        'enabled' => true,

        'binary' =>  config('app.env') == 'local' ?
            '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"' :
            base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),

        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
            // 'dpi' => 96,
            // "disable-smart-shrinking" => true
        ],

        "temporaryFolder" => config('app.env') == 'local' ?
            sys_get_temp_dir() : public_path('storage/temp'),

        'env' => [],
    ],

    'image' => [
        'enabled' => true,

        'binary' => config('app.env') == 'local' ?
            '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage"' :
            base_path('vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64'),


        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
            'dpi' => 96,
            "disable-smart-shrinking" => true
        ],
        "temporaryFolder" => config('app.env') == 'local' ?
            sys_get_temp_dir() : public_path('storage/temp'),


        'env'     => [],
    ],

];
