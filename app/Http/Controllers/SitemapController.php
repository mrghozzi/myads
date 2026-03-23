<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumTopic;
use App\Models\Directory;
use App\Models\Knowledgebase;
use App\Models\DirectoryCategory;
use App\Models\User;
use App\Models\Page;
use Illuminate\Support\Facades\File;

class SitemapController extends Controller
{
    protected $chunkSize = 10000;

    /**
     * Generate the sitemap index (sitemap.xml).
     */
    public function index()
    {
        $urlSite = url('/');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        $types = [
            'static' => 1,
            'pages' => max(1, ceil(Page::count() / $this->chunkSize)),
            'topics' => max(1, ceil(ForumTopic::count() / $this->chunkSize)),
            'directories' => max(1, ceil(Directory::count() / $this->chunkSize)),
            'directory_categories' => max(1, ceil(DirectoryCategory::count() / $this->chunkSize)),
            'knowledgebases' => max(1, ceil(Knowledgebase::count() / $this->chunkSize)),
            'users' => max(1, ceil(User::count() / $this->chunkSize)),
        ];

        foreach ($types as $type => $pages) {
            for ($page = 1; $page <= $pages; $page++) {
                $xml .= "  <sitemap>" . PHP_EOL;
                $xml .= "    <loc>{$urlSite}/sitemap/{$type}/{$page}.xml</loc>" . PHP_EOL;
                $xml .= "  </sitemap>" . PHP_EOL;
            }
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Generate a specific sitemap section dynamically with streaming.
     */
    public function section($type, $page = 1)
    {
        $urlSite = url('/');
        $offset = ($page - 1) * $this->chunkSize;
        $limit = $this->chunkSize;

        return response()->stream(function () use ($type, $page, $offset, $limit, $urlSite) {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

            if ($type === 'static' && $page == 1) {
                $staticPages = [
                    '/',
                    '/portal',
                    '/directory',
                    '/add-site.html',
                    '/forum',
                    '/store',
                    '/login',
                    '/register',
                ];
                foreach ($staticPages as $p) {
                    echo "  <url>" . PHP_EOL;
                    echo "    <loc>{$urlSite}{$p}</loc>" . PHP_EOL;
                    echo "    <changefreq>weekly</changefreq>" . PHP_EOL;
                    echo "    <priority>" . ($p === '/' ? '1.0' : '0.8') . "</priority>" . PHP_EOL;
                    echo "  </url>" . PHP_EOL;
                }
            } elseif ($type === 'pages') {
                Page::offset($offset)->limit($limit)->get()->each(function ($p) use ($urlSite) {
                    echo "  <url>" . PHP_EOL;
                    echo "    <loc>{$urlSite}/page/{$p->slug}</loc>" . PHP_EOL;
                    echo "    <changefreq>weekly</changefreq>" . PHP_EOL;
                    echo "    <priority>0.8</priority>" . PHP_EOL;
                    echo "  </url>" . PHP_EOL;
                });
            } elseif ($type === 'topics') {
                ForumTopic::offset($offset)->limit($limit)->get()->each(function ($topic) use ($urlSite) {
                    echo "  <url>" . PHP_EOL;
                    echo "    <loc>{$urlSite}/t{$topic->id}</loc>" . PHP_EOL;
                    echo "    <changefreq>weekly</changefreq>" . PHP_EOL;
                    echo "    <priority>0.5</priority>" . PHP_EOL;
                    echo "  </url>" . PHP_EOL;
                });
            } elseif ($type === 'directories') {
                Directory::offset($offset)->limit($limit)->get()->each(function ($site) use ($urlSite) {
                    echo "  <url>" . PHP_EOL;
                    echo "    <loc>{$urlSite}/dr{$site->id}</loc>" . PHP_EOL;
                    echo "    <changefreq>weekly</changefreq>" . PHP_EOL;
                    echo "    <priority>0.5</priority>" . PHP_EOL;
                    echo "  </url>" . PHP_EOL;
                });
            } elseif ($type === 'directory_categories') {
                DirectoryCategory::offset($offset)->limit($limit)->get()->each(function ($category) use ($urlSite) {
                    echo "  <url>" . PHP_EOL;
                    echo "    <loc>{$urlSite}/cat/{$category->id}</loc>" . PHP_EOL;
                    echo "    <changefreq>weekly</changefreq>" . PHP_EOL;
                    echo "    <priority>0.5</priority>" . PHP_EOL;
                    echo "  </url>" . PHP_EOL;
                });
            } elseif ($type === 'knowledgebases') {
                Knowledgebase::offset($offset)->limit($limit)->get()->each(function ($article) use ($urlSite) {
                    echo "  <url>" . PHP_EOL;
                    echo "    <loc>{$urlSite}/kb/{$article->o_mode}:{$article->name}</loc>" . PHP_EOL;
                    echo "    <changefreq>weekly</changefreq>" . PHP_EOL;
                    echo "    <priority>0.5</priority>" . PHP_EOL;
                    echo "  </url>" . PHP_EOL;
                });
            } elseif ($type === 'users') {
                User::offset($offset)->limit($limit)->get()->each(function ($user) use ($urlSite) {
                    echo "  <url>" . PHP_EOL;
                    echo "    <loc>{$urlSite}/u/{$user->username}</loc>" . PHP_EOL;
                    echo "    <changefreq>weekly</changefreq>" . PHP_EOL;
                    echo "    <priority>0.5</priority>" . PHP_EOL;
                    echo "  </url>" . PHP_EOL;
                });
            }

            echo '</urlset>';
        }, 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * Trigger from admin panel. Cleans up static file if exists to ensure dynamic route works.
     */
    public function generate()
    {
        $path = public_path('sitemap.xml');
        if (File::exists($path)) {
            File::delete($path);
        }

        return redirect()->back()->with('success', 'Sitemap configuration updated. It is now served dynamically.');
    }
}
