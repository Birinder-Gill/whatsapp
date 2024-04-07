<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AddNewProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a new product to our system.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $productName = 'digitalCard'; // $this->ask('What is the product name?');

        // $this->createServiceProduct($productName);
        $this->registerProduct($productName);
    }


    function registerProduct($productName)
    {
        $className = Str::studly($productName);
        $namespace = "App\\Services\\Products";
        $fullClassName = "$namespace\\$className";

        // Path to the AppServiceProvider
        $providerPath = app_path('Providers/AppServiceProvider.php');
        $content = file_get_contents($providerPath);

        // Check if the product case already exists to avoid duplicates
        if (strpos($content, "case '$productName':") !== false) {
            $this->error("A case for '$productName' already exists in the AppServiceProvider.");
            return;
        }

        // Add import statement if not already present
        if (!str_contains($content, $fullClassName)) {
            // Find the namespace declaration to determine where to insert use statements
            $namespacePattern = "/namespace App\\\Providers;/";
            $useStatement = "\n\nuse $fullClassName;";
            $content = preg_replace($namespacePattern, "$0$useStatement", $content, 1);
        }

        // Pattern to find the default case
        $pattern = "/(\s+default:)/";

        // New case to insert before the default case
        $replacement = "\n\t\t\t\tcase '$productName': return new $className(\$this->app->make(Request::class));\n$1";

        // Perform the replacement
        $newContent = preg_replace($pattern, $replacement, $content, -1, $count);

        // Only proceed if a replacement was made
        if ($count > 0) {
            file_put_contents($providerPath, $newContent);
            $this->info("Service provider updated successfully with $productName.");
        } else {
            $this->error("Failed to locate the 'default' case in AppServiceProvider.");
        }
    }

    function createServiceProduct($productName)
    {
        $className = Str::studly($productName);
        $path = app_path("Services/Products/{$className}.php");

        if (file_exists($path)) {
            $this->error("A service for {$className} already exists!");
            return;
        }

        $namespace = "App\\Services\\Products";
        $template = <<<EOT
<?php

namespace $namespace;

use App\Enums\UserLanguage;
use App\Services\ReplyCreationService;
use Nette\NotImplementedException;

class $className extends ReplyCreationService
{
    function getQueryResponse(string \$query): string
    {
        \$language = UserLanguage::HINGLISH;
        switch (\$language) {
            case UserLanguage::HINGLISH:
                return match (\$query) {
                    "UNKNOWN" => '',
                };
            #region Other languages
            case UserLanguage::HINDI:
                return match (\$query) {
                };
            case UserLanguage::ENGLISH:
                return match (\$query) {
                };
            #endregion
        }

        return '';
    }

    function getLinkMessage(): string
    {
        throw new NotImplementedException();
    }

    function getFirstMessage(\$personName): array
    {
        \$firstMessage = "";

        return [
            'message' => \$firstMessage,
            'media' =>  ''
        ];
    }

    function getFirstMedias(): array
    {
        return [];
    }

    function getFirstFollowUp(): string
    {
        throw new NotImplementedException();
    }

    function getContactSaveFollowUp(): string
    {
        throw new NotImplementedException();
    }
}

EOT;

        file_put_contents($path, $template);
        $this->info("Service class for {$className} created successfully.");
    }
}
