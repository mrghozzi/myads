<?php

namespace Tests\Unit;

use MyAds\Plugins\ArabicFixer\ArabicStringRepair;
use Tests\TestCase;

class ArabicStringRepairTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once dirname(__DIR__, 2) . '/plugins/arabic-fixer/src/ArabicStringRepair.php';
    }

    public function test_it_ignores_clean_arabic_text(): void
    {
        $value = 'العربية سليمة';

        $this->assertFalse(ArabicStringRepair::isMojibake($value));
        $this->assertNull(ArabicStringRepair::fix($value));
    }

    public function test_it_repairs_single_layer_mojibake(): void
    {
        $value = 'Ø§Ù„Ø¹Ø±Ø¨ÙŠØ©';

        $this->assertTrue(ArabicStringRepair::isMojibake($value));
        $this->assertSame('العربية', ArabicStringRepair::fix($value));
    }

    public function test_it_repairs_multi_layer_mojibake(): void
    {
        $value = 'Ã˜Â§Ã™â€žÃ˜Â¹Ã˜Â±Ã˜Â¨Ã™Å Ã˜Â©';

        $this->assertTrue(ArabicStringRepair::isMojibake($value));
        $this->assertSame('العربية', ArabicStringRepair::fix($value));
    }

    public function test_it_does_not_change_false_positive_like_latin_text(): void
    {
        $value = 'Cafe Example Text';

        $this->assertFalse(ArabicStringRepair::isMojibake($value));
        $this->assertNull(ArabicStringRepair::fix($value));
    }
}
