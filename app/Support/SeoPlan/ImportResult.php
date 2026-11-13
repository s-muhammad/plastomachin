<?php

namespace App\Support\SeoPlan;

final class ImportResult
{
    /**
     * @param  list<string>  $errors
     */
    public function __construct(
        public int $total,
        public int $created,
        public int $duplicates,
        public int $invalid,
        public array $errors = [],
    ) {}

    public function summary(): string
    {
        return sprintf(
            '%s مورد اضافه شد؛ %s مورد تکراری رد شد؛ %s ردیف نامعتبر بود.',
            fa_num($this->created),
            fa_num($this->duplicates),
            fa_num($this->invalid)
        );
    }
}
