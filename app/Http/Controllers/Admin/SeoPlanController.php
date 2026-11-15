<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SeoPlan\Competition;
use App\Enums\SeoPlan\KeywordRole;
use App\Enums\SeoPlan\KeywordStatus;
use App\Enums\SeoPlan\PageType;
use App\Enums\SeoPlan\SearchIntent;
use App\Enums\SeoPlan\TaskCategory;
use App\Enums\SeoPlan\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\SeoKeyword;
use App\Models\SeoTask;
use App\Support\SeoPlan\CsvReader;
use App\Support\SeoPlan\SeoPlanImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeoPlanController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $tab = in_array($tab, ['tasks', 'graph'], true) ? $tab : 'keywords';
        $keywordFilters = $this->keywordFilters($request);
        $taskFilters = $this->taskFilters($request);

        $keywords = SeoKeyword::query()
            ->filter($keywordFilters)
            ->withCount([
                'tasks as open_tasks_count' => fn ($query) => $query->where('status', '!=', TaskStatus::Done->value),
            ])
            ->sort($request->query('sort'), $request->query('direction'))
            ->paginate(25)
            ->withQueryString();

        $taskBase = SeoTask::query()
            ->filter($taskFilters)
            ->with('keyword:id,keyword');

        $tasks = [
            'todo' => (clone $taskBase)
                ->where('status', TaskStatus::Todo->value)
                ->orderBy('priority')
                ->orderByRaw('due_date is null')
                ->orderBy('due_date')
                ->orderBy('id')
                ->get(),
            'doing' => (clone $taskBase)
                ->where('status', TaskStatus::Doing->value)
                ->orderBy('priority')
                ->orderByRaw('due_date is null')
                ->orderBy('due_date')
                ->orderBy('id')
                ->get(),
        ];

        $doneQuery = (clone $taskBase)
            ->where('status', TaskStatus::Done->value)
            ->orderByDesc('done_at')
            ->orderByDesc('id');

        $doneCount = (clone $doneQuery)->count();
        $allDone = $request->boolean('all_done');

        $tasks['done'] = $doneQuery
            ->when(! $allDone, fn ($query) => $query->limit(10))
            ->get();

        $stats = [
            'total' => SeoKeyword::count(),
            'priority_one' => SeoKeyword::where('priority', 1)->count(),
            'published' => SeoKeyword::where('status', KeywordStatus::Published->value)->count(),
            'planned' => SeoKeyword::where('status', KeywordStatus::Planned->value)->count(),
            'tasks' => SeoTask::count(),
        ];

        $targetPages = SeoKeyword::query()
            ->whereNotNull('target_page')
            ->where('target_page', '!=', '')
            ->distinct()
            ->orderBy('target_page')
            ->pluck('target_page');

        $keywordOptions = $tab === 'tasks'
            ? SeoKeyword::query()->orderBy('keyword')->get(['id', 'keyword'])
            : collect();

        return view('admin.seo-plan.index', [
            'tab' => $tab,
            'keywords' => $keywords,
            'tasks' => $tasks,
            'doneCount' => $doneCount,
            'allDone' => $allDone,
            'stats' => $stats,
            'targetPages' => $targetPages,
            'keywordOptions' => $keywordOptions,
            'keywordFilters' => $keywordFilters,
            'taskFilters' => $taskFilters,
            'graph' => $tab === 'graph' ? $this->graphData($keywordFilters) : null,
            'options' => [
                'roles' => KeywordRole::options(),
                'page_types' => PageType::options(),
                'intents' => SearchIntent::options(),
                'keyword_statuses' => KeywordStatus::options(),
                'competitions' => Competition::options(),
                'categories' => TaskCategory::options(),
                'task_statuses' => TaskStatus::options(),
                'priorities' => [1 => '۱ - بالا', 2 => '۲ - متوسط', 3 => '۳ - پایین'],
            ],
        ]);
    }

    public function storeKeyword(Request $request): RedirectResponse
    {
        SeoKeyword::create($this->validateKeyword($request));

        return back()->with('success', 'کلمه کلیدی اضافه شد.');
    }

    public function updateKeyword(Request $request, SeoKeyword $keyword): RedirectResponse
    {
        $keyword->update($this->validateKeyword($request));

        return back()->with('success', 'کلمه کلیدی به‌روزرسانی شد.');
    }

    public function inlineKeyword(Request $request, SeoKeyword $keyword): RedirectResponse
    {
        $data = $request->validate([
            'priority' => ['sometimes', 'required', 'integer', Rule::in([1, 2, 3])],
            'status' => ['sometimes', 'required', Rule::in(KeywordStatus::values())],
            'competition' => ['sometimes', 'nullable', Rule::in(Competition::values())],
            'volume' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:4294967295'],
            'position' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:65535'],
        ], [], [
            'priority' => 'اولویت',
            'status' => 'وضعیت',
            'competition' => 'رقابت',
            'volume' => 'حجم جستجو',
            'position' => 'رتبه',
        ]);

        $keyword->update($data);

        return back();
    }

    public function destroyKeyword(SeoKeyword $keyword): RedirectResponse
    {
        $keyword->delete();

        return back()->with('success', 'کلمه کلیدی حذف شد.');
    }

    public function createTask(SeoKeyword $keyword): RedirectResponse
    {
        if ($keyword->page_type !== PageType::Blog->value) {
            return back()->with('error', 'ساخت تسک فقط برای ردیف‌های مقاله وبلاگ ممکن است.');
        }

        $hasOpenTask = SeoTask::query()
            ->where('keyword_id', $keyword->id)
            ->where('status', '!=', TaskStatus::Done->value)
            ->exists();

        if ($hasOpenTask) {
            return back()->with('error', 'برای این کلمه یک تسک باز وجود دارد.');
        }

        SeoTask::create([
            'title' => 'نوشتن مقاله: '.$keyword->keyword,
            'description' => $keyword->content_title
                ? 'عنوان پیشنهادی: '.$keyword->content_title
                : null,
            'category' => TaskCategory::Content->value,
            'priority' => $keyword->priority,
            'status' => TaskStatus::Todo->value,
            'keyword_id' => $keyword->id,
        ]);

        return back()->with('success', 'تسک نوشتن مقاله ساخته شد.');
    }

    public function previewImport(Request $request, SeoPlanImporter $importer)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [], [
            'file' => 'فایل CSV',
        ]);

        $file = $request->file('file');
        $csv = CsvReader::read($file->getRealPath());

        $missing = array_values(array_diff(SeoPlanImporter::KEYWORD_COLUMNS, $csv['headers']));

        if ($missing !== []) {
            return back()->with('error', 'ستون‌های ضروری در فایل نیست: '.implode(', ', $missing));
        }

        if ($csv['rows'] === []) {
            return back()->with('error', 'فایل CSV هیچ ردیف داده‌ای ندارد.');
        }

        $limit = (int) config('seo-plan.import_limit', 2000);

        if (count($csv['rows']) > $limit) {
            return back()->with('error', 'حداکثر '.fa_num($limit).' ردیف در هر ایمپورت مجاز است.');
        }

        $preview = $importer->previewKeywords($csv['rows']);

        session()->put('seo_plan_import', [
            'filename' => $file->getClientOriginalName(),
            'rows' => $csv['rows'],
        ]);

        return view('admin.seo-plan.import-preview', [
            'preview' => $preview,
            'filename' => $file->getClientOriginalName(),
        ]);
    }

    public function import(SeoPlanImporter $importer): RedirectResponse
    {
        $payload = session()->get('seo_plan_import');

        if (! is_array($payload) || ! isset($payload['rows']) || ! is_array($payload['rows'])) {
            return redirect()
                ->route('admin.seo-plan.index', ['tab' => 'keywords'])
                ->with('error', 'فایل ایمپورتی برای تأیید پیدا نشد. دوباره تلاش کنید.');
        }

        $result = $importer->importKeywords($payload['rows']);

        session()->forget('seo_plan_import');

        return redirect()
            ->route('admin.seo-plan.index', ['tab' => 'keywords'])
            ->with('success', $result->summary());
    }

    public function previewTaskImport(Request $request, SeoPlanImporter $importer)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [], [
            'file' => 'فایل CSV',
        ]);

        $file = $request->file('file');
        $csv = CsvReader::read($file->getRealPath());

        $missing = array_values(array_diff(SeoPlanImporter::TASK_COLUMNS, $csv['headers']));

        if ($missing !== []) {
            return back()->with('error', 'ستون‌های ضروری در فایل نیست: '.implode(', ', $missing));
        }

        if ($csv['rows'] === []) {
            return back()->with('error', 'فایل CSV هیچ ردیف داده‌ای ندارد.');
        }

        $limit = (int) config('seo-plan.import_limit', 2000);

        if (count($csv['rows']) > $limit) {
            return back()->with('error', 'حداکثر '.fa_num($limit).' ردیف در هر ایمپورت مجاز است.');
        }

        $preview = $importer->previewTasks($csv['rows']);

        session()->put('seo_plan_task_import', [
            'filename' => $file->getClientOriginalName(),
            'rows' => $csv['rows'],
        ]);

        return view('admin.seo-plan.task-import-preview', [
            'preview' => $preview,
            'filename' => $file->getClientOriginalName(),
        ]);
    }

    public function importTasks(SeoPlanImporter $importer): RedirectResponse
    {
        $payload = session()->get('seo_plan_task_import');

        if (! is_array($payload) || ! isset($payload['rows']) || ! is_array($payload['rows'])) {
            return redirect()
                ->route('admin.seo-plan.index', ['tab' => 'tasks'])
                ->with('error', 'فایل ایمپورتی برای تأیید پیدا نشد. دوباره تلاش کنید.');
        }

        $result = $importer->importTasks($payload['rows']);

        session()->forget('seo_plan_task_import');

        return redirect()
            ->route('admin.seo-plan.index', ['tab' => 'tasks'])
            ->with('success', $result->summary());
    }

    public function exportKeywords(Request $request): StreamedResponse
    {
        $query = SeoKeyword::query()
            ->filter($this->keywordFilters($request))
            ->sort($request->query('sort'), $request->query('direction'));

        $filename = 'seo-keywords-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'wb');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, SeoPlanImporter::KEYWORD_COLUMNS, ',', '"', '');

            $query->chunk(200, function ($keywords) use ($out) {
                foreach ($keywords as $keyword) {
                    fputcsv($out, [
                        $keyword->keyword,
                        $keyword->role,
                        $keyword->target_page,
                        $keyword->target_url,
                        $keyword->page_type,
                        $keyword->intent,
                        $keyword->priority,
                        $keyword->content_title,
                        $keyword->secondary_keywords,
                        $keyword->internal_links,
                        $keyword->status,
                        $keyword->notes,
                    ], ',', '"', '');
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $data = $this->validateTask($request);
        $data['done_at'] = $data['status'] === TaskStatus::Done->value ? now() : null;

        SeoTask::create($data);

        return back()->with('success', 'تسک اضافه شد.');
    }

    public function updateTask(Request $request, SeoTask $task): RedirectResponse
    {
        $data = $this->validateTask($request);
        $data['done_at'] = $data['status'] === TaskStatus::Done->value
            ? ($task->done_at ?? now())
            : null;

        $task->update($data);

        return back()->with('success', 'تسک به‌روزرسانی شد.');
    }

    public function updateTaskStatus(Request $request, SeoTask $task): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(TaskStatus::values())],
        ], [], [
            'status' => 'وضعیت تسک',
        ]);

        $task->changeStatus($data['status']);

        return back();
    }

    public function destroyTask(SeoTask $task): RedirectResponse
    {
        $task->delete();

        return back()->with('success', 'تسک حذف شد.');
    }

    /**
     * Build the keyword graph payload for the graph tab.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function graphData(array $filters): array
    {
        $limit = 300;

        $query = SeoKeyword::query()->filter($filters);
        $total = (clone $query)->count();

        $keywords = $query
            ->withCount([
                'tasks as open_tasks_count' => fn ($query) => $query->where('status', '!=', TaskStatus::Done->value),
            ])
            ->with(['tasks' => fn ($query) => $query->orderBy('status')->orderBy('priority')->orderByDesc('id')])
            ->orderBy('priority')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $nodes = [];

        foreach ($keywords as $keyword) {
            $nodes[] = [
                'id' => $keyword->id,
                'keyword' => $keyword->keyword,
                'role' => $keyword->role,
                'roleLabel' => $keyword->role_label,
                'pageType' => $keyword->page_type,
                'pageTypeLabel' => $keyword->page_type_label,
                'status' => $keyword->status,
                'statusLabel' => $keyword->status_label,
                'priority' => $keyword->priority,
                'volume' => $keyword->volume,
                'competitionLabel' => $keyword->competition_label,
                'position' => $keyword->position,
                'targetPage' => $keyword->target_page,
                'targetUrl' => $keyword->safe_target_url,
                'contentTitle' => $keyword->content_title,
                'secondaryKeywords' => $keyword->secondary_keywords,
                'internalLinks' => $keyword->internal_links,
                'notes' => $keyword->notes,
                'openTasksCount' => (int) $keyword->open_tasks_count,
                'tasks' => $keyword->tasks->map(fn (SeoTask $task) => [
                    'title' => $task->title,
                    'status' => $task->status,
                    'statusLabel' => $task->status_label,
                    'categoryLabel' => $task->category_label,
                    'dueDate' => $task->due_date?->format('Y-m-d'),
                    'dueDateLabel' => $task->due_date ? jalali_date($task->due_date) : null,
                    'isOverdue' => $task->is_overdue,
                ])->values()->all(),
                'groups' => [
                    'page' => $keyword->target_page !== null && $keyword->target_page !== '' ? $keyword->target_page : null,
                    'page_type' => $keyword->page_type,
                    'role' => $keyword->role,
                ],
                'tableUrl' => route('admin.seo-plan.index', ['tab' => 'keywords', 'search' => $keyword->keyword]),
                'taskUrl' => $keyword->page_type === PageType::Blog->value
                    ? route('admin.seo-plan.keywords.task', $keyword)
                    : null,
                'canCreateTask' => $keyword->page_type === PageType::Blog->value
                    && (int) $keyword->open_tasks_count === 0,
            ];
        }

        return [
            'nodes' => $nodes,
            'related' => $this->relatedKeywordEdges($keywords),
            'total' => $total,
            'truncated' => $total > count($nodes),
            'labels' => [
                'page_types' => PageType::options(),
                'roles' => KeywordRole::options(),
                'statuses' => KeywordStatus::options(),
            ],
        ];
    }

    /**
     * Link keywords that are mentioned in another keyword's secondary keywords or internal links.
     *
     * @param  \Illuminate\Support\Collection<int, SeoKeyword>  $keywords
     * @return list<array{source: int, target: int}>
     */
    private function relatedKeywordEdges($keywords): array
    {
        $normalized = [];

        foreach ($keywords as $keyword) {
            $normalized[$keyword->id] = $this->normalizeText($keyword->keyword);
        }

        $edges = [];

        foreach ($keywords as $keyword) {
            $phrases = $this->splitPhrases($keyword->secondary_keywords.' '.$keyword->internal_links);
            $matches = 0;

            foreach ($phrases as $phrase) {
                $phrase = $this->normalizeText($phrase);

                if (mb_strlen($phrase) < 8) {
                    continue;
                }

                foreach ($normalized as $otherId => $other) {
                    if ($otherId === $keyword->id || mb_strlen($other) < 6) {
                        continue;
                    }

                    if (! str_contains($other, $phrase) && ! str_contains($phrase, $other)) {
                        continue;
                    }

                    $source = min($keyword->id, $otherId);
                    $target = max($keyword->id, $otherId);

                    $edges[$source.':'.$target] = ['source' => $source, 'target' => $target];

                    if (++$matches >= 4) {
                        break 2;
                    }
                }
            }
        }

        return array_values($edges);
    }

    /**
     * @return list<string>
     */
    private function splitPhrases(?string $text): array
    {
        if ($text === null || $text === '') {
            return [];
        }

        $parts = preg_split('/[،,;؛\n\r]+/u', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn (string $part) => $part !== ''));
    }

    private function normalizeText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $text = str_replace(
            ["\u{200c}", "\u{200f}", "\u{200e}", 'ي', 'ك', 'ۀ', 'ة', 'أ', 'إ', 'آ'],
            ['', '', '', 'ی', 'ک', 'ه', 'ه', 'ا', 'ا', 'ا'],
            $text,
        );

        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return mb_strtolower(trim($text));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateKeyword(Request $request): array
    {
        return $request->validate([
            'keyword' => ['required', 'string', 'max:191'],
            'role' => ['required', Rule::in(KeywordRole::values())],
            'target_page' => ['nullable', 'string', 'max:255'],
            'target_url' => ['nullable', 'string', 'max:255', 'regex:/^(\/|https?:\/\/).*$/i'],
            'page_type' => ['required', Rule::in(PageType::values())],
            'intent' => ['required', Rule::in(SearchIntent::values())],
            'priority' => ['required', 'integer', Rule::in([1, 2, 3])],
            'volume' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'competition' => ['nullable', Rule::in(Competition::values())],
            'position' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status' => ['required', Rule::in(KeywordStatus::values())],
            'content_title' => ['nullable', 'string', 'max:255'],
            'secondary_keywords' => ['nullable', 'string'],
            'internal_links' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ], [
            'target_url.regex' => 'آدرس صفحه هدف باید با / یا http شروع شود.',
        ], [
            'keyword' => 'کلمه کلیدی',
            'role' => 'نقش',
            'target_page' => 'صفحه هدف',
            'target_url' => 'آدرس صفحه هدف',
            'page_type' => 'نوع صفحه',
            'intent' => 'قصد جستجو',
            'priority' => 'اولویت',
            'volume' => 'حجم جستجو',
            'competition' => 'رقابت',
            'position' => 'رتبه',
            'status' => 'وضعیت',
            'content_title' => 'عنوان مقاله',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', Rule::in(TaskCategory::values())],
            'priority' => ['required', 'integer', Rule::in([1, 2, 3])],
            'status' => ['required', Rule::in(TaskStatus::values())],
            'keyword_id' => ['nullable', 'integer', 'exists:seo_keywords,id'],
            'due_date' => ['nullable', 'date'],
        ], [], [
            'title' => 'عنوان تسک',
            'category' => 'دسته',
            'priority' => 'اولویت',
            'status' => 'وضعیت',
            'keyword_id' => 'کلمه مرتبط',
            'due_date' => 'تاریخ سررسید',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function keywordFilters(Request $request): array
    {
        return array_filter([
            'search' => $request->query('search'),
            'status' => $request->query('status'),
            'priority' => $request->query('priority'),
            'page_type' => $request->query('page_type'),
            'target_page' => $request->query('target_page'),
            'intent' => $request->query('intent'),
            'writing_plan' => $request->boolean('writing_plan') ? 1 : null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @return array<string, mixed>
     */
    private function taskFilters(Request $request): array
    {
        return array_filter([
            'search' => $request->query('task_search'),
            'category' => $request->query('task_category'),
            'priority' => $request->query('task_priority'),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
