<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWidgetsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_delete_and_reorder_widgets(): void
    {
        $admin = User::factory()->create(['ucheck' => 1]);
        $this->actingAs($admin);

        $createResponse = $this->post(route('admin.widgets.store'), [
            'name' => 'Widget One',
            'o_parent' => 1,
            'o_order' => 0,
            'o_valuer' => '<div>Test</div>',
            'o_mode' => 'widget_html',
        ]);

        $createResponse->assertStatus(302);
        $widget = Option::where('o_type', 'box_widget')->first();
        $this->assertNotNull($widget);

        $updateResponse = $this->post(route('admin.widgets.update', $widget->id), [
            'name' => 'Widget One Updated',
            'o_parent' => 2,
            'o_order' => 3,
            'o_valuer' => '<div>Updated</div>',
        ]);

        $updateResponse->assertStatus(302);
        $this->assertDatabaseHas('options', [
            'id' => $widget->id,
            'name' => 'Widget One Updated',
            'o_parent' => 2,
            'o_order' => 3,
        ]);

        $widgets = collect(range(1, 3))->map(function ($index) {
            return Option::create([
                'name' => 'Widget ' . $index,
                'o_valuer' => '',
                'o_type' => 'box_widget',
                'o_parent' => 1,
                'o_order' => $index,
                'o_mode' => 'widget_members',
            ]);
        });

        $orderResponse = $this->post(route('admin.widgets.reorder'), [
            'order' => $widgets->pluck('id')->reverse()->values()->all(),
        ]);

        $orderResponse->assertStatus(200);
        $this->assertDatabaseHas('options', [
            'id' => $widgets->last()->id,
            'o_order' => 0,
        ]);

        $deleteResponse = $this->delete(route('admin.widgets.delete', $widget->id));
        $deleteResponse->assertStatus(302);
        $this->assertDatabaseMissing('options', ['id' => $widget->id]);
    }
}
