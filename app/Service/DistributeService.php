<?php


namespace App\Service;


use App\Constants\RedisKey;
use App\Unity\ConsistentHash;
use Hyperf\Redis\Redis;
use Hyperf\Di\Annotation\Inject;

use Psr\Container\ContainerInterface;

class DistributeService
{

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @Inject
     * @var ConsistentHash
     */
    private $hash;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function updateServer()
    {
        $redis = $this->container->get(Redis::class);
        $servers = $redis->sMembers(RedisKey::SERVER_SET);

        $buckets = $this->hash->generate($servers);

        $redis->set(RedisKey::SERVER_CONSISTENT_HASH, json_encode($buckets));

    }

}