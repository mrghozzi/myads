<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\Hooks;

class HooksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset hooks before each test (using reflection or re-instantiating if possible)
        // Since Hooks is static, we might need a reset method or handle it.
        // For simplicity in this test environment, we assume clean state or just test logic.
    }

    public function test_add_and_do_action()
    {
        $flag = false;
        
        Hooks::add_action('test_action', function() use (&$flag) {
            $flag = true;
        });

        Hooks::do_action('test_action');

        $this->assertTrue($flag);
    }

    public function test_action_priority()
    {
        $result = [];

        Hooks::add_action('priority_test', function() use (&$result) {
            $result[] = 'first';
        }, 10);

        Hooks::add_action('priority_test', function() use (&$result) {
            $result[] = 'second';
        }, 20);

        Hooks::add_action('priority_test', function() use (&$result) {
            $result[] = 'zero';
        }, 5);

        Hooks::do_action('priority_test');

        $this->assertEquals(['zero', 'first', 'second'], $result);
    }

    public function test_add_and_apply_filter()
    {
        Hooks::add_filter('test_filter', function($value) {
            return $value . ' modified';
        });

        $result = Hooks::apply_filters('test_filter', 'original');

        $this->assertEquals('original modified', $result);
    }

    public function test_filter_chaining()
    {
        Hooks::add_filter('chain_test', function($value) {
            return $value . ' A';
        });

        Hooks::add_filter('chain_test', function($value) {
            return $value . ' B';
        });

        $result = Hooks::apply_filters('chain_test', 'Start');

        $this->assertEquals('Start A B', $result);
    }
}
