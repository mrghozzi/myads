<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Setting;
use App\Models\Status;
use App\Models\User;
use App\Services\RichTextEditorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumModernizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::create([
            'titer' => 'MYADS',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);
    }

    public function test_rich_text_editor_service_registers_myads_markdown_alongside_quill_and_sceditor(): void
    {
        $editors = RichTextEditorService::getAvailableEditors();

        $this->assertArrayHasKey('myads_markdown', $editors);
        $this->assertArrayHasKey('quill', $editors);
        $this->assertArrayHasKey('sceditor', $editors);

        RichTextEditorService::setActiveEditor('myads_markdown');
        $this->assertSame('myads_markdown', RichTextEditorService::getActiveEditor());

        RichTextEditorService::setActiveEditor('quill');
        $this->assertSame('quill', RichTextEditorService::getActiveEditor());
    }

    public function test_admin_can_create_and_update_forum_category_with_color_and_slug(): void
    {
        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'admin-user',
            'email' => 'admin@example.com',
        ]);

        $response = $this->actingAs($admin)->post('/admin/forum/categories', [
            'name' => 'Web Development',
            'icons' => 'fa-code',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'Discussions about web development.',
            'color' => '#00c7d9',
        ]);

        $response->assertRedirect();

        $cat = ForumCategory::where('name', 'Web Development')->firstOrFail();
        $this->assertSame('#00c7d9', $cat->color);
        $this->assertNotEmpty($cat->slug);

        // Update category color
        $updateResponse = $this->actingAs($admin)->post('/admin/forum/categories/' . $cat->id, [
            'name' => 'Web Development Pro',
            'icons' => 'fa-code',
            'ordercat' => 2,
            'visibility' => 0,
            'txt' => 'Updated desc.',
            'color' => '#615dfa',
        ]);

        $updateResponse->assertRedirect();
        $cat->refresh();
        $this->assertSame('Web Development Pro', $cat->name);
        $this->assertSame('#615dfa', $cat->color);
    }

    public function test_forum_index_renders_modern_stream_and_supports_ajax_json_filtering(): void
    {
        $user = User::factory()->create([
            'id' => 10,
            'username' => 'author10',
            'email' => 'author10@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'General Chat',
            'icons' => 'fa-comments',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'General discussions',
            'color' => '#615dfa',
            'slug' => 'general-chat',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Modern Forum Architecture in MYADS',
            'txt' => 'This is a sample discussion in modern forum style.',
            'cat' => $cat->id,
            'uid' => $user->id,
            'date' => time(),
            'statu' => 1,
            'vu' => 42,
            'slug' => 'modern-forum-modern-architecture-in-myads',
            'comments_count' => 0,
        ]);

        Status::create([
            'uid' => $user->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        // Standard GET renders view
        $htmlResponse = $this->get('/forum');
        $htmlResponse->assertOk();
        $htmlResponse->assertSee('forum-modern-layout', false);
        $htmlResponse->assertSee('Modern Forum Architecture in MYADS');

        // Ajax request returns JSON stream HTML
        $ajaxResponse = $this->getJson('/forum?filter=latest');
        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonStructure([
            'html',
            'has_more',
            'current_page',
            'total',
        ]);
        $ajaxResponse->assertJsonFragment([
            'current_page' => 1,
            'total' => 1,
        ]);
    }

    public function test_commenting_updates_forum_topic_comments_count_and_last_posted_metadata(): void
    {
        $user = User::factory()->create([
            'id' => 20,
            'username' => 'commenter20',
            'email' => 'commenter20@example.com',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Realtime Sync Discussion',
            'txt' => 'Testing comment stats synchronization.',
            'cat' => 1,
            'uid' => $user->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'forum-modern-realtime-sync-discussion',
            'comments_count' => 0,
        ]);

        Status::create([
            'uid' => $user->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $this->assertSame(0, (int) $topic->comments_count);

        // Submit comment
        $response = $this->actingAs($user)->post(route('comment.store'), [
            'id' => $topic->id,
            'type' => 'forum',
            'comment' => 'Great forum addition!',
        ]);

        $response->assertOk();

        $topic->refresh();
        $this->assertSame(1, (int) $topic->comments_count);
        $this->assertSame($user->id, (int) $topic->last_poster_id);
        $this->assertNotNull($topic->last_posted_at);
    }

    public function test_category_page_renders_modern_layout_with_selected_category(): void
    {
        $cat = ForumCategory::create([
            'name' => 'Technology',
            'icons' => 'fa-laptop',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'Tech discussions',
            'color' => '#f0932b',
            'slug' => 'technology',
        ]);

        $response = $this->get('/f' . $cat->id);
        $response->assertOk();
        $response->assertSee('Technology');
        $response->assertSee('#f0932b');
        $response->assertSee('forum-modern-layout', false);
    }

    public function test_topic_discussion_page_renders_modern_post_and_scrubber(): void
    {
        $author = User::factory()->create([
            'id' => 30,
            'username' => 'forum_master',
            'email' => 'master@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'Announcements',
            'icons' => 'fa-bullhorn',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'Important announcements',
            'color' => '#22c55e',
            'slug' => 'announcements',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Release of MYADS v4.6.3 Modern Forum Edition',
            'txt' => 'Welcome to the new modern forum experience!',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'vu' => 100,
            'slug' => 'release-of-myads-v463-forum-modern-edition',
            'comments_count' => 0,
        ]);

        Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $response = $this->get('/t' . $topic->id);
        $response->assertOk();
        $response->assertSee('Release of MYADS v4.6.3 Modern Forum Edition');
        $response->assertSee('forum-modern-scrubber', false);
        $response->assertSee('forum_master');
        $response->assertSee('Welcome to the new modern forum experience!');
    }

    public function test_create_topic_renders_with_myads_markdown_and_quill_editors(): void
    {
        $user = User::factory()->create([
            'id' => 40,
            'username' => 'writer40',
            'email' => 'writer40@example.com',
        ]);

        RichTextEditorService::setActiveEditor('myads_markdown');
        $response = $this->actingAs($user)->get(route('forum.create'));
        $response->assertOk();
        $response->assertSee('forum-modern-editor-toolbar', false);

        RichTextEditorService::setActiveEditor('quill');
        $quillResponse = $this->actingAs($user)->get(route('forum.create'));
        $quillResponse->assertOk();
        $quillResponse->assertSee('quill', false);
    }

    public function test_forum_stream_and_categories_strictly_isolate_stype_2_topics(): void
    {
        $author = User::factory()->create([
            'id' => 50,
            'username' => 'forumUser50',
            'email' => 'forumUser50@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'General Topics Only',
            'icons' => 'fa-comments',
            'ordercat' => 10,
            'visibility' => 0,
            'txt' => 'Discussions section',
            'color' => '#3b82f6',
            'slug' => 'general-topics-only',
        ]);

        // 1. Legitimate forum topic (s_type = 2)
        $forumTopic = ForumTopic::create([
            'name' => 'Exclusive Forum Discussion Topic',
            'txt' => 'This is a genuine discussion for forum only.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'exclusive-forum-discussion-topic',
            'comments_count' => 0,
        ]);
        Status::create([
            'uid' => $author->id,
            'tp_id' => $forumTopic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        // 2. Community blog post (s_type = 100)
        $blogTopic = ForumTopic::create([
            'name' => 'Community Blog Post Article',
            'txt' => 'Social feed long article.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'community-blog-post-article',
            'comments_count' => 0,
        ]);
        Status::create([
            'uid' => $author->id,
            'tp_id' => $blogTopic->id,
            's_type' => 100,
            'date' => time(),
            'statu' => 1,
        ]);

        // 3. Community photo/gallery post (s_type = 4)
        $galleryTopic = ForumTopic::create([
            'name' => 'Community Photo Gallery Image',
            'txt' => 'Social gallery upload.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'community-photo-gallery-image',
            'comments_count' => 0,
        ]);
        Status::create([
            'uid' => $author->id,
            'tp_id' => $galleryTopic->id,
            's_type' => 4,
            'date' => time(),
            'statu' => 1,
        ]);

        // 4. Video post (s_type = 10)
        $videoTopic = ForumTopic::create([
            'name' => 'Community Video Hub Clip',
            'txt' => 'Social video post.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'community-video-hub-clip',
            'comments_count' => 0,
        ]);
        Status::create([
            'uid' => $author->id,
            'tp_id' => $videoTopic->id,
            's_type' => 10,
            'date' => time(),
            'statu' => 1,
        ]);

        // 5. Short reel / clip (s_type = 14)
        $clipTopic = ForumTopic::create([
            'name' => 'Community Short Reel 9x16',
            'txt' => 'Vertical clip reel.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'community-short-reel-9x16',
            'comments_count' => 0,
        ]);
        Status::create([
            'uid' => $author->id,
            'tp_id' => $clipTopic->id,
            's_type' => 14,
            'date' => time(),
            'statu' => 1,
        ]);

        // Verify GET /forum
        $forumResponse = $this->get('/forum');
        $forumResponse->assertOk();
        $forumResponse->assertSee('Exclusive Forum Discussion Topic');
        $forumResponse->assertSee('hexagon-image-30-32');
        $forumResponse->assertSee('user-avatar small no-outline');
        $forumResponse->assertDontSee('Community Blog Post Article');
        $forumResponse->assertDontSee('Community Photo Gallery Image');
        $forumResponse->assertDontSee('Community Video Hub Clip');
        $forumResponse->assertDontSee('Community Short Reel 9x16');

        // Verify JSON Ajax Stream
        $ajaxResponse = $this->getJson('/forum?filter=latest');
        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonFragment([
            'total' => 1,
        ]);
        $this->assertStringContainsString('hexagon-image-30-32', $ajaxResponse->json('html'));

        // Verify category stream /f{id}
        $catResponse = $this->get('/f' . $cat->id);
        $catResponse->assertOk();
        $catResponse->assertSee('Exclusive Forum Discussion Topic');
        $catResponse->assertSee('hexagon-image-30-32');
        $catResponse->assertDontSee('Community Blog Post Article');
        $catResponse->assertDontSee('Community Photo Gallery Image');

        // Verify topic view /t{id}
        $topicResponse = $this->get('/t' . $forumTopic->id);
        $topicResponse->assertOk();
        $topicResponse->assertSee('Exclusive Forum Discussion Topic');
        $topicResponse->assertSee('hexagon-image-30-32');
        $topicResponse->assertSee('user-avatar small no-outline');
    }

    public function test_forum_topic_renders_modern_comments_and_threaded_replies(): void
    {
        $author = User::factory()->create([
            'id' => 70,
            'username' => 'topicAuthor70',
            'email' => 'author70@example.com',
        ]);
        $replier = User::factory()->create([
            'id' => 71,
            'username' => 'replyUser71',
            'email' => 'reply71@example.com',
        ]);
        $subReplier = User::factory()->create([
            'id' => 72,
            'username' => 'nestedUser72',
            'email' => 'nested72@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'Community Discussion Group',
            'icons' => 'fa-comments',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'Test',
            'color' => '#615dfa',
            'slug' => 'forum-modern-discussion-group',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Testing Threaded Comments in Modern Forum',
            'txt' => 'Original post discussion text.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'testing-threaded-comments-in-modern-forum',
            'comments_count' => 0,
        ]);
        Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        // 1. Post a top-level comment
        $commentResponse = $this->actingAs($replier)->post('/comment/store', [
            'id' => $topic->id,
            'type' => 'forum',
            'comment' => 'This is the first top-level reply.',
        ]);
        $commentResponse->assertOk();

        $this->assertDatabaseHas('f_coment', [
            'tid' => $topic->id,
            'uid' => $replier->id,
            'parent_id' => null,
            'txt' => 'This is the first top-level reply.',
        ]);

        $topComment = ForumComment::where('tid', $topic->id)->whereNull('parent_id')->first();
        $this->assertNotNull($topComment);

        // 2. Post a nested / threaded reply to that comment
        $nestedResponse = $this->actingAs($subReplier)->post('/comment/store', [
            'id' => $topic->id,
            'type' => 'forum',
            'parent_id' => $topComment->id,
            'comment' => '@' . $replier->username . ' This is a nested response!',
        ]);
        $nestedResponse->assertOk();

        $this->assertDatabaseHas('f_coment', [
            'tid' => $topic->id,
            'uid' => $subReplier->id,
            'parent_id' => $topComment->id,
        ]);

        // 3. View the topic and verify the modern post stream & tree structure
        $viewResponse = $this->actingAs($author)->get('/t' . $topic->id);
        $viewResponse->assertOk();

        // Check cards and reply composer
        $viewResponse->assertSee('forum-modern-comment-card');
        $viewResponse->assertSee('This is the first top-level reply.');
        $viewResponse->assertSee('forum-modern-comment-replies');
        $viewResponse->assertSee('forum-modern-nested-card');
        $viewResponse->assertSee('This is a nested response!');
        $viewResponse->assertSee('forum-modern-reply-card');
        $viewResponse->assertSee('forum-modern-editor-toolbar');
        $viewResponse->assertSee('forum-modern-reply-textarea');
    }

    public function test_topic_share_button_renders_clean_container_and_copy_action(): void
    {
        $user = User::factory()->create([
            'id' => 300,
            'username' => 'shareuser',
            'email' => 'shareuser@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'General Sharing',
            'icons' => 'fa-share',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'Discussions about sharing',
            'slug' => 'general-sharing',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Testing Share Button and Copy Link',
            'txt' => 'Content of topic to test share button.',
            'cat' => $cat->id,
            'uid' => $user->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'testing-share-button-and-copy-link',
        ]);

        Status::create([
            'uid' => $user->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $response = $this->actingAs($user)->get('/t' . $topic->id);
        $response->assertOk();
        $response->assertSee('forum-modern-share-container');
        $response->assertSee('forum-modern-share-dropdown');
        $response->assertSee('forum-modern-share-copy-btn');
        $response->assertSee('toggleForumShare');
        $response->assertSee('copyTopicUrl');
    }

    public function test_topic_author_and_moderator_can_pin_and_unpin_comment(): void
    {
        $author = User::factory()->create([
            'id' => 310,
            'username' => 'topicauthor310',
            'email' => 'author310@example.com',
        ]);

        $commenter = User::factory()->create([
            'id' => 311,
            'username' => 'commenter311',
            'email' => 'commenter311@example.com',
        ]);

        $stranger = User::factory()->create([
            'id' => 312,
            'username' => 'stranger312',
            'email' => 'stranger312@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'Pinning Test',
            'icons' => 'fa-thumb-tack',
            'ordercat' => 1,
            'visibility' => 0,
            'slug' => 'pinning-test',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Topic with Pinned Comment',
            'txt' => 'Topic description for pinning comment.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'topic-with-pinned-comment',
        ]);

        Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $comment = ForumComment::create([
            'tid' => $topic->id,
            'uid' => $commenter->id,
            'txt' => 'This important comment should be pinned.',
            'date' => time(),
        ]);

        // Unauthorized user cannot pin
        $unauthRes = $this->actingAs($stranger)->postJson('/forum/comment/' . $comment->id . '/pin');
        $unauthRes->assertStatus(403);

        // Topic Author can pin
        $pinRes = $this->actingAs($author)->postJson('/forum/comment/' . $comment->id . '/pin');
        $pinRes->assertOk();
        $pinRes->assertJsonFragment(['success' => true, 'is_pinned' => true]);

        $comment->refresh();
        $this->assertTrue((bool) $comment->is_pinned);
        $topic->refresh();
        $this->assertSame((int) $comment->id, (int) $topic->pinned_comment_id);

        // Topic Author can unpin
        $unpinRes = $this->actingAs($author)->postJson('/forum/comment/' . $comment->id . '/pin');
        $unpinRes->assertOk();
        $unpinRes->assertJsonFragment(['success' => true, 'is_pinned' => false]);

        $comment->refresh();
        $this->assertFalse((bool) $comment->is_pinned);
        $topic->refresh();
        $this->assertNull($topic->pinned_comment_id);
    }

    public function test_topic_author_and_moderator_can_select_and_unselect_best_answer(): void
    {
        $author = User::factory()->create([
            'id' => 320,
            'username' => 'topicauthor320',
            'email' => 'author320@example.com',
        ]);

        $commenter = User::factory()->create([
            'id' => 321,
            'username' => 'commenter321',
            'email' => 'commenter321@example.com',
        ]);

        $stranger = User::factory()->create([
            'id' => 322,
            'username' => 'stranger322',
            'email' => 'stranger322@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'Best Answer Category',
            'icons' => 'fa-check',
            'ordercat' => 1,
            'visibility' => 0,
            'slug' => 'best-answer-category',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Discussion Seeking Best Answer',
            'txt' => 'Can someone give the correct solution?',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'discussion-seeking-best-answer',
        ]);

        Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $comment = ForumComment::create([
            'tid' => $topic->id,
            'uid' => $commenter->id,
            'txt' => 'Here is the perfect answer and solution.',
            'date' => time(),
        ]);

        // Stranger cannot mark best answer
        $unauthRes = $this->actingAs($stranger)->postJson('/forum/comment/' . $comment->id . '/best-answer');
        $unauthRes->assertStatus(403);

        // Author marks as best answer
        $bestRes = $this->actingAs($author)->postJson('/forum/comment/' . $comment->id . '/best-answer');
        $bestRes->assertOk();
        $bestRes->assertJsonFragment(['success' => true, 'is_best_answer' => true]);

        $comment->refresh();
        $this->assertTrue((bool) $comment->is_best_answer);
        $topic->refresh();
        $this->assertSame((int) $comment->id, (int) $topic->best_comment_id);

        // Topic view shows best answer badge and action buttons
        $viewRes = $this->actingAs($author)->get('/t' . $topic->id);
        $viewRes->assertOk();
        $viewRes->assertSee('forum-modern-badge-best-answer');
        $viewRes->assertSee('is-best-answer');

        // Author unmarks best answer
        $unmarkRes = $this->actingAs($author)->postJson('/forum/comment/' . $comment->id . '/best-answer');
        $unmarkRes->assertOk();
        $unmarkRes->assertJsonFragment(['success' => true, 'is_best_answer' => false]);

        $comment->refresh();
        $this->assertFalse((bool) $comment->is_best_answer);
        $topic->refresh();
        $this->assertNull($topic->best_comment_id);
    }

    public function test_comment_owner_and_moderator_can_delete_comment(): void
    {
        $commentAuthor = User::factory()->create([
            'id' => 350,
            'username' => 'commentowner350',
            'email' => 'commentowner350@example.com',
        ]);

        $moderator = User::factory()->create([
            'id' => 351,
            'username' => 'mod351',
            'email' => 'mod351@example.com',
        ]);

        $stranger = User::factory()->create([
            'id' => 352,
            'username' => 'stranger352',
            'email' => 'stranger352@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'Deletion Test Category',
            'icons' => 'fa-trash',
            'ordercat' => 1,
            'visibility' => 0,
            'slug' => 'deletion-test-category',
        ]);

        // Register moderator
        $modRecord = \App\Models\ForumModerator::create([
            'user_id' => $moderator->id,
            'is_global' => true,
            'permissions' => ['delete_comments', 'edit_topics', 'pin_topics'],
            'is_active' => true,
        ]);

        $topic = ForumTopic::create([
            'name' => 'Topic For Deletion Testing',
            'txt' => 'Topic description.',
            'cat' => $cat->id,
            'uid' => $commentAuthor->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'topic-for-deletion-testing',
        ]);

        Status::create([
            'uid' => $commentAuthor->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $comment1 = ForumComment::create([
            'tid' => $topic->id,
            'uid' => $commentAuthor->id,
            'txt' => 'Comment to be deleted by its owner.',
            'date' => time(),
        ]);

        $comment2 = ForumComment::create([
            'tid' => $topic->id,
            'uid' => $commentAuthor->id,
            'txt' => 'Comment to be deleted by the moderator.',
            'date' => time(),
        ]);

        // Stranger cannot delete comment1
        $unauthRes = $this->actingAs($stranger)->postJson('/comment/delete', [
            'trashid' => $comment1->id,
            'type' => 'forum',
        ]);
        $unauthRes->assertStatus(403);
        $this->assertDatabaseHas('f_coment', ['id' => $comment1->id]);

        // Stranger does NOT see delete action button in topic view
        $strangerView = $this->actingAs($stranger)->get('/t' . $topic->id);
        $strangerView->assertOk();
        $strangerView->assertDontSee('trash_comment' . $comment1->id);

        // Comment Owner sees delete button and can delete their own comment
        $ownerView = $this->actingAs($commentAuthor)->get('/t' . $topic->id);
        $ownerView->assertOk();
        $ownerView->assertSee('trash_comment' . $comment1->id);
        $ownerView->assertSee('forum-modern-delete-btn');

        $ownerDeleteRes = $this->actingAs($commentAuthor)->postJson('/comment/delete', [
            'trashid' => $comment1->id,
            'type' => 'forum',
        ]);
        $ownerDeleteRes->assertOk();
        $ownerDeleteRes->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseMissing('f_coment', ['id' => $comment1->id]);

        // Moderator sees delete button and can delete comment2
        $modView = $this->actingAs($moderator)->get('/t' . $topic->id);
        $modView->assertOk();
        $modView->assertSee('trash_comment' . $comment2->id);

        $modDeleteRes = $this->actingAs($moderator)->postJson('/comment/delete', [
            'trashid' => $comment2->id,
            'type' => 'forum',
        ]);
        $modDeleteRes->assertOk();
        $modDeleteRes->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseMissing('f_coment', ['id' => $comment2->id]);
    }

    public function test_admin_can_create_sub_forum_under_parent_category(): void
    {
        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'admin-user',
            'email' => 'admin@example.com',
        ]);

        $parent = ForumCategory::create([
            'name' => 'Technology',
            'icons' => 'fa-laptop',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'Tech discussions',
            'color' => '#4f46e5',
        ]);

        $response = $this->actingAs($admin)->post('/admin/forum/categories', [
            'name' => 'AI & Machine Learning',
            'parent_id' => $parent->id,
            'icons' => 'fa-brain',
            'ordercat' => 0,
            'visibility' => 0,
            'txt' => 'Discussions about AI and modern ML models.',
            'color' => '#10b981',
        ]);

        $response->assertRedirect();

        $sub = ForumCategory::where('name', 'AI & Machine Learning')->firstOrFail();
        $this->assertEquals($parent->id, $sub->parent_id);
        $this->assertTrue($sub->isSubCategory());
        $this->assertFalse($sub->isRoot());
        $this->assertEquals(1, $sub->depth());
        $this->assertTrue($parent->children->contains('id', $sub->id));
    }

    public function test_admin_can_reorder_categories_and_change_hierarchy_via_ajax(): void
    {
        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'admin-user',
            'email' => 'admin@example.com',
        ]);

        $cat1 = ForumCategory::create([
            'name' => 'General',
            'icons' => 'fa-comments',
            'ordercat' => 0,
            'visibility' => 0,
        ]);

        $cat2 = ForumCategory::create([
            'name' => 'Support',
            'icons' => 'fa-life-ring',
            'ordercat' => 1,
            'visibility' => 0,
        ]);

        $cat3 = ForumCategory::create([
            'name' => 'Feedback',
            'icons' => 'fa-bullhorn',
            'ordercat' => 2,
            'visibility' => 0,
        ]);

        // Drag cat3 to be sub-forum of cat1, and swap order of cat1 and cat2
        $response = $this->actingAs($admin)->postJson('/admin/forum/categories/reorder', [
            'items' => [
                ['id' => $cat2->id, 'parent_id' => null, 'order' => 0],
                ['id' => $cat1->id, 'parent_id' => null, 'order' => 1],
                ['id' => $cat3->id, 'parent_id' => $cat1->id, 'order' => 0],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['success' => true]);

        $cat1->refresh();
        $cat2->refresh();
        $cat3->refresh();

        $this->assertSame(1, $cat1->ordercat);
        $this->assertNull($cat1->parent_id);

        $this->assertSame(0, $cat2->ordercat);
        $this->assertNull($cat2->parent_id);

        $this->assertSame(0, $cat3->ordercat);
        $this->assertSame($cat1->id, $cat3->parent_id);
    }

    public function test_admin_cannot_set_descendant_or_self_as_parent(): void
    {
        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'admin-user',
            'email' => 'admin@example.com',
        ]);

        $parent = ForumCategory::create([
            'name' => 'Main Category',
            'icons' => 'fa-folder',
            'ordercat' => 0,
            'visibility' => 0,
        ]);

        $child = ForumCategory::create([
            'parent_id' => $parent->id,
            'name' => 'Sub Category',
            'icons' => 'fa-file',
            'ordercat' => 0,
            'visibility' => 0,
        ]);

        // Attempt to set self as parent
        $selfRes = $this->actingAs($admin)->post('/admin/forum/categories/' . $parent->id, [
            'name' => 'Main Category',
            'parent_id' => $parent->id,
            'icons' => 'fa-folder',
            'ordercat' => 0,
            'visibility' => 0,
        ]);
        $selfRes->assertSessionHasErrors('parent_id');

        // Attempt to set child as parent (circular reference)
        $circRes = $this->actingAs($admin)->post('/admin/forum/categories/' . $parent->id, [
            'name' => 'Main Category',
            'parent_id' => $child->id,
            'icons' => 'fa-folder',
            'ordercat' => 0,
            'visibility' => 0,
        ]);
        $circRes->assertSessionHasErrors('parent_id');
    }

    public function test_forum_category_view_aggregates_sub_forum_topics_and_renders_sub_forums(): void
    {
        $user = User::factory()->create([
            'id' => 12,
            'username' => 'poster12',
            'email' => 'poster12@example.com',
        ]);

        $parent = ForumCategory::create([
            'name' => 'Development',
            'icons' => 'fa-code',
            'ordercat' => 0,
            'visibility' => 0,
            'color' => '#6366f1',
        ]);

        $sub = ForumCategory::create([
            'parent_id' => $parent->id,
            'name' => 'PHP & Laravel',
            'icons' => 'fa-php',
            'ordercat' => 0,
            'visibility' => 0,
            'color' => '#ef4444',
        ]);

        // Topic in sub-category
        $topic = ForumTopic::create([
            'uid' => $user->id,
            'name' => 'Laravel 11 Features',
            'txt' => 'Discussion on modern Laravel features.',
            'date' => now()->subMinute(),
            'cat' => $sub->id,
            'statu' => 1,
            'vu' => 5,
        ]);

        Status::create([
            'uid' => $user->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        // Viewing parent category should aggregate topics from sub-category and display sub-forum card
        $parentView = $this->get('/f' . $parent->id);
        $parentView->assertOk();
        $parentView->assertSee('PHP & Laravel');
        $parentView->assertSee('Laravel 11 Features');
        $parentView->assertSee('forum-modern-subforum-card');

        // Viewing topic in sub-category should display ancestor in breadcrumbs
        $topicView = $this->get('/t' . $topic->id);
        $topicView->assertOk();
        $topicView->assertSee('Development');
        $topicView->assertSee('PHP & Laravel');
    }

    public function test_portal_renders_modern_forum_feed_card(): void
    {
        $author = User::factory()->create([
            'id' => 400,
            'username' => 'portalAuthor400',
            'email' => 'author400@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'Artificial Intelligence',
            'icons' => 'fa-brain',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'AI discussions',
            'color' => '#10b981',
            'slug' => 'artificial-intelligence',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Future of Agentic AI Systems in 2026',
            'txt' => 'This is an in-depth discussion about agentic coding and autonomous agents in modern software engineering.',
            'cat' => $cat->id,
            'uid' => $author->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'future-of-agentic-ai-systems-in-2026',
            'vu' => 42,
            'is_pinned' => true,
        ]);

        Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $response = $this->get('/portal');
        $response->assertOk();
        $response->assertSee('forum-feed-card');
        $response->assertSee('Future of Agentic AI Systems in 2026');
        $response->assertSee('Artificial Intelligence');
        $response->assertSee('#10b981');
        $response->assertSee('forum-feed-cat-pill');
        $response->assertSee('forum-status-pill pinned');
        $response->assertSee('forum-feed-join-btn');
        $response->assertSee(route('forum.topic', $topic->id) . '#reply', false);
        $response->assertSee('forum-comment-link', false);

        $topicResponse = $this->get('/t' . $topic->id);
        $topicResponse->assertOk();
        $topicResponse->assertSee('id="reply"', false);
        $topicResponse->assertSee('checkAndFocusReply', false);
    }

    public function test_forum_topic_sidebar_scrubber_renders_moderator_tools_only_for_moderators(): void
    {
        $regularAuthor = User::factory()->create([
            'id' => 501,
            'username' => 'regularAuthor501',
            'email' => 'regularAuthor501@example.com',
        ]);

        $regularViewer = User::factory()->create([
            'id' => 502,
            'username' => 'regularViewer502',
            'email' => 'regularViewer502@example.com',
        ]);

        $adminUser = User::factory()->create([
            'id' => 1,
            'username' => 'superadmin1',
            'email' => 'admin@example.com',
        ]);

        $cat = ForumCategory::create([
            'name' => 'Tech & Security',
            'icons' => 'fa-microchip',
            'ordercat' => 1,
            'visibility' => 0,
            'txt' => 'Tech discussions',
            'color' => '#8b5cf6',
            'slug' => 'tech-and-security',
        ]);

        $topic = ForumTopic::create([
            'name' => 'Cybersecurity Guidelines for 2026',
            'txt' => 'Key security practices for cloud infrastructure.',
            'cat' => $cat->id,
            'uid' => $regularAuthor->id,
            'date' => time(),
            'statu' => 1,
            'slug' => 'cybersecurity-guidelines-for-2026',
            'is_pinned' => false,
            'is_locked' => false,
        ]);

        Status::create([
            'uid' => $regularAuthor->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        // 1. Guest visits topic: should NOT see moderator tools
        $guestResponse = $this->get('/t' . $topic->id);
        $guestResponse->assertOk();
        $guestResponse->assertDontSee('forum-modern-mod-tools-card');

        // 2. Regular member (viewer) visits topic: should NOT see moderator tools
        $viewerResponse = $this->actingAs($regularViewer)->get('/t' . $topic->id);
        $viewerResponse->assertOk();
        $viewerResponse->assertDontSee('forum-modern-mod-tools-card');

        // 3. Regular author visits own topic: should NOT see moderator tools in sidebar
        $authorResponse = $this->actingAs($regularAuthor)->get('/t' . $topic->id);
        $authorResponse->assertOk();
        $authorResponse->assertDontSee('forum-modern-mod-tools-card');

        // 4. Admin/Moderator visits topic: MUST see moderator tools in sidebar
        $adminResponse = $this->actingAs($adminUser)->get('/t' . $topic->id);
        $adminResponse->assertOk();
        $adminResponse->assertSee('forum-modern-mod-tools-card');
        $adminResponse->assertSee('forum-modern-mod-tool-btn');
        $adminResponse->assertSee(route('forum.pin', $topic->id));
        $adminResponse->assertSee(route('forum.lock', $topic->id));
        $adminResponse->assertSee(route('forum.edit', $topic->id));
        $adminResponse->assertSee('deleteForumTopic(' . $topic->id . ')', false);
    }
}



