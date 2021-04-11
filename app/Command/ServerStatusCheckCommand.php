<?php

declare(strict_types=1);

namespace App\Command;

use App\Amqp\Consumer\DemoConsumer;
use App\Amqp\Consumer\ServerStatusConsumer;
use App\Amqp\Producer\DemoProducer;
use App\Constants\RedisKey;
use App\Message\ServerStatusMessage;
use App\Service\DistributeService;
use App\Unity\Http;
use GuzzleHttp\Client;
use Hyperf\Amqp\Consumer;
use Hyperf\Amqp\Producer;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use Swoole\Timer;

/**
 * @Command
 */
class ServerStatusCheckCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('server:status:check');
    }

    public function handle()
    {

        $distributeService = $this->container->get(DistributeService::class);

        $redis = $this->container->get(Redis::class);
        Timer::tick(3000, function () use($redis, $distributeService){
            $server_list = $redis->sMembers(RedisKey::SERVER_SET);
            if(empty($server_list)){
                return;
            }
            $remove_server_list = [];
            $now = time();
            foreach ($server_list as $val){
                $last_time = $redis->get(RedisKey::SERVER_HEARTBEAT_TIME . $val);
                if(!$last_time || $last_time + 10 < $now){
                    $lost_count = $redis->incr(RedisKey::SERVER_LOST_HEARTBEAT_COUNT . $val);
                    if($lost_count > 3){
                        $remove_server_list[] = $val;
                        $redis->sRem(RedisKey::SERVER_SET, $val);
                    }
                }

            }

            if($remove_server_list){

                $distributeService->updateServer();
                $http = $this->container->get(Http::class);
                $http->sendServerModifyMessage(new ServerStatusMessage(ServerStatusMessage::EVENT_REM, $remove_server_list));
            }
        });
    }
}
