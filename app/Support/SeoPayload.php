<?php

namespace App\Support;

class SeoPayload
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $keywords,
        public readonly ?string $canonical_url,
        public readonly string $robots,
        public readonly array $og,
        public readonly array $twitter,
        public readonly array $schema_blocks,
        public readonly array $head_snippets,
        public readonly bool $indexable,
        public readonly ?string $lastmod,
    ) {
    }
}
