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
                $activity->related_content = Directory::find($activity->tp_id);
                $activity->type_label = 'Directory';
                break;
            case 2:
            case 4:
            case 100:
                $activity->related_content = ForumTopic::with(['attachments', 'imageOption'])->find($activity->tp_id);
                $activity->type_label = 'Forum';
                break;
            case 7867:
                $activity->related_content = Product::withoutGlobalScope('store')->find($activity->tp_id);
                $activity->type_label = 'Store';
                break;
            case 5:
                $activity->related_content = News::find($activity->tp_id);
                $activity->type_label = 'News';
                break;
            case 6:
                $activity->related_content = OrderRequest::find($activity->tp_id);
                $activity->type_label = 'Order';
                break;
            case KnowledgebaseCommunityService::STATUS_TYPE:
                $activity->related_content = $this->hydrateKnowledgebaseArticle($activity->tp_id);
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
