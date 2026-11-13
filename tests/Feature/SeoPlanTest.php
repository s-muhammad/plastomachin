<?php

use App\Models\SeoKeyword;
use App\Models\SeoTask;
use App\Models\User;
use Database\Seeders\SeoPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

function seoPlanAdmin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

function makeSeoKeyword(array $overrides = []): SeoKeyword
{
    return SeoKeyword::create(array_merge([
        'keyword' => 'کلمه تست',
        'role' => 'secondary',
        'target_page' => 'صفحه تست',
        'target_url' => '/test/',
        'page_type' => 'service',
        'intent' => 'commercial',
        'priority' => 2,
        'status' => 'planned',
    ], $overrides));
}

function makeSeoTask(array $overrides = []): SeoTask
{
    return SeoTask::create(array_merge([
        'title' => 'تسک تست',
        'category' => 'technical',
        'priority' => 2,
        'status' => 'todo',
    ], $overrides));
}

test('the seo plan seeder is idempotent and keeps user edits', function () {
    $directory = storage_path('framework/testing/seo-plan-'.uniqid());
    File::ensureDirectoryExists($directory);

    $keywordsPath = $directory.'/keywords.csv';
    $tasksPath = $directory.'/tasks.csv';

    File::put($keywordsPath, <<<'CSV'
keyword,role,target_page,target_url,page_type,intent,priority,content_title,secondary_keywords,internal_links,status,notes
کلمه سیدر یک,main,صفحه اصلی,/,home,commercial,1,,,,planned,یادداشت اولیه
کلمه سیدر دو,secondary,دستگاه تست,/test-machine/,service,commercial,2,,,,planned,
CSV);

    File::put($tasksPath, <<<'CSV'
title,description,category,priority,status
تسک سیدر یک,توضیح تست,technical,1,todo
CSV);

    config([
        'seo-plan.keywords_path' => $keywordsPath,
        'seo-plan.tasks_path' => $tasksPath,
    ]);

    $this->seed(SeoPlanSeeder::class);
    $this->seed(SeoPlanSeeder::class);

    expect(SeoKeyword::count())->toBe(2)
        ->and(SeoTask::count())->toBe(1);

    $keyword = SeoKeyword::where('keyword', 'کلمه سیدر یک')->firstOrFail();
    $keyword->update([
        'volume' => 1200,
        'position' => 4,
        'status' => 'published',
        'notes' => 'ویرایش کاربر',
    ]);

    $this->seed(SeoPlanSeeder::class);

    $keyword->refresh();

    expect(SeoKeyword::count())->toBe(2)
        ->and(SeoTask::count())->toBe(1)
        ->and($keyword->volume)->toBe(1200)
        ->and($keyword->position)->toBe(4)
        ->and($keyword->status)->toBe('published')
        ->and($keyword->notes)->toBe('ویرایش کاربر');

    File::deleteDirectory($directory);
});

test('the writing plan filter only returns planned priority one keywords', function () {
    makeSeoKeyword([
        'keyword' => 'کلمه برنامه نوشتن',
        'target_page' => 'صفحه الف',
        'status' => 'planned',
        'priority' => 1,
    ]);

    makeSeoKeyword([
        'keyword' => 'کلمه اولویت دو',
        'target_page' => 'صفحه ب',
        'status' => 'planned',
        'priority' => 2,
    ]);

    makeSeoKeyword([
        'keyword' => 'کلمه منتشر شده',
        'target_page' => 'صفحه ج',
        'status' => 'published',
        'priority' => 1,
    ]);

    $response = $this->actingAs(seoPlanAdmin())
        ->get(route('admin.seo-plan.index', ['tab' => 'keywords', 'writing_plan' => 1]));

    $response->assertOk()
        ->assertSee('کلمه برنامه نوشتن')
        ->assertDontSee('کلمه اولویت دو')
        ->assertDontSee('کلمه منتشر شده')
        ->assertViewHas('keywords', fn ($keywords) => $keywords->total() === 1);
});

test('a blog keyword can create a writing task only once while it is open', function () {
    $keyword = makeSeoKeyword([
        'keyword' => 'مقاله تست',
        'page_type' => 'blog',
        'target_page' => null,
        'target_url' => null,
        'content_title' => 'عنوان پیشنهادی مقاله',
    ]);

    $this->actingAs(seoPlanAdmin())
        ->post(route('admin.seo-plan.keywords.task', $keyword))
        ->assertRedirect();

    $this->assertDatabaseHas('seo_tasks', [
        'keyword_id' => $keyword->id,
        'title' => 'نوشتن مقاله: مقاله تست',
        'category' => 'content',
        'status' => 'todo',
    ]);

    $this->actingAs(seoPlanAdmin())
        ->post(route('admin.seo-plan.keywords.task', $keyword))
        ->assertSessionHas('error');

    expect(SeoTask::where('keyword_id', $keyword->id)->count())->toBe(1);
});

test('a task status change fills done_at and clears it when reopened', function () {
    $task = makeSeoTask();

    $this->actingAs(seoPlanAdmin())
        ->patch(route('admin.seo-plan.tasks.status', $task), ['status' => 'done'])
        ->assertRedirect();

    $task->refresh();

    expect($task->status)->toBe('done')
        ->and($task->done_at)->not->toBeNull();

    $this->actingAs(seoPlanAdmin())
        ->patch(route('admin.seo-plan.tasks.status', $task), ['status' => 'doing'])
        ->assertRedirect();

    $task->refresh();

    expect($task->status)->toBe('doing')
        ->and($task->done_at)->toBeNull();
});

test('the tasks tab renders the kanban board and task details', function () {
    $keyword = makeSeoKeyword(['keyword' => 'کلمه مرتبط با تسک']);

    makeSeoTask([
        'title' => 'تسک انجام شده',
        'status' => 'done',
        'done_at' => now(),
        'keyword_id' => $keyword->id,
    ]);

    makeSeoTask([
        'title' => 'تسک در حال انجام',
        'status' => 'doing',
        'priority' => 1,
        'due_date' => today()->subDay(),
    ]);

    $this->actingAs(seoPlanAdmin())
        ->get(route('admin.seo-plan.index', ['tab' => 'tasks']))
        ->assertOk()
        ->assertSee('تسک انجام شده')
        ->assertSee('تسک در حال انجام')
        ->assertSee('کلمه مرتبط با تسک')
        ->assertSee('گذشته از سررسید');
});

test('csv import previews and imports only new keywords', function () {
    makeSeoKeyword([
        'keyword' => 'کلمه موجود',
        'target_url' => '/existing/',
    ]);

    $csv = <<<'CSV'
keyword,role,target_page,target_url,page_type,intent,priority,content_title,secondary_keywords,internal_links,status,notes
کلمه موجود,secondary,صفحه موجود,/existing/,service,commercial,2,,,,planned,
کلمه جدید,main,صفحه جدید,/new/,service,commercial,1,,,,planned,
CSV;

    $file = UploadedFile::fake()->createWithContent('keywords.csv', $csv);

    $this->actingAs(seoPlanAdmin())
        ->post(route('admin.seo-plan.keywords.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('کلمه جدید')
        ->assertSee('تکراری');

    $this->actingAs(seoPlanAdmin())
        ->post(route('admin.seo-plan.keywords.import'))
        ->assertRedirect(route('admin.seo-plan.index', ['tab' => 'keywords']));

    expect(SeoKeyword::where('keyword', 'کلمه جدید')->exists())->toBeTrue()
        ->and(SeoKeyword::where('keyword', 'کلمه موجود')->count())->toBe(1);
});

test('csv task import previews and imports only new tasks', function () {
    makeSeoTask([
        'title' => 'تسک موجود',
        'category' => 'technical',
        'status' => 'todo',
    ]);

    $csv = <<<'CSV'
title,description,category,priority,status
تسک موجود,توضیح موجود,technical,2,todo
تسک جدید,توضیح جدید,content,1,doing
CSV;

    $file = UploadedFile::fake()->createWithContent('tasks.csv', $csv);

    $this->actingAs(seoPlanAdmin())
        ->post(route('admin.seo-plan.tasks.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('تسک جدید')
        ->assertSee('تکراری');

    $this->actingAs(seoPlanAdmin())
        ->post(route('admin.seo-plan.tasks.import'))
        ->assertRedirect(route('admin.seo-plan.index', ['tab' => 'tasks']));

    expect(SeoTask::where('title', 'تسک جدید')->where('status', 'doing')->exists())->toBeTrue()
        ->and(SeoTask::where('title', 'تسک موجود')->count())->toBe(1);
});

test('only admins can open the seo plan page', function () {
    $this->get(route('admin.seo-plan.index'))->assertRedirect(route('admin.login'));

    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.seo-plan.index'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();

    $this->actingAs(seoPlanAdmin())
        ->get(route('admin.seo-plan.index'))
        ->assertOk()
        ->assertSee('برنامه سئو');
});
