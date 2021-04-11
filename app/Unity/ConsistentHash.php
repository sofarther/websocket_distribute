<?php


namespace App\Unity;


class ConsistentHash
{

    const VIRTUAL_COUNT = 160; // 每个服务器 虚拟的节点数
    // 将所有服务器节点（包括虚拟节点） 分布 到 1024 个位置
    const CONSISTENT_BUCKETS = 1024;

    private $virtualNode = []; //保存 所有的虚拟节点
    private $buckets = []; //保存 每个位置  对应的 节点

    public function generate($nodes)
    {
        $this->virtualNode = [];
        $this->buckets = [];

        // 节点 必须先进行 排序, 保证原有的的 节点 顺序相同
        sort($nodes);

        // 生成虚拟节点
        foreach ($nodes as $k => $node) {
            for ($i = 0; $i < self::VIRTUAL_COUNT; $i++) {
                $hashKey = $node . '-' . $i;
                $this->virtualNode[] = [$node, $this->hash($hashKey)];
            }

        }

        // 根据 hash 键 进行排序
        array_multisort(array_column($this->virtualNode, 1), $this->virtualNode);

        // 每个位置 分配 一个 节点
        $slice = floor(0xFFFFFFFF / self::CONSISTENT_BUCKETS);

        for ($i = 0; $i < self::CONSISTENT_BUCKETS; $i++) {
            $this->buckets[] = $this->hashFind(floor($slice * $i), 0 , count($this->virtualNode) - 1);
        }

        return $this->buckets;
    }

    public function getNodeByKey($key)
    {

        $hashKey = $this->hash($key);

        return $this->buckets[($hashKey % self::CONSISTENT_BUCKETS)][0];

    }

    // 二分法 获取 node
    private function hashFind($key, $lo, $hi)
    {

        if ($key <= $this->virtualNode[$lo][1] || $key > $this->virtualNode[$hi][1]) {
            return $this->virtualNode[$lo];
        }

        $middle = $lo + floor(($hi - $lo) / 2);

        if($middle == 0){
            return $this->virtualNode[$middle];
        }else if($key <= $this->virtualNode[$middle][1] && $key > $this->virtualNode[$middle - 1][1]){
            return $this->virtualNode[$middle];
        }else if($key > $this->virtualNode[$middle][1]){
            return $this->hashFind($key, $middle + 1, $hi);
        }

        return $this->hashFind($key, $lo, $middle -1);


    }

    // crc32 算法  生成 hash 值
    private function hash($str)
    {
        return abs(crc32($str));
    }

}