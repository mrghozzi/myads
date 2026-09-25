<?php

namespace Tests\Unit;

use App\Support\SeoHeadSanitizer;
use PHPUnit\Framework\TestCase;

class SeoHeadSanitizerTest extends TestCase
{
    private SeoHeadSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new SeoHeadSanitizer();
    }

    public function test_allows_google_adsense_script_tag(): void
    {
        $code = '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9228974727858878" crossorigin="anonymous"></script>';
        $result = $this->sanitizer->sanitize($code);

        $this->assertSame($code, $result);
    }

    public function test_allows_script_with_data_attributes(): void
    {
        $code = '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js" data-ad-client="ca-pub-9228974727858878" crossorigin="anonymous"></script>';
        $result = $this->sanitizer->sanitize($code);

        $this->assertSame($code, $result);
    }

    public function test_allows_inline_script(): void
    {
        $code = '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
        $result = $this->sanitizer->sanitize($code);

        $this->assertSame($code, $result);
    }

    public function test_allows_html_comments_with_scripts(): void
    {
        $input = "<!-- Google AdSense -->\n<script async src=\"https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9228974727858878\" crossorigin=\"anonymous\"></script>";
        $result = $this->sanitizer->sanitize($input);

        $this->assertStringContainsString('<!-- Google AdSense -->', $result);
        $this->assertStringContainsString('<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9228974727858878" crossorigin="anonymous"></script>', $result);
    }

    public function test_allows_meta_and_link_tags(): void
    {
        $input = '<meta name="google-site-verification" content="test-123">' . PHP_EOL . '<link rel="canonical" href="https://example.com">';
        $result = $this->sanitizer->sanitize($input);

        $this->assertStringContainsString('<meta name="google-site-verification" content="test-123">', $result);
        $this->assertStringContainsString('<link rel="canonical" href="https://example.com">', $result);
    }

    public function test_validates_json_ld_scripts(): void
    {
        $valid = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"Organization","name":"MyAds"}</script>';
        $this->assertSame($valid, $this->sanitizer->sanitize($valid));

        $invalid = '<script type="application/ld+json">{invalid json</script>';
        $this->assertSame('', $this->sanitizer->sanitize($invalid));
    }

    public function test_blocks_malicious_javascript_protocols(): void
    {
        $maliciousScript = '<script src="javascript:alert(1)"></script>';
        $this->assertSame('', $this->sanitizer->sanitize($maliciousScript));

        $maliciousLink = '<link rel="stylesheet" href="javascript:alert(1)">';
        $this->assertSame('', $this->sanitizer->sanitize($maliciousLink));
    }

    public function test_strips_disallowed_tags(): void
    {
        $input = '<iframe src="https://example.com"></iframe><div class="test">content</div><input type="text">';
        $this->assertSame('', $this->sanitizer->sanitize($input));
    }

    public function test_allows_style_and_noscript_tags(): void
    {
        $style = '<style>.ad-banner { display: block; }</style>';
        $this->assertStringContainsString('.ad-banner { display: block; }', $this->sanitizer->sanitize($style));

        $noscript = '<noscript><img src="https://example.com/pixel.gif" alt=""></noscript>';
        $this->assertStringContainsString('<noscript><img src="https://example.com/pixel.gif" alt=""></noscript>', $this->sanitizer->sanitize($noscript));
    }
}
