<?php

namespace App\View\Components;

use App\Models\Option;
use Illuminate\View\Component;

class WidgetColumn extends Component
{
    public $side;
    public $widgets;

    /**
     * Create a new component instance.
     *
     * @param int $side The side of the widget column (1 for left, 2 for right).
     * @return void
     */
    public function __construct($side)
    {
        $this->side = $side;
        
        // Map human-readable side strings to numeric o_parent IDs
        $sideMapping = [
            'portal_left' => 1,
            'portal_right' => 2,
            'forum_left' => 3,
            'forum_right' => 4,
            'directory_left' => 5,
            'directory_right' => 6,
            'profile_left' => 7,
            'profile_right' => 8,
        ];

        // Resolve the mapped ID or use the side value directly if it's already an integer
        $resolvedSide = current(array_filter([$sideMapping[$this->side] ?? null, is_numeric($this->side) ? (int)$this->side : null]));

        if ($resolvedSide !== null) {
            // Fetch widgets for this side
            $this->widgets = Option::where('o_type', 'box_widget')
                ->where('o_parent', $resolvedSide)
                ->orderBy('o_order', 'desc')
                ->get();
        } else {
            $this->widgets = collect(); // Default to empty for unknown sides
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.widget-column');
    }
}
