<?php

namespace Tests\Feature;

use Tests\TestCase;

class AllLanguagesCompatibilityTest extends TestCase
{
    private array $locales = [
        'ar', 'de', 'en', 'es', 'fa', 'fr', 'it', 'ja', 'pt', 'ru', 'sr', 'tr', 'zh_CN', 'zh_TW'
    ];

    private array $requiredFiles = [
        'about.php',
        'auth.php',
        'help.php',
        'messages.php',
        'pagination.php',
        'passwords.php',
        'support.php',
        'validation.php',
    ];

    public function test_all_locales_have_complete_file_set(): void
    {
        foreach ($this->locales as $locale) {
            $dir = base_path('lang/' . $locale);
            $this->assertDirectoryExists($dir, "Language directory [lang/{$locale}] does not exist.");

            foreach ($this->requiredFiles as $file) {
                $filePath = $dir . '/' . $file;
                $this->assertFileExists($filePath, "Required language file [{$file}] is missing in locale [{$locale}].");
            }
        }
    }

    public function test_about_php_has_full_key_parity(): void
    {
        $enData = include base_path('lang/en/about.php');
        $this->assertIsArray($enData);

        foreach ($this->locales as $locale) {
            $data = include base_path("lang/{$locale}/about.php");
            $this->assertIsArray($data, "about.php in [{$locale}] is not an array.");
            $this->assertSameSize($enData, $data, "about.php key count mismatch in [{$locale}].");

            foreach (array_keys($enData) as $key) {
                $this->assertArrayHasKey($key, $data, "Missing key [{$key}] in [lang/{$locale}/about.php].");
            }
        }
    }

    public function test_help_php_has_full_key_parity(): void
    {
        $enData = include base_path('lang/en/help.php');
        $this->assertIsArray($enData);

        foreach ($this->locales as $locale) {
            $data = include base_path("lang/{$locale}/help.php");
            $this->assertIsArray($data, "help.php in [{$locale}] is not an array.");
            $this->assertSameSize($enData, $data, "help.php key count mismatch in [{$locale}].");

            foreach (array_keys($enData) as $key) {
                $this->assertArrayHasKey($key, $data, "Missing key [{$key}] in [lang/{$locale}/help.php].");
            }
        }
    }

    public function test_support_php_has_full_key_parity(): void
    {
        $enData = include base_path('lang/en/support.php');
        $this->assertIsArray($enData);

        foreach ($this->locales as $locale) {
            $filePath = base_path("lang/{$locale}/support.php");
            if (!file_exists($filePath)) {
                $this->fail("support.php missing in [{$locale}].");
            }
            $data = include $filePath;
            $this->assertIsArray($data, "support.php in [{$locale}] is not an array.");
            $this->assertSameSize($enData, $data, "support.php key count mismatch in [{$locale}].");

            foreach (array_keys($enData) as $key) {
                $this->assertArrayHasKey($key, $data, "Missing key [{$key}] in [lang/{$locale}/support.php].");
                $this->assertNotEmpty((string) $data[$key], "Empty translation for [{$key}] in [lang/{$locale}/support.php].");
            }
        }
    }

    public function test_messages_php_has_full_key_parity(): void
    {
        $enData = include base_path('lang/en/messages.php');
        $this->assertIsArray($enData);

        foreach ($this->locales as $locale) {
            $filePath = base_path("lang/{$locale}/messages.php");
            $data = include $filePath;
            $this->assertIsArray($data, "messages.php in [{$locale}] is not an array.");
            $this->assertSameSize($enData, $data, "messages.php key count mismatch in [{$locale}]. Expected " . count($enData) . " keys, got " . count($data));

            foreach (array_keys($enData) as $key) {
                $this->assertArrayHasKey($key, $data, "Missing key [{$key}] in [lang/{$locale}/messages.php].");
                $this->assertNotSame('', trim((string) $data[$key]), "Empty translation for key [{$key}] in [lang/{$locale}/messages.php].");
            }
        }
    }

    public function test_standard_laravel_files_parity(): void
    {
        foreach (['auth.php', 'pagination.php', 'passwords.php', 'validation.php'] as $file) {
            $enData = include base_path("lang/en/{$file}");
            $this->assertIsArray($enData);

            foreach ($this->locales as $locale) {
                $filePath = base_path("lang/{$locale}/{$file}");
                $this->assertFileExists($filePath, "File [{$file}] missing in [{$locale}].");
                $data = include $filePath;
                $this->assertIsArray($data, "File [{$file}] in [{$locale}] is not an array.");
            }
        }
    }
}
