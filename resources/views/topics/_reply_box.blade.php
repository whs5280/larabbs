@include('shared._error')

<div class="reply-box">
    <form action="{{ route('replies.store') }}" method="POST" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="topic_id" value="{{ $topic->id }}">
        <div class="form-group">
            <textarea class="form-control" rows="3" placeholder="分享你的见解~" name="content"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-share mr-1"></i> 回复</button>
    </form>
</div>
<hr>

<ul class="list-unstyled">
    @foreach ($replies as $index => $reply)
        <li class=" media" name="reply{{ $reply->id }}" id="reply{{ $reply->id }}">
            <div class="media-left">
                <a href="{{ route('users.show', [$reply->user_id]) }}">
                    <img class="media-object img-thumbnail mr-3" alt="{{ $reply->user->name }}" src="{{ $reply->user->avatar }}" style="width:48px;height:48px;" />
                </a>
            </div>

            <div class="media-body">
                <div class="media-heading mt-0 mb-1 text-secondary">
                    <a href="{{ route('users.show', [$reply->user_id]) }}" title="{{ $reply->user->name }}">
                        {{ $reply->user->name }}
                    </a>
                    <span class="text-secondary"> • </span>
                    <span class="meta text-secondary" title="{{ $reply->created_at }}">{{ $reply->created_at->diffForHumans() }}</span>

                    {{-- 回复删除按钮 --}}
                    <span class="meta float-right ">
            <a title="删除回复">
              <i class="far fa-trash-alt"></i>
            </a>
          </span>
                </div>
                <div class="reply-content text-secondary">
                    {!! $reply->content !!}
                </div>
            </div>
        </li>

        @if ( ! $loop->last)
            <hr>
        @endif

    @endforeach
</ul>