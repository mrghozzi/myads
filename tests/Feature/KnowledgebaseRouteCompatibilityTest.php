<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgebaseRouteCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_knowledgebase_index_route_accepts_hyphenated_store_names(): void
    {
        $this->seedThemeSetting();
        $owner = User::factory()->create();

        Option::create([
            'name' => 'Web-Designing',
            'o_valuer' => 'Store description',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 0,
            'o_mode' => 'upload/product.png',
        ]);

        Option::create([
            'name' => 'Getting-Started',
            'o_valuer' => 'Knowledgebase article body',
            'o_type' => 'knowledgebase',
            'o_parent' => $owner->id,
            'o_order' => 0,
            'o_mode' => 'Web-Designing',
        ]);

        $this->get('/kb/Web-Designing')
            ->assertOk()
            ->assertSee('Getting-Started');
    }

    public function test_knowledgebase_show_route_accepts_hyphenated_store_names(): void
    {
        $this->seedThemeSetting();
        $owner = User::factory()->create();

        Option::create([
            'name' => 'Web-Designing',
            'o_valuer' => 'Store description',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 0,
            'o_mode' => 'upload/product.png',
        ]);

        Option::create([
            'name' => 'Getting-Started',
            'o_valuer' => 'Knowledgebase article body',
            'o_type' => 'knowledgebase',
            'o_parent' => $owner->id,
            'o_order' => 0,
            'o_mode' => 'Web-Designing',
        ]);

        $this->get('/kb/Web-Designing:Getting-Started')
            ->assertOk()
            ->assertSee('Knowledgebase article body');
    }

    private function seedThemeSetting(): void
    {
        Setting::create([
            'titer' => 'MyAds',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);
    }
}
