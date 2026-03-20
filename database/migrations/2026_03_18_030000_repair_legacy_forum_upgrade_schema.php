<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureOptionsTable();
        $this->ensurePagesTable();
        $this->ensureForumCategoriesTable();
        $this->ensureForumTopicsTable();
        $this->ensureStatusTable();
        $this->ensureForumModeratorsTable();
        $this->ensureForumModeratorCategoriesTable();
        $this->ensureForumAttachmentsTable();
    }

    public function down(): void
    {
        // Repair-only migration; intentionally irreversible.
    }

    private function ensureOptionsTable(): void
    {
        if (!Schema::hasTable('options')) {
            Schema::create('options', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->longText('o_valuer')->nullable();
                $table->string('o_type')->nullable();
                $table->integer('o_parent')->default(0);
                $table->integer('o_order')->default(0);
                $table->string('o_mode')->nullable();
            });

            return;
        }

        $this->addMissingColumns('options', [
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'o_valuer' => fn (Blueprint $table) => $table->longText('o_valuer')->nullable(),
            'o_type' => fn (Blueprint $table) => $table->string('o_type')->nullable(),
            'o_parent' => fn (Blueprint $table) => $table->integer('o_parent')->default(0),
            'o_order' => fn (Blueprint $table) => $table->integer('o_order')->default(0),
            'o_mode' => fn (Blueprint $table) => $table->string('o_mode')->nullable(),
        ]);
    }

    private function ensurePagesTable(): void
    {
        if (Schema::hasTable('pages')) {
            return;
        }

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->boolean('widget_left')->default(true);
            $table->boolean('widget_right')->default(true);
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    private function ensureForumCategoriesTable(): void
    {
        if (!Schema::hasTable('f_cat')) {
            Schema::create('f_cat', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icons')->nullable();
                $table->text('txt')->nullable();
                $table->integer('ordercat')->default(0);
            });

            return;
        }

        $this->addMissingColumns('f_cat', [
            'name' => fn (Blueprint $table) => $table->string('name')->default(''),
            'icons' => fn (Blueprint $table) => $table->string('icons')->nullable(),
            'txt' => fn (Blueprint $table) => $table->text('txt')->nullable(),
            'ordercat' => fn (Blueprint $table) => $table->integer('ordercat')->default(0),
        ]);
    }

    private function ensureForumTopicsTable(): void
    {
        if (!Schema::hasTable('forum')) {
            Schema::create('forum', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid')->default(0);
                $table->string('name')->default('');
                $table->text('txt')->nullable();
                $table->unsignedBigInteger('cat')->default(0);
                $table->tinyInteger('statu')->default(1);
                $table->bigInteger('date')->default(0);
                $table->integer('reply')->default(0);
                $table->integer('vu')->default(0);
                $table->boolean('is_pinned')->default(false);
                $table->unsignedBigInteger('pinned_at')->nullable();
                $table->unsignedBigInteger('pinned_by')->nullable();
                $table->boolean('is_locked')->default(false);
                $table->unsignedBigInteger('locked_at')->nullable();
                $table->unsignedBigInteger('locked_by')->nullable();
            });

            return;
        }

        $this->addMissingColumns('forum', [
            'uid' => fn (Blueprint $table) => $table->unsignedBigInteger('uid')->default(0),
            'name' => fn (Blueprint $table) => $table->string('name')->default(''),
            'txt' => fn (Blueprint $table) => $table->text('txt')->nullable(),
            'cat' => fn (Blueprint $table) => $table->unsignedBigInteger('cat')->default(0),
            'statu' => fn (Blueprint $table) => $table->tinyInteger('statu')->default(1),
            'date' => fn (Blueprint $table) => $table->bigInteger('date')->default(0),
            'reply' => fn (Blueprint $table) => $table->integer('reply')->default(0),
            'vu' => fn (Blueprint $table) => $table->integer('vu')->default(0),
            'is_pinned' => fn (Blueprint $table) => $table->boolean('is_pinned')->default(false),
            'pinned_at' => fn (Blueprint $table) => $table->unsignedBigInteger('pinned_at')->nullable(),
            'pinned_by' => fn (Blueprint $table) => $table->unsignedBigInteger('pinned_by')->nullable(),
            'is_locked' => fn (Blueprint $table) => $table->boolean('is_locked')->default(false),
            'locked_at' => fn (Blueprint $table) => $table->unsignedBigInteger('locked_at')->nullable(),
            'locked_by' => fn (Blueprint $table) => $table->unsignedBigInteger('locked_by')->nullable(),
        ]);
    }

    private function ensureStatusTable(): void
    {
        if (!Schema::hasTable('status')) {
            Schema::create('status', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('uid')->default(0);
                $table->unsignedBigInteger('tp_id')->default(0);
                $table->integer('s_type')->default(100);
                $table->bigInteger('date')->default(0);
                $table->text('txt')->nullable();
                $table->tinyInteger('statu')->default(1);
            });

            return;
        }

        $this->addMissingColumns('status', [
            'uid' => fn (Blueprint $table) => $table->unsignedBigInteger('uid')->default(0),
            'tp_id' => fn (Blueprint $table) => $table->unsignedBigInteger('tp_id')->default(0),
            's_type' => fn (Blueprint $table) => $table->integer('s_type')->default(100),
            'date' => fn (Blueprint $table) => $table->bigInteger('date')->default(0),
            'txt' => fn (Blueprint $table) => $table->text('txt')->nullable(),
            'statu' => fn (Blueprint $table) => $table->tinyInteger('statu')->default(1),
        ]);
    }

    private function ensureForumModeratorsTable(): void
    {
        if (!Schema::hasTable('forum_moderators')) {
            Schema::create('forum_moderators', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->boolean('is_global')->default(false);
                $table->text('permissions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });

            return;
        }

        $this->addMissingColumns('forum_moderators', [
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable(),
            'is_global' => fn (Blueprint $table) => $table->boolean('is_global')->default(false),
            'permissions' => fn (Blueprint $table) => $table->text('permissions')->nullable(),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    private function ensureForumModeratorCategoriesTable(): void
    {
        if (!Schema::hasTable('forum_moderator_categories')) {
            Schema::create('forum_moderator_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('moderator_id');
                $table->unsignedBigInteger('category_id');
            });

            return;
        }

        $this->addMissingColumns('forum_moderator_categories', [
            'moderator_id' => fn (Blueprint $table) => $table->unsignedBigInteger('moderator_id')->default(0),
            'category_id' => fn (Blueprint $table) => $table->unsignedBigInteger('category_id')->default(0),
        ]);
    }

    private function ensureForumAttachmentsTable(): void
    {
        if (!Schema::hasTable('forum_attachments')) {
            Schema::create('forum_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('topic_id');
                $table->unsignedBigInteger('user_id');
                $table->string('file_path');
                $table->string('original_name');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });

            return;
        }

        $this->addMissingColumns('forum_attachments', [
            'topic_id' => fn (Blueprint $table) => $table->unsignedBigInteger('topic_id')->default(0),
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->default(0),
            'file_path' => fn (Blueprint $table) => $table->string('file_path')->nullable(),
            'original_name' => fn (Blueprint $table) => $table->string('original_name')->nullable(),
            'mime_type' => fn (Blueprint $table) => $table->string('mime_type')->nullable(),
            'file_size' => fn (Blueprint $table) => $table->unsignedBigInteger('file_size')->default(0),
            'sort_order' => fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ]);
    }

    private function addMissingColumns(string $table, array $definitions): void
    {
        $missingDefinitions = [];

        foreach ($definitions as $column => $definition) {
            if (!Schema::hasColumn($table, $column)) {
                $missingDefinitions[] = $definition;
            }
        }

        if ($missingDefinitions === []) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($missingDefinitions) {
            foreach ($missingDefinitions as $definition) {
                $definition($tableBlueprint);
            }
        });
    }
};
