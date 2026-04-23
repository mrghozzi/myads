<?php

namespace App\Services;

use App\Models\Option;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Short;
use App\Support\StoreCategoryCatalog;
use Illuminate\Support\Facades\Cache;

class ExtensionMarketplaceCatalogService
{
    private const CACHE_TTL_SECONDS = 600;

    public function __construct(
        private readonly ExtensionManifestReader $manifestReader
    ) {
    }

    /**
     * @return array{type: string, items: array<int, array<string, string>>}
     */
    public function build(string $type): array
    {
        $normalizedType = $this->normalizeType($type);

        return Cache::remember(
            $this->cacheKey($normalizedType),
            self::CACHE_TTL_SECONDS,
            fn (): array => $this->buildFresh($normalizedType)
        );
    }

    /**
     * @return array{type: string, items: array<int, array<string, string>>}
     */
    private function buildFresh(string $type): array
    {
        try {
            $categoryNames = $this->categoryNamesForType($type);
            if ($categoryNames === []) {
                return ['type' => $type, 'items' => []];
            }

            $typeRows = Option::query()
                ->where('o_type', 'store_type')
                ->whereIn('name', $categoryNames)
                ->orderByDesc('id')
                ->get(['id', 'name', 'o_parent'])
                ->groupBy('o_parent')
                ->map(static fn ($rows) => $rows->first());

            if ($typeRows->isEmpty()) {
                return ['type' => $type, 'items' => []];
            }

            $productIds = $typeRows->keys()->map(static fn ($id): int => (int) $id)->values();
            $products = Product::query()
                ->visible()
                ->whereIn('id', $productIds)
                ->orderByDesc('id')
                ->get()
                ->keyBy('id');

            if ($products->isEmpty()) {
                return ['type' => $type, 'items' => []];
            }

            $latestFiles = ProductFile::query()
                ->whereIn('o_parent', $products->keys())
                ->orderByDesc('id')
                ->get()
                ->groupBy('o_parent')
                ->map(static fn ($files) => $files->first());

            $shortLinks = Short::query()
                ->where('sh_type', 7867)
                ->whereIn('tp_id', $latestFiles->pluck('id'))
                ->get()
                ->keyBy('tp_id');

            $metadataFile = $type === 'plugins' ? 'plugin.json' : 'theme.json';
            $itemsBySlug = [];

            foreach ($products as $product) {
                $typeRow = $typeRows->get($product->id);
                $latestFile = $latestFiles->get($product->id);

                if (! $typeRow || ! $latestFile) {
                    continue;
                }

                $metadata = $this->manifestReader->readFromSource((string) $latestFile->o_mode, $metadataFile);
                if ($metadata === null) {
                    continue;
                }

                $downloadUrl = '';
                if ($product->o_order <= 0) {
                    $shortLink = $shortLinks->get($latestFile->id);
                    if ($shortLink) {
                        $downloadUrl = route('store.download.hash', $shortLink->sho);
                    }
                }

                $item = [
                    'name' => $metadata['name'],
                    'slug' => $metadata['slug'],
                    'version' => $metadata['version'],
                    'author' => $metadata['author'],
                    'description' => $metadata['description'] !== '' ? $metadata['description'] : (string) $product->o_valuer,
                    'min_myads' => $metadata['min_myads'],
                    'product_url' => route('store.show', $product->name),
                    'image_url' => (string) ($product->product_image ?? ''),
                    'download_url' => $downloadUrl,
                    'category' => (string) $typeRow->name,
                    '_priority' => (string) $this->categoryPriority((string) $typeRow->name),
                    '_product_id' => (string) $product->id,
                ];

                $existing = $itemsBySlug[$metadata['slug']] ?? null;
                if ($existing === null || $this->shouldReplace($existing, $item)) {
                    $itemsBySlug[$metadata['slug']] = $item;
                }
            }

            $items = array_values($itemsBySlug);

            usort($items, static function (array $left, array $right): int {
                $priorityCompare = ((int) $right['_priority']) <=> ((int) $left['_priority']);
                if ($priorityCompare !== 0) {
                    return $priorityCompare;
                }

                return ((int) $right['_product_id']) <=> ((int) $left['_product_id']);
            });

            foreach ($items as &$item) {
                unset($item['_priority'], $item['_product_id']);
            }
            unset($item);

            return [
                'type' => $type,
                'items' => $items,
            ];
        } catch (\Throwable) {
            return ['type' => $type, 'items' => []];
        }
    }

    private function shouldReplace(array $existing, array $incoming): bool
    {
        $priorityCompare = ((int) $incoming['_priority']) <=> ((int) $existing['_priority']);
        if ($priorityCompare !== 0) {
            return $priorityCompare > 0;
        }

        return ((int) $incoming['_product_id']) > ((int) $existing['_product_id']);
    }

    /**
     * @return array<int, string>
     */
    private function categoryNamesForType(string $type): array
    {
        return match ($type) {
            'plugins' => [StoreCategoryCatalog::PLUGINS],
            'themes' => StoreCategoryCatalog::namesForFilter(StoreCategoryCatalog::THEMES),
            default => [],
        };
    }

    private function categoryPriority(string $category): int
    {
        return match (StoreCategoryCatalog::normalize($category)) {
            StoreCategoryCatalog::THEMES => $category === StoreCategoryCatalog::THEMES ? 2 : 1,
            default => 1,
        };
    }

    private function normalizeType(string $type): string
    {
        return match ($type) {
            'plugins' => 'plugins',
            default => 'themes',
        };
    }

    private function cacheKey(string $type): string
    {
        return 'marketplace_extension_catalog_' . $type;
    }
}
