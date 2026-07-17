<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\News;
use App\Models\Option;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\ForumTopic;
use App\Models\User;
use App\Services\KnowledgebaseCommunityService;

class StatusActivityService
{
    public function __construct(
        private readonly V420SchemaService $schema
    ) {
    }

    public function hiddenDirectoryStatusIds(): array
    {
        if (!class_exists(StatusLinkPreview::class) || !$this->schema->supports('link_previews')) {
            return [];
        }

        return StatusLinkPreview::query()
            ->whereNotNull('directory_status_id')
            ->pluck('directory_status_id')
            ->filter()
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    public function decorate(Status $activity): Status
    {
        $relations = ['user', 'group'];

        if ($this->schema->supports('link_previews')) {
            $relations[] = 'linkPreviewRecord';
        } else {
            $activity->setRelation('linkPreviewRecord', null);
        }

        if ($this->schema->supports('reposts')) {
            $relations[] = 'repostRecord.originalStatus.user';
        } else {
            $activity->setRelation('repostRecord', null);
        }

        $activity->loadMissing($relations);
        $activity->related_content = null;

        switch ((int) $activity->s_type) {
            case 1:
                $directory = Directory::find($activity->tp_id);
                $activity->setRelation('directoryListing', $directory);
                $activity->related_content = $directory;
                $activity->type_label = 'Directory';
                break;
            case 2:
            case 4:
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
            case 100:
                $topic = ForumTopic::with(['attachments', 'imageOption'])->find($activity->tp_id);
                $activity->setRelation('forumTopic', $topic);
                $activity->related_content = $topic;
                $activity->type_label = match((int) $activity->s_type) {
                    10 => 'Video',
                    11 => 'Audio',
                    12 => 'File',
                    13 => 'Music',
                    14 => 'Clips',
                    default => 'Forum',
                };
                break;
            case 7867:
                $product = Product::withoutGlobalScope('store')->find($activity->tp_id);
                $activity->setRelation('productItem', $product);
                $activity->related_content = $product;
                $activity->type_label = 'Store';
                break;
            case 5:
                $news = News::find($activity->tp_id);
                $activity->setRelation('newsItem', $news);
                $activity->related_content = $news;
                $activity->type_label = 'News';
                break;
            case 6:
                $order = OrderRequest::with([
                    'user',
                    'awardedOffer.user',
                    'contract.provider',
                ])
                    ->withCount([
                        'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
                    ])
                    ->find($activity->tp_id);
                $activity->setRelation('orderRequest', $order);
                $activity->related_content = $order;
                $activity->type_label = 'Order';
                break;
            case KnowledgebaseCommunityService::STATUS_TYPE:
                $article = $this->hydrateKnowledgebaseArticle($activity->tp_id);
                $activity->setRelation('knowledgebaseItem', $article);
                $activity->related_content = $article;
                $activity->type_label = 'Knowledgebase';
                break;
        }

        if ($this->schema->supports('reposts') && $activity->repostRecord) {
            $repostRelations = ['originalStatus.user'];
            if ($this->schema->supports('link_previews')) {
                $repostRelations[] = 'originalStatus.linkPreviewRecord';
            }

            $activity->repostRecord->loadMissing($repostRelations);
            if ($activity->repostRecord->originalStatus) {
                $this->decorate($activity->repostRecord->originalStatus);
            }
        }

        return $activity;
    }

    private function hydrateKnowledgebaseArticle(int $articleId): ?Option
    {
        $article = Option::query()
            ->where('id', $articleId)
            ->where('o_type', 'knowledgebase')
            ->first();

        if (!$article) {
            return null;
        }

        $product = Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->where('name', $article->o_mode)
            ->first();

        $author = (int) $article->o_parent > 0 ? User::find((int) $article->o_parent) : null;

        $article->setRelation('productItem', $product);
        $article->setRelation('authorUser', $author);

        return $article;
    }

    public function decorateMany(iterable $activities): void
    {
        if (is_object($activities) && method_exists($activities, 'getCollection')) {
            $activities = $activities->getCollection();
        } elseif (is_object($activities) && method_exists($activities, 'items')) {
            $activities = collect($activities->items());
        }

        $activities = collect($activities);
        if ($activities->isEmpty()) {
            return;
        }

        $statusCollection = \Illuminate\Database\Eloquent\Collection::make($activities->filter(fn ($a) => $a instanceof Status));
        if ($statusCollection->isEmpty()) {
            return;
        }

        $relations = ['user', 'group'];
        if ($this->schema->supports('link_previews')) {
            $relations[] = 'linkPreviewRecord';
        }
        if ($this->schema->supports('reposts')) {
            $relations[] = 'repostRecord.originalStatus.user';
            if ($this->schema->supports('link_previews')) {
                $relations[] = 'repostRecord.originalStatus.linkPreviewRecord';
            }
        }
        $statusCollection->loadMissing($relations);

        $allStatusesToDecorate = collect();
        foreach ($statusCollection as $status) {
            $allStatusesToDecorate->push($status);
            if ($this->schema->supports('reposts') && $status->repostRecord && $status->repostRecord->originalStatus) {
                $allStatusesToDecorate->push($status->repostRecord->originalStatus);
            }
        }

        $forumIds = [];
        $directoryIds = [];
        $storeIds = [];
        $newsIds = [];
        $orderIds = [];
        $kbIds = [];

        foreach ($allStatusesToDecorate as $status) {
            $type = (int) $status->s_type;
            if ($type === 1) {
                $directoryIds[] = $status->tp_id;
            } elseif (in_array($type, [2, 4, 10, 11, 12, 13, 14, 100], true)) {
                $forumIds[] = $status->tp_id;
            } elseif ($type === 7867) {
                $storeIds[] = $status->tp_id;
            } elseif ($type === 5) {
                $newsIds[] = $status->tp_id;
            } elseif ($type === 6) {
                $orderIds[] = $status->tp_id;
            } elseif ($type === KnowledgebaseCommunityService::STATUS_TYPE) {
                $kbIds[] = $status->tp_id;
            }
        }

        $directories = !empty($directoryIds) ? Directory::whereIn('id', array_unique($directoryIds))->get()->keyBy('id') : collect();
        $forums = !empty($forumIds) ? ForumTopic::with(['attachments', 'imageOption'])->whereIn('id', array_unique($forumIds))->get()->keyBy('id') : collect();
        $stores = !empty($storeIds) ? Product::withoutGlobalScope('store')->whereIn('id', array_unique($storeIds))->get()->keyBy('id') : collect();
        $news = !empty($newsIds) ? News::whereIn('id', array_unique($newsIds))->get()->keyBy('id') : collect();
        $orders = !empty($orderIds) ? OrderRequest::with(['user', 'awardedOffer.user', 'contract.provider'])->withCount(['offers as offers_count' => fn ($query) => $query->marketplaceVisible()])->whereIn('id', array_unique($orderIds))->get()->keyBy('id') : collect();

        $kbs = collect();
        if (!empty($kbIds)) {
            $articles = Option::whereIn('id', array_unique($kbIds))->where('o_type', 'knowledgebase')->get();
            $productNames = $articles->pluck('o_mode')->filter()->unique()->all();
            $products = !empty($productNames) ? Product::withoutGlobalScope('store')->where('o_type', 'store')->whereIn('name', $productNames)->get()->keyBy('name') : collect();
            $authorIds = $articles->pluck('o_parent')->map(static fn ($id) => (int) $id)->filter(static fn ($id) => $id > 0)->unique()->all();
            $authors = !empty($authorIds) ? User::whereIn('id', array_unique($authorIds))->get()->keyBy('id') : collect();

            foreach ($articles as $article) {
                $article->setRelation('productItem', $products->get($article->o_mode));
                $article->setRelation('authorUser', $authors->get((int) $article->o_parent));
                $kbs->put($article->id, $article);
            }
        }

        foreach ($allStatusesToDecorate as $activity) {
            $activity->related_content = null;
            $type = (int) $activity->s_type;

            if (!$this->schema->supports('link_previews')) {
                $activity->setRelation('linkPreviewRecord', null);
            }
            if (!$this->schema->supports('reposts')) {
                $activity->setRelation('repostRecord', null);
            }

            switch ($type) {
                case 1:
                    $directory = $directories->get($activity->tp_id);
                    $activity->setRelation('directoryListing', $directory);
                    $activity->related_content = $directory;
                    $activity->type_label = 'Directory';
                    break;
                case 2:
                case 4:
                case 10:
                case 11:
                case 12:
                case 13:
                case 14:
                case 100:
                    $topic = $forums->get($activity->tp_id);
                    $activity->setRelation('forumTopic', $topic);
                    $activity->related_content = $topic;
                    $activity->type_label = match ($type) {
                        10 => 'Video',
                        11 => 'Audio',
                        12 => 'File',
                        13 => 'Music',
                        14 => 'Clips',
                        default => 'Forum',
                    };
                    break;
                case 7867:
                    $product = $stores->get($activity->tp_id);
                    $activity->setRelation('productItem', $product);
                    $activity->related_content = $product;
                    $activity->type_label = 'Store';
                    break;
                case 5:
                    $newsItem = $news->get($activity->tp_id);
                    $activity->setRelation('newsItem', $newsItem);
                    $activity->related_content = $newsItem;
                    $activity->type_label = 'News';
                    break;
                case 6:
                    $order = $orders->get($activity->tp_id);
                    $activity->setRelation('orderRequest', $order);
                    $activity->related_content = $order;
                    $activity->type_label = 'Order';
                    break;
                case KnowledgebaseCommunityService::STATUS_TYPE:
                    $article = $kbs->get($activity->tp_id);
                    $activity->setRelation('knowledgebaseItem', $article);
                    $activity->related_content = $article;
                    $activity->type_label = 'Knowledgebase';
                    break;
            }
        }
    }
}
