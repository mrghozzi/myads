<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Option;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KnowledgebaseCommunityService
{
    public const STATUS_TYPE = 205;
    public const REACTION_TYPE = 205;
    public const COMMENT_REACTION_TYPE = 206;
    public const COMMENT_OPTION_TYPE = 'kb_comment';

    public function publish(Product $product, Option $article, User $publisher): Status
    {
        if (
            $article->o_type !== 'knowledgebase'
            || (int) $article->o_order !== 0
            || (string) $article->o_mode !== (string) $product->name
        ) {
            throw new \InvalidArgumentException('Only published knowledgebase topics can be published to the community.');
        }

        return Status::create([
            'uid' => $publisher->id,
            'date' => time(),
            's_type' => self::STATUS_TYPE,
            'tp_id' => $article->id,
            'statu' => 1,
        ]);
    }

    public function deletePublishedStatus(Status $status): void
    {
        if ((int) $status->s_type !== self::STATUS_TYPE) {
            throw new \InvalidArgumentException('Status is not a knowledgebase community post.');
        }

        DB::transaction(function () use ($status) {
            $comments = Option::query()
                ->where('o_type', self::COMMENT_OPTION_TYPE)
                ->where('o_parent', $status->id)
                ->get();

            foreach ($comments as $comment) {
                $commentLikes = Like::query()
                    ->where('sid', $comment->id)
                    ->where('type', self::COMMENT_REACTION_TYPE)
                    ->get();

                foreach ($commentLikes as $like) {
                    Option::query()
                        ->where('o_parent', $like->id)
                        ->where('o_type', 'data_reaction')
                        ->delete();

                    $like->delete();
                }

                $comment->delete();
            }

            $statusLikes = Like::query()
                ->where('sid', $status->id)
                ->where('type', self::REACTION_TYPE)
                ->get();

            foreach ($statusLikes as $like) {
                Option::query()
                    ->where('o_parent', $like->id)
                    ->where('o_type', 'data_reaction')
                    ->delete();

                $like->delete();
            }

            $status->delete();
        });
    }
}
