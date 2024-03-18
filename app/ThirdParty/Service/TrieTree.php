<?php

namespace App\ThirdParty\Service;

use Illuminate\Support\Facades\Cache;

class TrieTree
{
    CONST TRIE_CACHE_KEY = 'trie_tree_online:string';

    private $tree = array();

    /**
     * 构造函数
     * @param array|null $tree
     */
    public function __construct(array $tree = null)
    {
        if(!empty($tree)) {
            $this->tree = $tree;
        }
    }

    /**
     * 初始化
     * @param array $strList
     * @return TrieTree
     */
    public static function init(array $strList): TrieTree
    {
        return self::buildTrieTree($strList);
    }

    /**
     * @return array
     */
    public function getTree(): array
    {
        return $this->tree;
    }

    /**
     * 构建Trie树
     * @param $strList
     * @return TrieTree
     */
    public static function buildTrieTree($strList): TrieTree
    {
        $tree = Cache::get(self::TRIE_CACHE_KEY);
        if($tree) {
            return new self($tree);
        } else {
            $tree = new self();
            foreach ($strList as $str) {
                $tree->addWordToTrieTree($str);
            }
            return $tree;
        }
    }

    /**
     * 添加单词到Trie树 (递归思路)
     * @param $keyword
     * @return void
     */
    public function addWordToTrieTree($keyword)
    {
        $charArr = $this->strSplit($keyword);
        $charArr[] = null;  // 串结尾字符
        $T = &$this->tree;
        for ($i = 0; $i < count($charArr); $i++) {
            $c = $charArr[$i];
            if (!array_key_exists($c, $T)) {
                $T[$c] = array();   // 插入新字符，关联数组
            }
            $T = &$T[$c];
        }
    }

    /**
     * 相识度匹配，联想查询
     * @param $prefix
     * @return array
     */
    public function query($prefix): array
    {
        $subTree = $this->findSubTree($this->strSplit($prefix), $this->tree);
        $words = $this->traverseTree($subTree);
        foreach ($words as &$word) {
            $word = $prefix . $word;
        }
        return $words;
    }

    /**
     * 查找子树
     * @param $charArray
     * @param $tree
     * @return array|mixed
     */
    public function findSubTree($charArray, $tree)
    {
        foreach ($charArray as $char) {
            if (array_key_exists($char, $tree)) {
                $tree = $tree[$char];
            } else {
                return [];
            }
        }
        return $tree;
    }

    /**
     * 遍历树
     * @param $tree
     * @return array
     */
    public function traverseTree($tree): array
    {
        $words = [];
        foreach ($tree as $node => $subTree) {
            if (empty($subTree)) {
                $words[] = $node;
                return $words;
            }
            $chars = $this->traverseTree($subTree);
            foreach ($chars as $char) {
                $words[] = $node . $char;
            }
        }
        return $words;
    }

    /**
     * 字符串分割
     * @param $str
     * @return array|false|string[]
     */
    private function strSplit($str)
    {
        return preg_split('/(?<!^)(?!$)/u', $str);
    }
}
