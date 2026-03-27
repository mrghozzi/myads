<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\News;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\ForumTopic;

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
        $relations = ['user'];

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

    public function decorateMany(iterable $activities): void
    {
        foreach ($activities as $activity) {
            if ($activity instanceof Status) {
                $this->decorate($activity);
            }
        }
    }
}
