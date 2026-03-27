<?php

namespace App\Services\Contracts;

interface UrlSafetyInspectorInterface
{
    public function firstViolation(string $url, array $blockedDomains = [], array $blockedPatterns = []): ?string;
}
