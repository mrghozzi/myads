<?php

namespace Tests\Unit;

use App\Models\Option;
use PHPUnit\Framework\TestCase;

class OptionWidgetFillableTest extends TestCase
{
    public function test_option_model_is_fillable_for_widget_fields(): void
    {
        $fillable = (new Option())->getFillable();
        $expected = ['name', 'o_valuer', 'o_type', 'o_parent', 'o_order', 'o_mode'];
        $this->assertEqualsCanonicalizing($expected, $fillable);
    }
}
