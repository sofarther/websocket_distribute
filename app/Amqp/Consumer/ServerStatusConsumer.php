<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use App\Constants\RedisKey;
use App\Message\ServerStatusMessage;
use App\Service\DistributeService;
use App\Unity\Http;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Redis\Redis;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Container\ContainerInterface;

/**
 * (exchange="hyperf", routingKey="hyperf", queue="hyperf", name ="DemoConsumer", nums=1)
 */
class ServerStatusConsumer extends ConsumerMessage
{
    protected $exchange = 'hyperf';
    protected $routingKey = 'server-status';
    protected $queue="server-status";

    public function __construct(ContainerInterface $container)
    {

        $this->container = $container;
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {

       if(!$data){
           return Result::ACK;
       }

       $redis = $this->container->get(Redis::class);

       $key = generate_server_key($data['host'], $data['port']);

       //不支持 使用 pipeline() , sIsMember() 不会进行执行，无法判断
      // $redis->pipeline();

       $redis->set(RedisKey::SERVER_HEARTBEAT_TIME . $key, $data['timestamp']);
       $redis->set(RedisKey::SERVER_LOST_HEARTBEAT_COUNT.$key, 0);

       $isMember = $redis->sIsMember(RedisKey::SERVER_SET, $key);

       if(!$isMember){
           $redis->sAdd(RedisKey::SERVER_SET, $key);
           $distributeService = $this->container->get(DistributeService::class);
           $distributeService->updateServer();

           // 通知 添加 服务节点
           $http = $this->container->get(Http::class);
           $http->sendServerModifyMessage(new ServerStatusMessage(ServerStatusMessage::EVENT_ADD, [$key]));
       }

      // $redis->exec();

       return Result::ACK;
    }
}
