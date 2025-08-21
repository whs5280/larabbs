<?php

namespace App\Package\Git;

use Illuminate\Support\ServiceProvider;

/**
 * Git
 * 存储过程：不是存差异，而是存快照
 * 内容寻址：Git 不是用文件名来保存文件，而是用文件内容计算出的 SHA-1 哈希值
 * 压缩：在存储前，Git 会使用 zlib 库对文件内容进行压缩
 * 打包机制: Git 才会采用增量编码，它会查找相似的文件版本（例如一个文件的 v1 和 v2），然后只存储其中一个版本的完整内容（基础对象）
 *
 * 短期/日常操作：使用快照模式，便于快速创建和访问单个对象
 * 长期/优化存储：通过打包机制，在后台智能地使用差异存储来极致地压缩仓库体积
 */
class GitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        }

        $this->commands([
            Commands\CompareContent::class,
        ]);
    }
}
