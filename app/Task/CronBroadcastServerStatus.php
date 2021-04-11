<?php
declare(strict_types=1);


namespace App\Task;

use App\Amqp\Producer\ServerStatusProducer;
use Hyperf\Amqp\Producer;
use Hyperf\Crontab\Annotation\Crontab;
use Psr\Container\ContainerInterface;

/**
 * @Crontab(name="CronBroadcastServerStatus", rule="*\/5 * * * * *", callback="execute", memo="定时广播服务器状态")
 */
class CronBroadcastServerStatus
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $host ;
    protected $port ;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->host = swoole_get_local_ip()['eth0'];
        $servers = config('server.servers');
        $this->port = $servers[0]['port'];
    }

    public function execute()
    {

        $producer = $this->container->get(Producer::class);

        $producer->produce(new ServerStatusProducer([
            'host' => $this->host,
            'port' => $this->port,
            'timestamp' => time(),
        ]));

    }
}