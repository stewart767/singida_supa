<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Create admin user for testing
        $adminRole = Role::where('name', 'super_admin')->first();
        $this->adminUser = User::factory()->create();
        if ($adminRole) {
            $this->adminUser->roles()->attach($adminRole);
        }
    }

    public function test_public_news_page_loads_and_searches()
    {
        News::create([
            'title' => 'Admission Selection List 2026',
            'slug' => 'admission-selection-list-2026',
            'summary' => 'The list of selected applicants is released.',
            'content' => 'Please download the selection list from the attachments.',
            'published_at' => now(),
            'is_featured' => true,
        ]);

        News::create([
            'title' => 'Fee Payment Guidelines',
            'slug' => 'fee-payment-guidelines',
            'summary' => 'Instructions on how to generate control numbers.',
            'content' => 'Follow these steps to pay application fee.',
            'published_at' => now(),
            'is_featured' => false,
        ]);

        // Load page without search
        $response = $this->get(route('public.news'));
        $response->assertStatus(200);
        $response->assertSee('Admission Selection List 2026');
        $response->assertSee('Fee Payment Guidelines');

        // Load page with search matching first article
        $response = $this->get(route('public.news', ['search' => 'Selection']));
        $response->assertStatus(200);
        $response->assertSee('Admission Selection List 2026');
        $response->assertDontSee('Fee Payment Guidelines');
    }

    public function test_public_news_details_page_loads()
    {
        $news = News::create([
            'title' => 'Important Orientation Guide',
            'slug' => 'important-orientation-guide',
            'summary' => 'Orientation guide for freshers.',
            'content' => 'Orientation week starts next Monday.',
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.news.show', $news->slug));
        $response->assertStatus(200);
        $response->assertSee('Important Orientation Guide');
        $response->assertSee('Orientation week starts next Monday');
    }

    public function test_admin_can_publish_and_update_news()
    {
        Storage::fake('public');

        $this->actingAs($this->adminUser);

        // 1. Create News
        $image = UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg');
        $storeResponse = $this->post(route('admin.cms.news.store'), [
            'title' => 'New Portal Launched',
            'summary' => 'Welcome to the new portal.',
            'content' => 'This is the full content of the post.',
            'image' => $image,
            'is_featured' => '1',
            'published_at' => '2026-07-29',
        ]);

        $storeResponse->assertStatus(200);
        $this->assertDatabaseHas('cms_news', [
            'title' => 'New Portal Launched',
            'is_featured' => true,
        ]);

        $newsItem = News::where('title', 'New Portal Launched')->first();
        $this->assertNotNull($newsItem->image_path);
        Storage::disk('public')->assertExists($newsItem->image_path);

        $oldImagePath = $newsItem->image_path;

        // 2. Update News (regeneration of slug, replacing image, nullable date)
        $newImage = UploadedFile::fake()->create('new_cover.jpg', 100, 'image/jpeg');
        $updateResponse = $this->put(route('admin.cms.news.update', $newsItem), [
            'title' => 'Portal Launched Corrected Title',
            'summary' => 'Corrected summary.',
            'content' => 'Updated content text.',
            'image' => $newImage,
            'is_featured' => '0',
            'published_at' => '', // should allow null
        ]);

        $updateResponse->assertStatus(200);
        
        $newsItem->refresh();
        $this->assertEquals('Portal Launched Corrected Title', $newsItem->title);
        $this->assertFalse($newsItem->is_featured);
        $this->assertNull($newsItem->published_at); // verify nullable conversion
        $this->assertNotEquals($oldImagePath, $newsItem->image_path);
        
        // Old image should be deleted from storage
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($newsItem->image_path);

        // Slug should have updated
        $this->assertStringContainsString('portal-launched-corrected-title', $newsItem->slug);
    }

    public function test_admin_can_delete_news_and_clean_files()
    {
        Storage::fake('public');

        $this->actingAs($this->adminUser);

        $news = News::create([
            'title' => 'Delete Me Later',
            'slug' => 'delete-me-later',
            'summary' => 'Summary content.',
            'content' => 'Content text.',
            'image_path' => 'news/fake_image.png',
            'published_at' => now(),
        ]);

        Storage::disk('public')->put('news/fake_image.png', 'content');
        Storage::disk('public')->assertExists('news/fake_image.png');

        $response = $this->delete(route('admin.cms.news.destroy', $news));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('cms_news', ['id' => $news->id]);
        Storage::disk('public')->assertMissing('news/fake_image.png');
    }

    public function test_homepage_announcements_section_displays_news()
    {
        News::create([
            'title' => 'Featured Homepage Alert',
            'slug' => 'featured-homepage-alert',
            'summary' => 'This alert shows on homepage.',
            'content' => 'Content text.',
            'is_featured' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Featured Homepage Alert');
    }

    public function test_news_announcements_can_be_disabled()
    {
        $news = News::create([
            'title' => 'Test News Item',
            'slug' => 'test-news-item',
            'summary' => 'Summary text.',
            'content' => 'Content text.',
            'is_featured' => true,
            'published_at' => now(),
        ]);

        // Verify default is true and route works
        $response = $this->get(route('public.news'));
        $response->assertStatus(200);

        // Update settings to disable news & announcements
        \App\Models\Setting::set('show_news_announcements', false, 'admission', 'boolean');

        // Check routes now return 404
        $response = $this->get(route('public.news'));
        $response->assertStatus(404);

        $response = $this->get(route('public.news.show', $news->slug));
        $response->assertStatus(404);

        // Check homepage does not see the alert
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('Test News Item');
        $response->assertDontSee('Latest Announcements');

        // Test saving settings via admin endpoint
        $this->actingAs($this->adminUser);
        $settingsResponse = $this->post(route('admin.cms.settings'), [
            'showNewsAnnouncements' => '1',
        ]);
        $settingsResponse->assertStatus(200);
        $this->assertTrue(\App\Models\Setting::get('show_news_announcements'));
        
        $settingsResponse = $this->post(route('admin.cms.settings'), [
            'showNewsAnnouncements' => '0',
        ]);
        $settingsResponse->assertStatus(200);
        $this->assertFalse(\App\Models\Setting::get('show_news_announcements'));
    }

    public function test_admin_can_update_cta_background_image()
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser);

        $image = \Illuminate\Http\UploadedFile::fake()->create('cta_bg.webp', 200, 'image/webp');
        $response = $this->post(route('admin.cms.about'), [
            'title' => 'Test About Title',
            'cta_background_image' => $image,
        ]);

        $response->assertStatus(200);
        
        $ctaBg = \App\Models\Setting::get('cta_background_image');
        $this->assertNotNull($ctaBg);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($ctaBg);

        // Verify homepage shows it
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee(asset('storage/' . $ctaBg));
    }

    public function test_admin_can_update_logos_and_renders_on_frontend()
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser);

        $sttcImg = \Illuminate\Http\UploadedFile::fake()->create('sttc_logo.png', 100, 'image/png');
        $outImg = \Illuminate\Http\UploadedFile::fake()->create('out_logo.png', 100, 'image/png');

        $response = $this->post(route('admin.cms.logos'), [
            'sttc_logo' => $sttcImg,
            'out_logo' => $outImg,
            'university_name' => 'Singida STTC & OUT Joint Portal',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);

        $sttcPath = \App\Models\Setting::get('sttc_logo');
        $outPath = \App\Models\Setting::get('out_logo');

        $this->assertNotNull($sttcPath);
        $this->assertNotNull($outPath);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($sttcPath);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($outPath);

        // Verify homepage shows both logos
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee(asset('storage/' . $sttcPath));
        $homeResponse->assertSee(asset('storage/' . $outPath));
    }

    public function test_admin_can_update_page_banner_background_image_and_renders_on_frontend()
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser);

        $bannerImg = \Illuminate\Http\UploadedFile::fake()->create('news_banner.jpg', 800, 'image/jpeg');

        // Post request to save a new page banner background
        $response = $this->post(route('admin.cms.page-banners'), [
            'key' => 'news',
            'image' => $bannerImg,
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'url']);

        $bannerPath = \App\Models\Setting::get('banner_news');
        $this->assertNotNull($bannerPath);
        Storage::disk('public')->assertExists($bannerPath);

        // Verify public news page renders the background image
        $newsResponse = $this->get(route('public.news'));
        $newsResponse->assertStatus(200);
        $newsResponse->assertSee(asset('storage/' . $bannerPath));

        // Delete request to remove the custom banner background
        $deleteResponse = $this->post(route('admin.cms.page-banners.delete'), [
            'key' => 'news'
        ], [
            'Accept' => 'application/json',
        ]);

        $deleteResponse->assertStatus(200);
        $this->assertEquals('', \App\Models\Setting::get('banner_news'));
    }

    public function test_admin_can_update_policies_and_renders_on_frontend()
    {
        // 1. Unauthenticated guest gets redirected or forbidden on POST
        $guestResponse = $this->post(route('admin.cms.policies'), [
            'privacy_policy_content' => '<p>Updated Guest Privacy Policy Content</p>',
            'terms_conditions_content' => '<p>Updated Guest Terms Content</p>',
        ], [
            'Accept' => 'application/json',
        ]);
        $guestResponse->assertStatus(401);

        // 2. Authenticated Admin User can update
        $this->actingAs($this->adminUser);

        $response = $this->post(route('admin.cms.policies'), [
            'privacy_policy_content' => '<h2>Custom Privacy Header</h2><p>Updated Privacy Policy Content</p>',
            'terms_conditions_content' => '<h2>Custom Terms Header</h2><p>Updated Terms Content</p>',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Privacy Policy & Terms updated successfully!'
        ]);

        // Assert settings saved
        $this->assertEquals('<h2>Custom Privacy Header</h2><p>Updated Privacy Policy Content</p>', \App\Models\Setting::get('privacy_policy_content'));
        $this->assertEquals('<h2>Custom Terms Header</h2><p>Updated Terms Content</p>', \App\Models\Setting::get('terms_conditions_content'));

        // 3. Frontend pages load and display the updated content
        $privacyResponse = $this->get(route('public.privacy'));
        $privacyResponse->assertStatus(200);
        $privacyResponse->assertSee('Custom Privacy Header');
        $privacyResponse->assertSee('Updated Privacy Policy Content');

        $termsResponse = $this->get(route('public.terms'));
        $termsResponse->assertStatus(200);
        $termsResponse->assertSee('Custom Terms Header');
        $termsResponse->assertSee('Updated Terms Content');
    }

    public function test_admin_can_update_hero_sliders_and_renders_on_frontend()
    {
        Storage::fake('public');
        $this->actingAs($this->adminUser);

        $customBanners = [
            [
                'id' => 101,
                'title' => 'Custom Banner Title',
                'subtitle' => 'Custom Banner Subtitle',
                'cta' => 'Custom Apply Button',
                'cta_link' => 'public.requirements', // Resolves route name
                'secondary_cta' => 'Custom Contact Button',
                'secondary_cta_link' => '/custom-path', // Absolute path
                'image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070',
                'status' => 'Active'
            ]
        ];

        $response = $this->post(route('admin.cms.sliders'), [
            'banners' => json_encode($customBanners)
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);

        // Verify homepage renders custom titles and custom button texts and routes
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Custom Banner Title');
        $homeResponse->assertSee('Custom Banner Subtitle');
        
        // Assert button text & link
        $homeResponse->assertSee('Custom Apply Button');
        $homeResponse->assertSee(route('public.requirements')); // Resolved route name
        
        $homeResponse->assertSee('Custom Contact Button');
        $homeResponse->assertSee('/custom-path'); // Relative URL path
    }
}
