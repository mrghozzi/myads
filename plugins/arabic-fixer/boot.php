<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Helpers\Hooks;

/**
 * Detect if a string contains double-encoded UTF-8 (mojibake).
 * Arabic UTF-8 bytes (0xD8xx, 0xD9xx) interpreted as latin1/cp1252
 * produce characters like Ø (U+00D8), Ù (U+00D9) followed by
 * continuation byte characters (U+0080-U+00BF).
 */
function arabic_fixer_is_mojibake(string $val): bool
{
    // Pattern: Ø or Ù followed by a character in the 0x80-0xBF range
    // (encoded as UTF-8: C2 80 - C2 BF or C3 80 - C3 BF)
    // This is the signature of Arabic UTF-8 bytes double-encoded.
    // \xC3[\x98\x99] = Ø or Ù in UTF-8
    // followed by \xC2[\x80-\xBF] or \xC3[\x80-\xBF] = the continuation byte chars
    return (bool) preg_match('/[\x{00D8}\x{00D9}][\x{0080}-\x{00BF}]/u', $val);
}

/**
 * Fix a single mojibake string by reversing the double-encoding.
 * Returns the fixed string or null if the fix failed/didn't apply.
 */
function arabic_fixer_fix_string(string $val): ?string
{
    if (!arabic_fixer_is_mojibake($val)) {
        return null;
    }

    $current = $val;

    // Try up to 3 rounds to handle multi-level encoding corruption
    for ($i = 0; $i < 3; $i++) {
        // Convert from UTF-8 to Windows-1252 to recover the original bytes
        // We use Windows-1252 (not ISO-8859-1) because bytes 0x80-0x9F
        // are valid in cp1252 but undefined in ISO-8859-1
        $decoded = mb_convert_encoding($current, 'Windows-1252', 'UTF-8');

        if ($decoded === false || $decoded === $current) {
            break;
        }

        // Check if the result is valid UTF-8
        if (!mb_check_encoding($decoded, 'UTF-8')) {
            break;
        }

        $current = $decoded;

        // If no more mojibake patterns remain, we're done
        if (!arabic_fixer_is_mojibake($current)) {
            break;
        }
    }

    // Verify the result actually contains Arabic characters (U+0600-U+06FF)
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $current)) {
        return $current;
    }

    return null;
}

/**
 * Scan all tables and return an array of items that would be fixed.
 * Each item: ['table' => ..., 'id' => ..., 'column' => ..., 'before' => ..., 'after' => ...]
 */
function arabic_fixer_scan_all(): array
{
    $tables = [
        'setting', 'options', 'ads', 'news', 'forum', 'f_coment',
        'cat_dir', 'f_cat', 'directory', 'users', 'messages', 'report',
        'banner', 'link', 'notif', 'visits'
    ];

    $excludeColumns = [
        'password', 'email', 'remember_token', 'avatar', 'cover',
        'url', 'slug', 'token', 'ip', 'ip_address', 'file', 'image', 'icon',
        'created_at', 'updated_at', 'email_verified_at', 'date',
        'o_type'
    ];

    $results = [];

    foreach ($tables as $table) {
        if (!Schema::hasTable($table)) continue;

        $allColumns = Schema::getColumnListing($table);
        $rows = DB::table($table)->get();

        // Determine primary key for identification
        $pkColumn = null;
        if (in_array('id', $allColumns)) {
            $pkColumn = 'id';
        } elseif ($table === 'options' && in_array('name', $allColumns)) {
            $pkColumn = 'name';
        } elseif ($table === 'setting') {
            $pkColumn = '_setting_'; // special marker
        }

        foreach ($rows as $row) {
            foreach ($allColumns as $col) {
                if (in_array(strtolower($col), $excludeColumns)) continue;

                $val = $row->$col;
                if (!is_string($val) || empty($val)) continue;

                $fixed = arabic_fixer_fix_string($val);
                if ($fixed !== null && $fixed !== $val) {
                    $rowId = 'N/A';
                    if ($pkColumn === 'id' && isset($row->id)) {
                        $rowId = $row->id;
                    } elseif ($pkColumn === 'name' && isset($row->name)) {
                        $rowId = $row->name;
                    } elseif ($pkColumn === '_setting_') {
                        $rowId = 'setting';
                    }

                    $results[] = [
                        'table'  => $table,
                        'column' => $col,
                        'id'     => $rowId,
                        'pk'     => $pkColumn,
                        'before' => mb_substr($val, 0, 120),
                        'after'  => mb_substr($fixed, 0, 120),
                    ];
                }
            }
        }
    }

    return $results;
}

Route::middleware(['web', 'auth', 'admin'])->group(function () {

    // Main page
    Route::get('/admin/arabic-fixer', function () {
        return view('arabic_fixer::index');
    })->name('admin.arabic-fixer.index');

    // Preview — scan and return results without making changes
    Route::post('/admin/arabic-fixer/preview', function (Request $request) {
        try {
            $items = arabic_fixer_scan_all();
            return back()->with('preview', $items);
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ أثناء المعاينة: ' . $e->getMessage());
        }
    })->name('admin.arabic-fixer.preview');

    // Run — actually apply fixes
    Route::post('/admin/arabic-fixer/run', function (Request $request) {
        $tables = [
            'setting', 'options', 'ads', 'news', 'forum', 'f_coment',
            'cat_dir', 'f_cat', 'directory', 'users', 'messages', 'report',
            'banner', 'link', 'notif', 'visits'
        ];

        $excludeColumns = [
            'password', 'email', 'remember_token', 'avatar', 'cover',
            'url', 'slug', 'token', 'ip', 'ip_address', 'file', 'image', 'icon',
            'created_at', 'updated_at', 'email_verified_at', 'date',
            'o_type'
        ];

        $errors = [];

        // 1. Convert database & tables to utf8mb4
        try {
            $dbName = DB::getDatabaseName();
            DB::statement("ALTER DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            $errors[] = "DB Alter: " . $e->getMessage();
        }

        $fixedCount = 0;
        $fixedFields = 0;

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) continue;

            // Convert the whole table's charset first
            try {
                DB::statement("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Exception $e) {
                $errors[] = "Table Alter ($table): " . $e->getMessage();
            }

            $allColumns = Schema::getColumnListing($table);
            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $updates = [];

                foreach ($allColumns as $col) {
                    if (in_array(strtolower($col), $excludeColumns)) continue;

                    $val = $row->$col;
                    if (!is_string($val) || empty($val)) continue;

                    $fixed = arabic_fixer_fix_string($val);
                    if ($fixed !== null && $fixed !== $val) {
                        $updates[$col] = $fixed;
                        $fixedFields++;
                    }
                }

                if (!empty($updates)) {
                    if (isset($row->id)) {
                        try {
                            DB::table($table)->where('id', $row->id)->update($updates);
                            $fixedCount++;
                        } catch (\Exception $ex) {
                            $errors[] = "Update ($table.id={$row->id}): " . $ex->getMessage();
                        }
                    } elseif (isset($row->name) && $table === 'options') {
                        try {
                            DB::table($table)->where('name', $row->name)->update($updates);
                            $fixedCount++;
                        } catch (\Exception $ex) {
                            $errors[] = "Update (options.name={$row->name}): " . $ex->getMessage();
                        }
                    } elseif ($table === 'setting') {
                        try {
                            DB::table($table)->update($updates);
                            $fixedCount++;
                        } catch (\Exception $ex) {
                            $errors[] = "Update ($table): " . $ex->getMessage();
                        }
                    }
                }
            }
        }

        $msg = "تم إصلاح $fixedFields حقل عبر $fixedCount سجل في قاعدة البيانات بنجاح.";
        if (count($errors) > 0) {
            $msg .= "\nأخطاء: " . implode(" | ", array_slice($errors, 0, 5));
        }

        return back()->with('success', $msg);
    })->name('admin.arabic-fixer.run');
});

// Register views
View::addNamespace('arabic_fixer', __DIR__ . '/views');

// Add to Admin Menu
Hooks::add_action('admin_sidebar_menu', function() {
    $url = route('admin.arabic-fixer.index');
    echo '<li class="nxl-item">
            <a href="' . $url . '" class="nxl-link">
                <span class="nxl-micon"><i class="feather-tool"></i></span>
                <span class="nxl-mtext">إصلاح العربية (Mojibake)</span>
            </a>
          </li>';
});
