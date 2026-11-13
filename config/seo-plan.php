<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SEO plan seed data
    |--------------------------------------------------------------------------
    | Paths can be overridden per site, either from the environment or by
    | changing these values. The files must be UTF-8 CSV files with the
    | English headers described in the module documentation.
    */
    'keywords_path' => env('SEO_PLAN_KEYWORDS_CSV', database_path('seeders/data/seo_keywords.csv')),
    'tasks_path' => env('SEO_PLAN_TASKS_CSV', database_path('seeders/data/seo_tasks.csv')),

    // Maximum number of rows accepted in one CSV import.
    'import_limit' => 2000,
];
