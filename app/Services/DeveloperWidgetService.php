<?php

namespace App\Services;

use App\Models\DeveloperApp;

class DeveloperWidgetService
{
    /**
     * Generate Javascript code for the widget.
     */
    public function generateWidgetScript(DeveloperApp $app, string $type): string
    {
        $domain = request()->getSchemeAndHttpHost();
        $appId = $app->id;

        $heights = [
            'follow' => '44px',
            'profile' => '190px',
            'content' => '480px',
        ];

        $defaultHeight = $heights[$type] ?? '200px';

        return "
(function() {
    var container = document.getElementById('myads-widget-{$type}-{$appId}');
    if (!container) return;
    
    var iframe = document.createElement('iframe');
    iframe.src = '{$domain}/embed/developer/{$appId}/{$type}?ref=' + encodeURIComponent(window.location.href);
    iframe.style.border = 'none';
    iframe.style.overflow = 'hidden';
    iframe.style.width = '100%';
    iframe.style.height = '{$defaultHeight}';
    iframe.style.transition = 'height 0.2s ease';
    iframe.setAttribute('loading', 'lazy');
    iframe.setAttribute('title', 'MYADS Widget');
    
    window.addEventListener('message', function(e) {
        if (e.data && e.data.type === 'myads:widget:resize' && e.data.widgetId === '{$appId}:{$type}') {
            iframe.style.height = e.data.height + 'px';
        }
    });

    container.innerHTML = '';
    container.appendChild(iframe);
})();
";
    }
}
