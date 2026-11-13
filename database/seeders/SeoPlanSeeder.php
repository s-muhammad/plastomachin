<?php

namespace Database\Seeders;

use App\Support\SeoPlan\CsvReader;
use App\Support\SeoPlan\SeoPlanImporter;
use Illuminate\Database\Seeder;

class SeoPlanSeeder extends Seeder
{
    public function run(SeoPlanImporter $importer): void
    {
        $this->seedKeywords($importer);
        $this->seedTasks($importer);
    }

    private function seedKeywords(SeoPlanImporter $importer): void
    {
        $path = config('seo-plan.keywords_path');

        if (! is_string($path) || ! is_file($path)) {
            $this->command?->warn('SEO plan keywords CSV not found: '.$path);

            return;
        }

        $csv = CsvReader::read($path);
        $missing = array_diff(SeoPlanImporter::KEYWORD_COLUMNS, $csv['headers']);

        if ($missing !== []) {
            $this->command?->warn('SEO plan keywords CSV is missing columns: '.implode(', ', $missing));

            return;
        }

        $result = $importer->importKeywords($csv['rows']);

        $this->command?->info(sprintf(
            'SEO keywords: %d created, %d duplicates skipped, %d invalid.',
            $result->created,
            $result->duplicates,
            $result->invalid
        ));
    }

    private function seedTasks(SeoPlanImporter $importer): void
    {
        $path = config('seo-plan.tasks_path');

        if (! is_string($path) || ! is_file($path)) {
            $this->command?->warn('SEO plan tasks CSV not found: '.$path);

            return;
        }

        $csv = CsvReader::read($path);
        $missing = array_diff(SeoPlanImporter::TASK_COLUMNS, $csv['headers']);

        if ($missing !== []) {
            $this->command?->warn('SEO plan tasks CSV is missing columns: '.implode(', ', $missing));

            return;
        }

        $result = $importer->importTasks($csv['rows']);

        $this->command?->info(sprintf(
            'SEO tasks: %d created, %d duplicates skipped, %d invalid.',
            $result->created,
            $result->duplicates,
            $result->invalid
        ));
    }
}
