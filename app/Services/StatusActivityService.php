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
        foreach ($activities as $activity) {
            if ($activity instanceof Status) {
                $this->decorate($activity);
            }
        }
    }
}
