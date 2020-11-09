<?php

namespace App\Observers;

use App\Models\Reply;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        $reply->content = clean($reply->content, 'user_topic_body');
    }

    public function created(Reply $reply)
    {
        // 统计数据，更加的严谨
        $reply->topic->reply_count = $reply->topic->replies->count();
        //$reply->topic->increment('reply_count', 1);
    }

    public function updating(Reply $reply)
    {
        //
    }
}