<?php

namespace App\Support\SeoPlan;

use App\Enums\SeoPlan\KeywordRole;
use App\Enums\SeoPlan\KeywordStatus;
use App\Enums\SeoPlan\PageType;
use App\Enums\SeoPlan\SearchIntent;
use App\Enums\SeoPlan\TaskCategory;
use App\Enums\SeoPlan\TaskStatus;
use App\Models\SeoKeyword;
use App\Models\SeoTask;

final class SeoPlanImporter
{
    public const KEYWORD_COLUMNS = [
        'keyword', 'role', 'target_page', 'target_url', 'page_type', 'intent',
        'priority', 'content_title', 'secondary_keywords', 'internal_links',
        'status', 'notes',
    ];

    public const TASK_COLUMNS = [
        'title', 'description', 'category', 'priority', 'status',
    ];

    /**
     * @param  list<array<string, string|null>>  $rows
     */
    public function importKeywords(array $rows): ImportResult
    {
        $existing = SeoKeyword::query()
            ->get(['keyword', 'target_url'])
            ->mapWithKeys(fn (SeoKeyword $keyword) => [$this->keywordKey($keyword->keyword, $keyword->target_url) => true])
            ->all();

        $created = 0;
        $duplicates = 0;
        $invalid = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $data = $this->mapKeyword($row);

            if ($data === null) {
                $invalid++;
                $errors[] = 'ردیف '.fa_num($index + 2).': ستون‌های اجباری خالی یا مقدار نامعتبر است.';

                continue;
            }

            $key = $this->keywordKey($data['keyword'], $data['target_url']);

            if (isset($existing[$key])) {
                $duplicates++;

                continue;
            }

            SeoKeyword::create($data);
            $existing[$key] = true;
            $created++;
        }

        return new ImportResult(count($rows), $created, $duplicates, $invalid, $errors);
    }

    /**
     * @param  list<array<string, string|null>>  $rows
     */
    public function importTasks(array $rows): ImportResult
    {
        $existing = SeoTask::query()
            ->pluck('title')
            ->mapWithKeys(fn (string $title) => [$this->taskKey($title) => true])
            ->all();

        $created = 0;
        $duplicates = 0;
        $invalid = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $data = $this->mapTask($row);

            if ($data === null) {
                $invalid++;
                $errors[] = 'ردیف '.fa_num($index + 2).': عنوان، دسته یا وضعیت نامعتبر است.';

                continue;
            }

            $key = $this->taskKey($data['title']);

            if (isset($existing[$key])) {
                $duplicates++;

                continue;
            }

            SeoTask::create($data);
            $existing[$key] = true;
            $created++;
        }

        return new ImportResult(count($rows), $created, $duplicates, $invalid, $errors);
    }

    /**
     * Build a read-only preview of the keyword import.
     *
     * @param  list<array<string, string|null>>  $rows
     * @return array{total:int, valid:int, duplicates:int, invalid:int, rows:list<array<string, mixed>>, has_more:bool}
     */
    public function previewKeywords(array $rows): array
    {
        $existing = SeoKeyword::query()
            ->get(['keyword', 'target_url'])
            ->mapWithKeys(fn (SeoKeyword $keyword) => [$this->keywordKey($keyword->keyword, $keyword->target_url) => true])
            ->all();

        $seen = [];
        $preview = [];
        $valid = 0;
        $duplicates = 0;
        $invalid = 0;

        foreach ($rows as $row) {
            $data = $this->mapKeyword($row);

            if ($data === null) {
                $invalid++;
                $preview[] = [
                    'keyword' => $this->value($row, 'keyword') ?? '—',
                    'target_page' => $this->value($row, 'target_page') ?? '—',
                    'target_url' => $this->value($row, 'target_url'),
                    'state' => 'invalid',
                    'error' => 'ستون‌های اجباری خالی یا مقدار نامعتبر است.',
                ];

                continue;
            }

            $key = $this->keywordKey($data['keyword'], $data['target_url']);
            $isDuplicate = isset($existing[$key]) || isset($seen[$key]);

            if ($isDuplicate) {
                $duplicates++;
            } else {
                $valid++;
                $seen[$key] = true;
            }

            $preview[] = [
                'keyword' => $data['keyword'],
                'target_page' => $data['target_page'] ?? '—',
                'target_url' => $data['target_url'],
                'state' => $isDuplicate ? 'duplicate' : 'new',
                'error' => $isDuplicate ? 'این کلمه با همین آدرس قبلاً ثبت شده است.' : null,
            ];
        }

        return [
            'total' => count($rows),
            'valid' => $valid,
            'duplicates' => $duplicates,
            'invalid' => $invalid,
            'rows' => array_slice($preview, 0, 50),
            'has_more' => count($preview) > 50,
        ];
    }

    /**
     * Build a read-only preview of the task import.
     *
     * @param  list<array<string, string|null>>  $rows
     * @return array{total:int, valid:int, duplicates:int, invalid:int, rows:list<array<string, mixed>>, has_more:bool}
     */
    public function previewTasks(array $rows): array
    {
        $existing = SeoTask::query()
            ->pluck('title')
            ->mapWithKeys(fn (string $title) => [$this->taskKey($title) => true])
            ->all();

        $seen = [];
        $preview = [];
        $valid = 0;
        $duplicates = 0;
        $invalid = 0;

        foreach ($rows as $row) {
            $data = $this->mapTask($row);

            if ($data === null) {
                $invalid++;
                $preview[] = [
                    'title' => $this->value($row, 'title') ?? '—',
                    'category' => $this->value($row, 'category') ?? '—',
                    'priority' => $this->value($row, 'priority') ?? '—',
                    'status' => $this->value($row, 'status') ?? '—',
                    'state' => 'invalid',
                    'error' => 'عنوان، دسته یا وضعیت نامعتبر است.',
                ];

                continue;
            }

            $key = $this->taskKey($data['title']);
            $isDuplicate = isset($existing[$key]) || isset($seen[$key]);

            if ($isDuplicate) {
                $duplicates++;
            } else {
                $valid++;
                $seen[$key] = true;
            }

            $preview[] = [
                'title' => $data['title'],
                'category' => TaskCategory::tryFrom($data['category'])?->label() ?? $data['category'],
                'priority' => $data['priority'],
                'status' => TaskStatus::tryFrom($data['status'])?->label() ?? $data['status'],
                'state' => $isDuplicate ? 'duplicate' : 'new',
                'error' => $isDuplicate ? 'این تسک با همین عنوان قبلاً ثبت شده است.' : null,
            ];
        }

        return [
            'total' => count($rows),
            'valid' => $valid,
            'duplicates' => $duplicates,
            'invalid' => $invalid,
            'rows' => array_slice($preview, 0, 50),
            'has_more' => count($preview) > 50,
        ];
    }

    /**
     * @param  array<string, string|null>  $row
     * @return array<string, mixed>|null
     */
    private function mapKeyword(array $row): ?array
    {
        $keyword = $this->value($row, 'keyword');
        $role = KeywordRole::tryFrom((string) $this->value($row, 'role'));
        $pageType = PageType::tryFrom((string) $this->value($row, 'page_type'));
        $intent = SearchIntent::tryFrom((string) $this->value($row, 'intent'));

        if ($keyword === null || $role === null || $pageType === null || $intent === null) {
            return null;
        }

        $status = KeywordStatus::tryFrom((string) $this->value($row, 'status')) ?? KeywordStatus::Planned;
        $priority = (int) ($this->value($row, 'priority') ?? 2);
        $priority = in_array($priority, [1, 2, 3], true) ? $priority : 2;

        return [
            'keyword' => mb_substr($keyword, 0, 191),
            'role' => $role->value,
            'target_page' => $this->truncate($this->value($row, 'target_page'), 255),
            'target_url' => $this->truncate($this->safeUrl($this->value($row, 'target_url')), 255),
            'page_type' => $pageType->value,
            'intent' => $intent->value,
            'priority' => $priority,
            'content_title' => $this->truncate($this->value($row, 'content_title'), 255),
            'secondary_keywords' => $this->value($row, 'secondary_keywords'),
            'internal_links' => $this->value($row, 'internal_links'),
            'status' => $status->value,
            'notes' => $this->value($row, 'notes'),
        ];
    }

    /**
     * @param  array<string, string|null>  $row
     * @return array<string, mixed>|null
     */
    private function mapTask(array $row): ?array
    {
        $title = $this->value($row, 'title');
        $category = TaskCategory::tryFrom((string) $this->value($row, 'category'));

        if ($title === null || $category === null) {
            return null;
        }

        $status = TaskStatus::tryFrom((string) $this->value($row, 'status')) ?? TaskStatus::Todo;
        $priority = (int) ($this->value($row, 'priority') ?? 2);
        $priority = in_array($priority, [1, 2, 3], true) ? $priority : 2;

        return [
            'title' => mb_substr($title, 0, 255),
            'description' => $this->value($row, 'description'),
            'category' => $category->value,
            'priority' => $priority,
            'status' => $status->value,
            'done_at' => $status === TaskStatus::Done ? now() : null,
        ];
    }

    private function value(array $row, string $key): ?string
    {
        $value = trim((string) ($row[$key] ?? ''));

        return $value === '' ? null : $value;
    }

    private function truncate(?string $value, int $length): ?string
    {
        return $value === null ? null : mb_substr($value, 0, $length);
    }

    private function safeUrl(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return str_starts_with($value, '/')
            || str_starts_with($value, 'http://')
            || str_starts_with($value, 'https://')
            ? $value
            : null;
    }

    private function keywordKey(string $keyword, ?string $targetUrl): string
    {
        return mb_strtolower(trim($keyword))."\x1F".trim((string) $targetUrl);
    }

    private function taskKey(string $title): string
    {
        return mb_strtolower(trim($title));
    }
}
