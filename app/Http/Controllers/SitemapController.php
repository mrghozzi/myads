<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumTopic;
use App\Models\Directory;
use App\Models\Knowledgebase;
use App\Models\DirectoryCategory;
use App\Models\User;
use Illuminate\Support\Facades\File;

class SitemapController extends Controller
{
    /**
     * Generate the sitemap.xml file.
     */
    public function generate()
    {
        $urlSite = url('/');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Static Pages
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

        foreach ($staticPages as $page) {
            $xml .= "  <url>" . PHP_EOL;
            $xml .= "    <loc>{$urlSite}{$page}</loc>" . PHP_EOL;
            $xml .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
            $xml .= "    <priority>" . ($page === '/' ? '1.0' : '0.8') . "</priority>" . PHP_EOL;
            $xml .= "  </url>" . PHP_EOL;
        }

        // Forum Topics
        ForumTopic::chunk(100, function ($topics) use (&$xml, $urlSite) {
            foreach ($topics as $topic) {
                $xml .= "  <url>" . PHP_EOL;
                $xml .= "    <loc>{$urlSite}/t{$topic->id}</loc>" . PHP_EOL;
                $xml .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
                $xml .= "    <priority>0.5</priority>" . PHP_EOL;
                $xml .= "  </url>" . PHP_EOL;
            }
        });

        // Directory Listings
        Directory::chunk(100, function ($sites) use (&$xml, $urlSite) {
            foreach ($sites as $site) {
                $xml .= "  <url>" . PHP_EOL;
                $xml .= "    <loc>{$urlSite}/dr{$site->id}</loc>" . PHP_EOL;
                $xml .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
                $xml .= "    <priority>0.5</priority>" . PHP_EOL;
                $xml .= "  </url>" . PHP_EOL;
            }
        });

        // Knowledgebase Articles
        Knowledgebase::chunk(100, function ($articles) use (&$xml, $urlSite) {
            foreach ($articles as $article) {
                $xml .= "  <url>" . PHP_EOL;
                $xml .= "    <loc>{$urlSite}/kb/{$article->o_mode}:{$article->name}</loc>" . PHP_EOL;
                $xml .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
                $xml .= "    <priority>0.5</priority>" . PHP_EOL;
                $xml .= "  </url>" . PHP_EOL;
            }
        });

        // Directory Categories
        DirectoryCategory::chunk(100, function ($categories) use (&$xml, $urlSite) {
            foreach ($categories as $category) {
                $xml .= "  <url>" . PHP_EOL;
                $xml .= "    <loc>{$urlSite}/cat/{$category->id}</loc>" . PHP_EOL;
                $xml .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
                $xml .= "    <priority>0.5</priority>" . PHP_EOL;
                $xml .= "  </url>" . PHP_EOL;
            }
        });

        // User Profiles
        User::chunk(100, function ($users) use (&$xml, $urlSite) {
            foreach ($users as $user) {
                $xml .= "  <url>" . PHP_EOL;
                $xml .= "    <loc>{$urlSite}/u/{$user->username}</loc>" . PHP_EOL;
                $xml .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
                $xml .= "    <priority>0.5</priority>" . PHP_EOL;
                $xml .= "  </url>" . PHP_EOL;
            }
        });

        $xml .= '</urlset>';

        $path = public_path('sitemap.xml');
        File::put($path, $xml);

        if (request()->path() === 'sitemap.xml' || request()->expectsJson()) {
            return response($xml, 200)->header('Content-Type', 'text/xml');
        }

        return redirect()->back()->with('success', 'Sitemap generated successfully.');
    }
}
