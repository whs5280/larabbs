<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\ESBuilders\TopicsBuilder;
use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\TopicRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'es']]);
    }

    // 走ES 查询，没有的话再查询数据库 todo(withOrder)
	public function index(Request $request, Topic $topic, User $user, Link $link)
	{
        $page = $request->input('page', 1);

        // ES 查询构建器
        $builder = (new TopicsBuilder)->paginate(20, $page);
        $result = app('es')->search($builder->getParams());

        // 将对象 转化为 Eloquent 模型
        $source = Topic::query()->hydrate(collect($result['hits']['hits'])->pluck('_source')->all());

        // 返回一个 LengthAwarePaginator 对象
        $topics = new LengthAwarePaginator($source, $result['hits']['total']['value'], 20, $page, [
            'path' => route('topics.index', false), // 手动构建分页的 url
        ]);

        // 数据库查询
        if (!$topics) {
            $topics = $topic->withOrder($request->order)
                ->with(['category','user']) // 预加载防止 N+1 问题
                ->paginate(20);
        }

		$active_users = $user->getActiveUsers();

        $links = $link->getAllCached();

        return view('topics.index', compact('topics', 'active_users', 'links'));
	}

    public function show(Request $request, Topic $topic)
    {
        // URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{
		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();

        return redirect()->to($topic->link())->with('success', '成功创建话题！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

        return redirect()->route('topics.show', $topic->id)->with('success', '更新成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

        return redirect()->route('topics.index')->with('success', '成功删除！');
	}

	public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($file, 'topics', Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }
}
