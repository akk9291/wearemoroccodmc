<?php

namespace App\Console\Commands;

use App\Support\ImageVariants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/** Backfill: WebP + 800/1600 px variants for every image already uploaded to storage/app/public/uploads. */
class GenerateImageVariants extends Command
{
    protected $signature = 'images:variants {--dir=uploads : Folder on the public disk}';

    protected $description = 'Create the WebP and resized variants (used in srcset) for uploaded images';

    public function handle(): int
    {
        $written = 0;
        foreach (Storage::disk('public')->allFiles($this->option('dir')) as $path) {
            if (preg_match('/-(\d+)\.webp$/', $path) || ! ImageVariants::isUpload($path)) {
                continue; // a variant itself, or not an image
            }
            $new = ImageVariants::generate($path);
            $written += count($new);
            foreach ($new as $file) {
                $this->line("  {$file}");
            }
        }
        $this->info("{$written} variant(s) written.");

        return self::SUCCESS;
    }
}
