<?php

namespace App\Console\Commands;

use App\Services\ThemeManager;
use Illuminate\Console\Command;

class ThemeCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:create 
                            {slug : The unique folder slug for the theme (e.g. dark-space)}
                            {--name= : The human-readable display name of the theme}
                            {--parent=default : The parent theme to inherit templates and assets from}
                            {--author= : The author name or handle}
                            {--description= : A short description of the theme}
                            {--ver=1.0.0 : The initial version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a new MYADS theme or child theme with parent inheritance, assets, and theme.json manifest';

    /**
     * Execute the console command.
     */
    public function handle(ThemeManager $themeManager): int
    {
        $slug = $this->argument('slug');
        $name = $this->option('name') ?: ucwords(str_replace(['-', '_'], ' ', $slug));
        $parent = $this->option('parent') ?: 'default';
        $author = $this->option('author') ?: 'MYADS Developer';
        $description = $this->option('description') ?: "Custom theme inheriting from {$parent}.";
        $version = $this->option('ver') ?: '1.0.0';

        $this->components->info("Scaffolding new MYADS theme [{$slug}]...");

        $result = $themeManager->createStarterTheme([
            'name' => $name,
            'slug' => $slug,
            'parent' => $parent,
            'author' => $author,
            'description' => $description,
            'version' => $version,
        ]);

        if (empty($result['success'])) {
            $this->components->error($result['message'] ?? 'Failed to create theme.');
            return self::FAILURE;
        }

        $this->table(
            ['Property', 'Value'],
            [
                ['Name', $name],
                ['Slug', $slug],
                ['Parent Theme', $parent],
                ['Author', $author],
                ['Version', $version],
                ['Location', $result['path']],
            ]
        );

        $this->newLine();
        $this->components->info("Theme [{$slug}] created successfully with parent inheritance from [{$parent}]!");
        $this->line("You can now customize templates in <comment>themes/{$slug}/views</comment> or CSS in <comment>themes/{$slug}/assets/css/theme.css</comment>.");
        $this->line("To preview it, visit: <info>" . url('/?preview_theme=' . $slug) . "</info>");

        return self::SUCCESS;
    }
}

