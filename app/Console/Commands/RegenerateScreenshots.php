<?php

namespace App\Console\Commands;

use App\Models\HeaderAnalysis;
use App\Http\Controllers\AnalyzeController;
use Illuminate\Console\Command;
use ReflectionClass;

class RegenerateScreenshots extends Command
{
    protected $signature = 'screenshots:regenerate {--id= : Specific analysis ID to regenerate}';
    protected $description = 'Regenerate screenshot URLs for existing analyses';

    public function handle()
    {
        $controller = new AnalyzeController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('generateScreenshotUrl');
        $method->setAccessible(true);

        $query = HeaderAnalysis::query();
        
        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        $analyses = $query->get();
        $this->info("Found {$analyses->count()} analyses to process.");

        $bar = $this->output->createProgressBar($analyses->count());
        $bar->start();

        $updated = 0;
        foreach ($analyses as $analysis) {
            try {
                $newScreenshotUrl = $method->invoke($controller, $analysis->url);
                if ($newScreenshotUrl) {
                    $analysis->screenshot_url = $newScreenshotUrl;
                    $analysis->save();
                    $updated++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Failed to regenerate screenshot for analysis #{$analysis->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully regenerated {$updated} screenshot URLs.");
    }
}

