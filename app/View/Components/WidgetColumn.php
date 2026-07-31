<?php

namespace App\View\Components;

use App\Models\Option;
use App\Models\Page;
use App\Services\AdminAccessService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Component;

class WidgetColumn extends Component
{
    public $side;
    public $widgets;
    public $resolvedSide;
    public $placeName;
    public $canManageWidgets = false;

    /**
     * Create a new component instance.
     *
     * @param int|string $side The side of the widget column (1 for left, 2 for right).
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
            'groups_left' => 9,
            'groups_right' => 10,
        ];

        // Resolve the mapped ID or use the side value directly if it's numeric
        $mappedId = $sideMapping[$this->side] ?? null;
        $numericId = is_numeric($this->side) ? (int)$this->side : null;
        $this->resolvedSide = $mappedId ?? $numericId;

        if ($this->resolvedSide !== null) {
            // Fetch widgets for this side
            $this->widgets = Option::where('o_type', 'box_widget')
                ->where('o_parent', $this->resolvedSide)
                ->orderBy('o_order', 'desc')
                ->get();
        } else {
            $this->widgets = collect(); // Default to empty for unknown sides
        }

        // Check if the current user is an admin who can access /admin/widgets
        $user = auth()->user();
        if ($user) {
            $this->canManageWidgets = app(AdminAccessService::class)->canAccess($user, 'admin.widgets');
        }

        $this->placeName = $this->resolvePlaceName($this->resolvedSide);
    }

    /**
     * Resolve human readable name for place ID.
     */
    private function resolvePlaceName(?int $placeId): string
    {
        if (!$placeId) {
            return '';
        }

        $places = [
            1 => __('messages.portal_left') ?: 'الجانب الأيسر للرئيسية',
            2 => __('messages.portal_right') ?: 'الجانب الأيمن للرئيسية',
            3 => __('messages.forum_left') ?: 'الجانب الأيسر للمنتدى',
            4 => __('messages.forum_right') ?: 'الجانب الأيمن للمنتدى',
            5 => __('messages.directory_left') ?: 'الجانب الأيسر للمجلة/الدليل',
            6 => __('messages.directory_right') ?: 'الجانب الأيمن للمجلة/الدليل',
            7 => __('messages.profile_left') ?: 'الجانب الأيسر للملف الشخصي',
            8 => __('messages.profile_right') ?: 'الجانب الأيمن للملف الشخصي',
            9 => __('messages.groups_left') ?: 'الجانب الأيسر للمجموعات',
            10 => __('messages.groups_right') ?: 'الجانب الأيمن للمجموعات',
        ];

        if (isset($places[$placeId])) {
            return (string) $places[$placeId];
        }

        // Dynamic custom pages places
        if ($placeId >= 100 && Schema::hasTable('pages')) {
            $pageId = (int) floor(($placeId - 100 + 1) / 2);
            $page = Page::find($pageId);
            if ($page) {
                $isLeft = ($placeId % 2 !== 0);
                $sideText = $isLeft ? (__('messages.page_left') ?: 'صفحة يسار') : (__('messages.page_right') ?: 'صفحة يمين');
                return $sideText . ': ' . $page->title;
            }
        }

        return __('messages.place') . ' #' . $placeId;
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

