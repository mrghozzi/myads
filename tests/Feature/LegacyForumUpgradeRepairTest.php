<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Setting;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyForumUpgradeRepairTest extends TestCase
{
    use RefreshDatabase;

    public function test_forum_category_description_can_be_left_empty_when_saved_from_admin(): void
    {
        Setting::create([
            'titer' => 'MyAds',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);

        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'admin-user',
            'email' => 'admin@example.com',
        ]);

        $this->actingAs($admin)->post('/admin/forum/categories', [
            'name' => 'Optional Description',
            'icons' => 'fa-comments',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => '',
        ])->assertRedirect();

        $category = ForumCategory::query()->firstOrFail();

        $this->assertSame('', $category->txt);

        $this->actingAs($admin)->post('/admin/forum/categories/' . $category->id, [
            'name' => 'Optional Description Updated',
            'icons' => 'fa-comments',
            'ordercat' => 2,
            'visibility' => 1,
            'txt' => '',
        ])->assertRedirect();

        $category->refresh();

        $this->assertSame('', $category->txt);
        $this->assertSame('Optional Description Updated', $category->name);
        $this->assertSame(2, (int) $category->ordercat);
        $this->assertSame(1, (int) $category->visibility);
    }

    public function test_legacy_forum_schema_is_repaired_and_forum_pages_render(): void
    {
        $this->replaceForumSchemaWithLegacyStructures();

        $migration = require base_path('database/migrations/2026_03_18_030000_repair_legacy_forum_upgrade_schema.php');
        $migration->up();

        foreach (['name', 'icons', 'txt', 'ordercat'] as $column) {
            $this->assertTrue(Schema::hasColumn('f_cat', $column), "Expected f_cat.{$column} to exist after repair.");
        }

        foreach (['uid', 'name', 'txt', 'cat', 'statu', 'date', 'reply', 'vu', 'is_pinned', 'pinned_at', 'pinned_by', 'is_locked', 'locked_at', 'locked_by'] as $column) {
            $this->assertTrue(Schema::hasColumn('forum', $column), "Expected forum.{$column} to exist after repair.");
        }

        foreach (['uid', 'tp_id', 's_type', 'date', 'txt', 'statu'] as $column) {
            $this->assertTrue(Schema::hasColumn('status', $column), "Expected status.{$column} to exist after repair.");
        }

        $this->assertTrue(Schema::hasTable('forum_moderators'));
        $this->assertTrue(Schema::hasTable('forum_moderator_categories'));
        $this->assertTrue(Schema::hasTable('forum_attachments'));
        $this->assertTrue(Schema::hasTable('options'));
        $this->assertTrue(Schema::hasTable('pages'));

        Setting::create([
            'titer' => 'MyAds',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);

        $admin = User::factory()->create();

        $category = ForumCategory::create([
            'name' => 'Legacy Category',
            'icons' => 'fa-comments',
            'txt' => 'Recovered from legacy upgrade',
            'ordercat' => 10,
        ]);

        $topic = ForumTopic::create([
            'uid' => $admin->id,
            'name' => 'Recovered Topic',
            'txt' => 'Recovered forum body',
            'cat' => $category->id,
            'statu' => 1,
            'date' => time(),
            'reply' => 0,
            'vu' => 0,
        ]);

        Status::create([
            'uid' => $admin->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $this->actingAs($admin)->get('/forum')->assertOk();
        $this->actingAs($admin)->get('/admin/forum/categories')->assertOk();
        $this->actingAs($admin)->get('/admin/forum/moderators')->assertOk();
    }

    private function replaceForumSchemaWithLegacyStructures(): void
    {
        Schema::dropIfExists('forum_moderator_categories');
        Schema::dropIfExists('forum_moderators');
        Schema::dropIfExists('forum_attachments');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('options');
        Schema::dropIfExists('status');
        Schema::dropIfExists('forum');
        Schema::dropIfExists('f_cat');

        Schema::create('f_cat', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('forum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->default(0);
            $table->string('name')->default('');
            $table->unsignedBigInteger('cat')->default(0);
            $table->tinyInteger('statu')->default(1);
        });

        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->default(0);
            $table->unsignedBigInteger('tp_id')->default(0);
            $table->integer('s_type')->default(100);
            $table->bigInteger('date')->default(0);
        });
    }
}
