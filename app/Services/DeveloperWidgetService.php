<?php

namespace App\Services;

use App\Models\DeveloperApp;
use App\Models\User;

class DeveloperWidgetService
{
    /**
     * Generate Javascript code for the widget.
     */
    public function generateWidgetScript(DeveloperApp $app, string $type): string
    {
        $domain = request()->getSchemeAndHttpHost();
        $appId = $app->id;
        $ownerId = $app->user_id;

        $js = "
(function() {
    var container = document.getElementById('myads-widget-{$type}-{$appId}');
    if (!container) return;
    
    var iframe = document.createElement('iframe');
    iframe.src = '{$domain}/embed/developer/{$appId}/{$type}?ref=' + encodeURIComponent(window.location.href);
    iframe.style.border = 'none';
    iframe.style.overflow = 'hidden';
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    
    container.appendChild(iframe);
})();
";

        return $js;
    }
}
