<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('options')) {
            return;
        }

        $subcategories = [
            ['name' => 'CMS',               'o_mode' => 'CMS',               'o_order' => 0],
            ['name' => 'Blog',              'o_mode' => 'Blog',              'o_order' => 1],
            ['name' => 'Forum',             'o_mode' => 'Forum',             'o_order' => 2],
            ['name' => 'Ecommerce',         'o_mode' => 'Ecommerce',         'o_order' => 3],
            ['name' => 'Social_Network',    'o_mode' => 'Social_Network',    'o_order' => 4],
            ['name' => 'Marketplace',       'o_mode' => 'Marketplace',       'o_order' => 5],
            ['name' => 'LMS',               'o_mode' => 'LMS',               'o_order' => 6],
            ['name' => 'CRM',               'o_mode' => 'CRM',               'o_order' => 7],
            ['name' => 'Helpdesk',          'o_mode' => 'Helpdesk',          'o_order' => 8],
            ['name' => 'Booking',           'o_mode' => 'Booking',           'o_order' => 9],
            ['name' => 'Directory',         'o_mode' => 'Directory',         'o_order' => 10],
            ['name' => 'Portfolio',         'o_mode' => 'Portfolio',         'o_order' => 11],
            ['name' => 'Wiki',              'o_mode' => 'Wiki',              'o_order' => 12],
            ['name' => 'API',               'o_mode' => 'API',               'o_order' => 13],
            ['name' => 'Landing_Page',      'o_mode' => 'Landing_Page',      'o_order' => 14],
            ['name' => 'Newsletter',        'o_mode' => 'Newsletter',        'o_order' => 15],
            ['name' => 'Survey',            'o_mode' => 'Survey',            'o_order' => 16],
            ['name' => 'Project_Management', 'o_mode' => 'Project_Management', 'o_order' => 17],
            ['name' => 'File_Management',   'o_mode' => 'File_Management',   'o_order' => 18],
            ['name' => 'Others',            'o_mode' => 'Others',            'o_order' => 99],
        ];

        foreach ($subcategories as $subcat) {
            if (!DB::table('options')->where('o_type', 'scriptcat')->where('name', $subcat['name'])->exists()) {
                DB::table('options')->insert([
                    'name'     => $subcat['name'],
                    'o_valuer' => '0',
                    'o_type'   => 'scriptcat',
                    'o_parent' => 0,
                    'o_order'  => $subcat['o_order'],
                    'o_mode'   => $subcat['o_mode'],
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('options')) {
            return;
        }

        DB::table('options')->where('o_type', 'scriptcat')->delete();
    }
};
