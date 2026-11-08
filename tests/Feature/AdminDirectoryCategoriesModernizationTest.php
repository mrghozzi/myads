<?php

namespace Tests\Feature;

use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\SiteAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AdminDirectoryCategoriesModernizationTest extends TestCase
{
    use RefreshDatabase, SeedsSiteSettings;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();

        $this->admin = User::factory()->create(['id' => 1]);
        SiteAdmin::create([
            'user_id' => $this->admin->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin)->withSession(['admin_password_confirmed_at' => time()]);
    }

    public function test_admin_can_view_directory_categories_page_with_stats_and_tree(): void
    {
        $root = DirectoryCategory::create([
            'name' => 'Root Technology',
            'slug' => 'root-technology-' . time(),
            'icon' => 'fa-solid fa-laptop-code',
            'sub' => 0,
            'ordercat' => 1,
            'statu' => 1,
            'txt' => 'Tech sites and tools',
        ]);

        $child = DirectoryCategory::create([
            'name' => 'Web Development',
            'slug' => 'web-dev-' . time(),
            'icon' => 'fa-solid fa-code',
            'sub' => $root->id,
            'ordercat' => 1,
            'statu' => 1,
            'txt' => 'Frontend and backend development tools',
        ]);

        $response = $this->actingAsAdmin()->get(route('admin.directory_categories'));

        $response->assertStatus(200);
        $response->assertViewHas('rootCategories');
        $response->assertViewHas('allCategories');
        $response->assertViewHas('stats');
        $response->assertSee('Root Technology');
        $response->assertSee('Web Development');
        $response->assertSee('kpiTotalCategories');
        $response->assertSee('dirTreeRoot');
        $response->assertSee('categoryModal');
    }

    public function test_admin_can_create_category_with_auto_generated_slug_and_icon(): void
    {
        $response = $this->actingAsAdmin()->post(route('admin.directory_categories.store'), [
            'name' => 'محركات البحث الحديثة',
            'slug' => '', // leave empty to test auto-generation
            'icon' => 'fa-solid fa-magnifying-glass',
            'sub' => 0,
            'ordercat' => 5,
            'statu' => 1,
            'txt' => 'أدلة ومحركات البحث على الويب',
            'metakeywords' => 'search, engines, web',
        ]);

        $response->assertSessionHas('success');

        $category = DirectoryCategory::where('name', 'محركات البحث الحديثة')->first();
        $this->assertNotNull($category);
        $this->assertNotEmpty($category->slug);
        $this->assertEquals('fa-solid fa-magnifying-glass', $category->icon);
        $this->assertEquals(0, $category->sub);
        $this->assertEquals(5, $category->ordercat);
        $this->assertEquals(1, $category->statu);
    }

    public function test_admin_can_create_subcategory_under_parent(): void
    {
        $parent = DirectoryCategory::create([
            'name' => 'Parent Category',
            'slug' => 'parent-cat-' . time(),
            'sub' => 0,
            'ordercat' => 0,
            'statu' => 1,
        ]);

        $response = $this->actingAsAdmin()->post(route('admin.directory_categories.store'), [
            'name' => 'Child Subcategory',
            'slug' => 'child-sub-' . time(),
            'icon' => 'fa-solid fa-child',
            'sub' => $parent->id,
            'ordercat' => 1,
            'statu' => 1,
            'txt' => 'Child description',
        ]);

        $response->assertSessionHas('success');

        $child = DirectoryCategory::where('name', 'Child Subcategory')->first();
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->sub);
    }

    public function test_admin_can_update_category_and_slug(): void
    {
        $category = DirectoryCategory::create([
            'name' => 'Old Category Name',
            'slug' => 'old-slug-' . time(),
            'sub' => 0,
            'ordercat' => 0,
            'statu' => 1,
        ]);

        $newSlug = 'updated-slug-' . time();
        $response = $this->actingAsAdmin()->post(route('admin.directory_categories.update', $category->id), [
            'name' => 'New Category Name',
            'slug' => $newSlug,
            'icon' => 'fa-solid fa-star',
            'sub' => 0,
            'ordercat' => 10,
            'statu' => 0,
            'txt' => 'Updated text',
            'metakeywords' => 'updated, keywords',
        ]);

        $response->assertSessionHas('success');

        $category->refresh();
        $this->assertEquals('New Category Name', $category->name);
        $this->assertEquals($newSlug, $category->slug);
        $this->assertEquals('fa-solid fa-star', $category->icon);
        $this->assertEquals(10, $category->ordercat);
        $this->assertEquals(0, $category->statu);
    }

    public function test_admin_can_toggle_category_status_via_ajax(): void
    {
        $category = DirectoryCategory::create([
            'name' => 'Toggle Category',
            'slug' => 'toggle-cat-' . time(),
            'sub' => 0,
            'ordercat' => 0,
            'statu' => 1,
        ]);

        // Toggle to 0
        $response = $this->actingAsAdmin()->postJson(route('admin.directory_categories.toggle_status', $category->id));
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'statu' => 0]);

        $category->refresh();
        $this->assertEquals(0, $category->statu);

        // Toggle back to 1
        $response2 = $this->actingAsAdmin()->postJson(route('admin.directory_categories.toggle_status', $category->id));
        $response2->assertStatus(200);
        $response2->assertJson(['success' => true, 'statu' => 1]);

        $category->refresh();
        $this->assertEquals(1, $category->statu);
    }

    public function test_admin_can_reorder_categories_and_hierarchy_via_ajax(): void
    {
        $cat1 = DirectoryCategory::create(['name' => 'Order 1', 'slug' => 'ord-1-' . time(), 'sub' => 0, 'ordercat' => 0, 'statu' => 1]);
        $cat2 = DirectoryCategory::create(['name' => 'Order 2', 'slug' => 'ord-2-' . time(), 'sub' => 0, 'ordercat' => 1, 'statu' => 1]);
        $cat3 = DirectoryCategory::create(['name' => 'Order 3', 'slug' => 'ord-3-' . time(), 'sub' => 0, 'ordercat' => 2, 'statu' => 1]);

        // Reorder cat3 to first position, and nest cat2 under cat1
        $reorderData = [
            'items' => [
                ['id' => $cat3->id, 'parent_id' => 0, 'order' => 0],
                ['id' => $cat1->id, 'parent_id' => 0, 'order' => 1],
                ['id' => $cat2->id, 'parent_id' => $cat1->id, 'order' => 0],
            ],
        ];

        $response = $this->actingAsAdmin()->postJson(route('admin.directory_categories.reorder'), $reorderData);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $cat3->refresh();
        $this->assertEquals(0, $cat3->sub);
        $this->assertEquals(0, $cat3->ordercat);

        $cat1->refresh();
        $this->assertEquals(0, $cat1->sub);
        $this->assertEquals(1, $cat1->ordercat);

        $cat2->refresh();
        $this->assertEquals($cat1->id, $cat2->sub);
        $this->assertEquals(0, $cat2->ordercat);
    }

    public function test_admin_can_safely_delete_category_and_reassign_listings(): void
    {
        $sourceCat = DirectoryCategory::create(['name' => 'Source Cat', 'slug' => 'source-' . time(), 'sub' => 0, 'ordercat' => 0, 'statu' => 1]);
        $targetCat = DirectoryCategory::create(['name' => 'Target Cat', 'slug' => 'target-' . time(), 'sub' => 0, 'ordercat' => 1, 'statu' => 1]);
        $childCat = DirectoryCategory::create(['name' => 'Child of Source', 'slug' => 'child-src-' . time(), 'sub' => $sourceCat->id, 'ordercat' => 0, 'statu' => 1]);

        // Create a directory listing attached to sourceCat
        $listing = Directory::create([
            'uid' => $this->admin->id,
            'name' => 'Test Website',
            'slug' => 'test-site-' . time(),
            'url' => 'https://example.com',
            'cat' => $sourceCat->id,
            'statu' => 1,
            'date' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->actingAsAdmin()->delete(route('admin.directory_categories.delete', $sourceCat->id), [
            'move_to_id' => $targetCat->id,
        ]);

        $response->assertSessionHas('success');

        // Source category should be deleted
        $this->assertNull(DirectoryCategory::find($sourceCat->id));

        // Child category should be moved to root (sub = 0)
        $childCat->refresh();
        $this->assertEquals(0, $childCat->sub);

        // Listing should be reassigned to targetCat
        $listing->refresh();
        $this->assertEquals($targetCat->id, $listing->cat);
    }

    public function test_guest_cannot_access_or_modify_directory_categories(): void
    {
        $this->get(route('admin.directory_categories'))->assertRedirect();
        $this->post(route('admin.directory_categories.store'), ['name' => 'Guest Cat'])->assertRedirect();
        $this->post(route('admin.directory_categories.reorder'), ['items' => []])->assertRedirect();
    }
}
