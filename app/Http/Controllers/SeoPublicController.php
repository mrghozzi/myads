<?php

namespace App\Http\Controllers;

use App\Services\RobotsTxtService;

class SeoPublicController extends Controller
{
    public function __construct(
        private readonly RobotsTxtService $robotsTxt,
    ) {
    }

    public function robots()
    {
        return response($this->robotsTxt->render(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
