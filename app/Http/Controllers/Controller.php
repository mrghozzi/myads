<?php

namespace App\Http\Controllers;

use App\Services\SeoManager;

abstract class Controller
{
    protected function seo(array $context = []): void
    {
        app(SeoManager::class)->setContext($context);
    }

    protected function noindex(array $context = []): void
    {
        $this->seo(array_merge($context, [
            'indexable' => false,
            'robots' => 'noindex,nofollow',
        ]));
    }
}
