<?php

namespace Tests\Feature;

use App\Models\ForumAttachment;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ForumAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_topic_can_be_created_with_attachments_downloaded_and_removed_on_topic_delete(): void
    {
        $author = User::factory()->create();

        $category = ForumCategory::create([
            'name' => 'Attachment Category',
            'icons' => 'fa-paperclip',
            'txt' => 'Attachments',
            'ordercat' => 1,
        ]);

        $createResponse = $this->actingAs($author)
            ->post('/post', [
                'name' => 'Topic With Files',
                'txt' => 'Topic body with files',
                'cat' => $category->id,
                'type' => 100,
                'attachments' => [
                    UploadedFile::fake()->image('photo.jpg')->size(120),
                    UploadedFile::fake()->create('manual.pdf', 80, 'application/pdf'),
                ],
            ]);

        $createResponse->assertStatus(302);

        $topic = ForumTopic::where('name', 'Topic With Files')->firstOrFail();
        $attachments = ForumAttachment::where('topic_id', $topic->id)->orderBy('id')->get();

        $this->assertCount(2, $attachments);

        $downloadResponse = $this->get('/forum/attachment/' . $attachments->first()->id);
        $downloadResponse->assertOk();
        $this->assertTrue($downloadResponse->headers->has('content-disposition'));

        $deleteResponse = $this->actingAs($author)
            ->postJson('/forum/delete', ['id' => $topic->id]);

        $deleteResponse->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('forum', ['id' => $topic->id]);
        $this->assertDatabaseMissing('forum_attachments', ['topic_id' => $topic->id]);
    }
}
