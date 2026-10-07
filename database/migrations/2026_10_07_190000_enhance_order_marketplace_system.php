<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_requests')) {
            Schema::table('order_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('order_requests', 'attachment_path')) {
                    $table->string('attachment_path', 255)->nullable()->after('description');
                }
                if (!Schema::hasColumn('order_requests', 'attachment_name')) {
                    $table->string('attachment_name', 255)->nullable()->after('attachment_path');
                }
                if (!Schema::hasColumn('order_requests', 'admin_notes')) {
                    $table->text('admin_notes')->nullable()->after('workflow_status');
                }
            });
        }

        if (Schema::hasTable('order_contracts')) {
            Schema::table('order_contracts', function (Blueprint $table) {
                if (!Schema::hasColumn('order_contracts', 'delivery_attachment_path')) {
                    $table->string('delivery_attachment_path', 255)->nullable()->after('delivery_note');
                }
                if (!Schema::hasColumn('order_contracts', 'delivery_attachment_name')) {
                    $table->string('delivery_attachment_name', 255)->nullable()->after('delivery_attachment_path');
                }
                if (!Schema::hasColumn('order_contracts', 'revision_note')) {
                    $table->text('revision_note')->nullable()->after('completion_note');
                }
                if (!Schema::hasColumn('order_contracts', 'revision_count')) {
                    $table->unsignedInteger('revision_count')->default(0)->after('revision_note');
                }
                if (!Schema::hasColumn('order_contracts', 'revision_requested_at')) {
                    $table->timestamp('revision_requested_at')->nullable()->after('delivered_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_requests')) {
            Schema::table('order_requests', function (Blueprint $table) {
                $columns = ['attachment_path', 'attachment_name', 'admin_notes'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('order_requests', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('order_contracts')) {
            Schema::table('order_contracts', function (Blueprint $table) {
                $columns = [
                    'delivery_attachment_path',
                    'delivery_attachment_name',
                    'revision_note',
                    'revision_count',
                    'revision_requested_at',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('order_contracts', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
