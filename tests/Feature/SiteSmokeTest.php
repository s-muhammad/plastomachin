<?php

use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Post;
use App\Models\SeoPage;
use App\Models\ServicePage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

function adminUser(): User
{
    return User::where('is_admin', true)->firstOrFail();
}

function plainText(string $html): string
{
    return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html))));
}

test('all public pages respond and contain their main content', function () {
    $service = ServicePage::firstOrFail();
    $post = Post::firstOrFail();

    $this->get('/')->assertOk()->assertSee('حوزه‌های کاری');
    $this->get(route('services.show', $service->slug))->assertOk()->assertSee($service->h1);
    $this->get(route('contact'))->assertOk()->assertSee('پیام خود را بفرستید');
    $this->get(route('blog.index'))->assertOk()->assertSee('مقالات');
    $this->get(route('blog.show', $post->slug))->assertOk()->assertSee($post->title);
    $this->get(route('faq'))->assertOk()->assertSee('سوالات متداول');
    $this->get(route('sitemap'))->assertOk();
    $this->get(route('robots'))->assertOk()->assertSee('Sitemap:');
});

test('the home page h1 matches the requested headline exactly', function () {
    $html = $this->get('/')->getContent();

    preg_match('/<h1[^>]*>(.*?)<\/h1>/su', $html, $matches);

    expect($matches)->toHaveCount(2)
        ->and(plainText($matches[1]))->toBe('خرید و فروش دستگاه تولید و دوخت نایلون در شهرک تجریشی');
});

test('the sitemap is valid xml with a declaration', function () {
    $response = $this->get(route('sitemap'));

    $response->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $body = $response->getContent();

    expect(Str::startsWith($body, '<?xml version="1.0" encoding="UTF-8"?>'))->toBeTrue()
        ->and($body)->toContain('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
        ->and(simplexml_load_string($body))->not->toBeFalse();
});

test('public html has no external asset dependencies', function () {
    foreach (['/', route('contact'), route('faq'), route('blog.index')] as $url) {
        $this->get($url)
            ->assertOk()
            ->assertDontSee('fonts.googleapis.com')
            ->assertDontSee('fonts.gstatic.com')
            ->assertDontSee('cdn.jsdelivr.net')
            ->assertDontSee('cdnjs.cloudflare.com')
            ->assertDontSee('unpkg.com')
            ->assertDontSee('use.fontawesome.com')
            ->assertDontSee('tailwindcss.com');
    }
});

test('social icons stay hidden until enabled in settings', function () {
    $this->get('/')->assertDontSee('aria-label="اینستاگرام"', false);

    Setting::setMany(['socials_enabled' => '1', 'instagram' => 'https://instagram.com/example']);

    $this->get('/')->assertSee('aria-label="اینستاگرام"', false);
});

test('the admin area is protected and has no registration route', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
    $this->get('/admin/register')->assertNotFound();
    $this->get('/admin/login')->assertOk()->assertSee('ورود به پنل مدیریت');
});

test('an admin can log in and every admin page renders', function () {
    $admin = adminUser();

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'Admin@1234',
    ])->assertRedirect('/admin');

    $this->assertAuthenticatedAs($admin);

    $service = ServicePage::firstOrFail();
    $post = Post::firstOrFail();
    $faq = Faq::general()->firstOrFail();

    $this->get('/admin')->assertOk()->assertSee('داشبورد');
    $this->get(route('admin.services.index'))->assertOk();
    $this->get(route('admin.services.create'))->assertOk();
    $this->get(route('admin.services.edit', $service))->assertOk();
    $this->get(route('admin.posts.index'))->assertOk();
    $this->get(route('admin.posts.create'))->assertOk();
    $this->get(route('admin.posts.edit', $post))->assertOk();
    $this->get(route('admin.faqs.index'))->assertOk();
    $this->get(route('admin.faqs.create'))->assertOk();
    $this->get(route('admin.faqs.edit', $faq))->assertOk();
    $this->get(route('admin.messages.index'))->assertOk();
    $this->get(route('admin.settings.edit'))->assertOk();
    $this->get(route('admin.seo.index'))->assertOk();
});

test('service pages can be created, updated, toggled, moved and deleted', function () {
    $payload = [
        'title' => 'صفحه تست',
        'slug' => '',
        'h1' => 'تیتر تست صفحه',
        'excerpt' => 'خلاصه تست',
        'body' => '<p>متن تست</p>',
        'is_active' => '1',
        'sort_order' => 99,
        'faqs' => [
            ['question' => 'سوال تست؟', 'answer' => 'پاسخ تست.'],
            ['question' => '', 'answer' => ''],
        ],
    ];

    $this->actingAs(adminUser())
        ->post(route('admin.services.store'), $payload)
        ->assertRedirect(route('admin.services.index'));

    $service = ServicePage::where('title', 'صفحه تست')->firstOrFail();

    expect($service->slug)->not->toBe('')
        ->and($service->faqs)->toHaveCount(1)
        ->and($service->sort_order)->toBe(99);

    $this->get(route('services.show', $service->slug))->assertOk()->assertSee('تیتر تست صفحه');

    $this->actingAs(adminUser())
        ->put(route('admin.services.update', $service), [...$payload, 'title' => 'صفحه تست ویرایش‌شده'])
        ->assertRedirect(route('admin.services.index'));

    expect($service->fresh()->title)->toBe('صفحه تست ویرایش‌شده');

    $this->actingAs(adminUser())->post(route('admin.services.toggle', $service))->assertRedirect();
    expect($service->fresh()->is_active)->toBeFalse();

    $this->actingAs(adminUser())->post(route('admin.services.move', [$service, 'up']))->assertRedirect();

    $this->actingAs(adminUser())->delete(route('admin.services.destroy', $service))->assertRedirect();
    expect(ServicePage::find($service->id))->toBeNull();
});

test('draft posts stay hidden until published', function () {
    $payload = [
        'title' => 'مقاله تست',
        'slug' => 'مقاله-تست',
        'excerpt' => 'خلاصه مقاله تست',
        'body' => '<p>متن مقاله</p>',
        'status' => 'draft',
        'published_at' => '',
    ];

    $this->actingAs(adminUser())
        ->post(route('admin.posts.store'), $payload)
        ->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'مقاله تست')->firstOrFail();

    $this->get(route('blog.index'))->assertOk()->assertDontSee('مقاله تست');
    $this->get(route('blog.show', $post->slug))->assertNotFound();

    $this->actingAs(adminUser())
        ->put(route('admin.posts.update', $post), [...$payload, 'status' => 'published'])
        ->assertRedirect(route('admin.posts.index'));

    $this->get(route('blog.index'))->assertOk()->assertSee('مقاله تست');
    $this->get(route('blog.show', $post->slug))->assertOk();

    $this->actingAs(adminUser())->delete(route('admin.posts.destroy', $post))->assertRedirect();
});

test('general faqs can be managed', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.faqs.store'), [
            'question' => 'سوال جدید تست؟',
            'answer' => 'پاسخ جدید تست.',
            'sort_order' => 20,
        ])
        ->assertRedirect(route('admin.faqs.index'));

    $faq = Faq::where('question', 'سوال جدید تست؟')->firstOrFail();

    $this->actingAs(adminUser())
        ->put(route('admin.faqs.update', $faq), [
            'question' => 'سوال ویرایش‌شده؟',
            'answer' => 'پاسخ ویرایش‌شده.',
            'sort_order' => 21,
        ])
        ->assertRedirect(route('admin.faqs.index'));

    expect($faq->fresh()->question)->toBe('سوال ویرایش‌شده؟');

    $this->actingAs(adminUser())->delete(route('admin.faqs.destroy', $faq))->assertRedirect();
    expect(Faq::find($faq->id))->toBeNull();
});

test('the contact form validates, blocks bots and stores messages', function () {
    $this->post(route('contact.store'), [
        'name' => 'ت',
        'phone' => '123',
        'message' => 'سلام',
    ])->assertSessionHasErrors(['name', 'phone']);

    $this->post(route('contact.store'), [
        'name' => 'کاربر تست',
        'phone' => '09120000000',
        'message' => 'نیاز به مشاوره',
        'source_page' => '/contact',
        'website' => 'bot',
    ])->assertRedirect();

    expect(ContactMessage::count())->toBe(0);

    $this->post(route('contact.store'), [
        'name' => 'کاربر تست',
        'phone' => '09120000000',
        'message' => 'نیاز به مشاوره',
        'source_page' => '/contact',
        'website' => '',
    ])->assertSessionHas('success');

    $message = ContactMessage::firstOrFail();

    expect($message->phone)->toBe('09120000000')
        ->and($message->status)->toBe('new');

    $this->actingAs(adminUser())->get(route('admin.messages.show', $message))->assertOk()->assertSee('کاربر تست');

    $this->actingAs(adminUser())
        ->patch(route('admin.messages.update', $message), ['status' => 'followed'])
        ->assertRedirect();

    expect($message->fresh()->status)->toBe('followed');

    $this->actingAs(adminUser())->delete(route('admin.messages.destroy', $message))->assertRedirect();
    expect(ContactMessage::find($message->id))->toBeNull();
});

test('settings and seo overrides are saved and reflected on the site', function () {
    $values = Setting::values();

    $this->actingAs(adminUser())
        ->put(route('admin.settings.update'), [
            'site_name' => $values['site_name'],
            'tagline' => $values['tagline'],
            'phone' => '09121111111',
            'whatsapp' => '989121111111',
            'address' => $values['address'],
            'working_hours' => $values['working_hours'],
            'map_url' => $values['map_url'],
            'instagram' => '',
            'telegram' => '',
            'meta_title' => $values['meta_title'],
            'meta_description' => $values['meta_description'],
            'socials_enabled' => '1',
        ])
        ->assertRedirect();

    expect(Setting::get('phone'))->toBe('09121111111')
        ->and(Setting::get('socials_enabled'))->toBe('1');

    $page = SeoPage::where('key', 'home')->firstOrFail();

    $this->actingAs(adminUser())
        ->put(route('admin.seo.update', $page), [
            'title' => 'عنوان سئوی تست',
            'description' => 'توضیح سئوی تست',
            'canonical' => 'https://plastomachin.ir/',
            'og_title' => 'OG تست',
            'og_description' => 'OG description',
        ])
        ->assertRedirect();

    $this->get('/')->assertOk()->assertSee('<title>عنوان سئوی تست</title>', false);
});

test('admin users can log out', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});
