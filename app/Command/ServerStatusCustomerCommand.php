<?php

declare(strict_types=1);

namespace App\Command;

use App\Amqp\Consumer\DemoConsumer;
use App\Amqp\Consumer\ServerStatusConsumer;
use App\Amqp\Producer\DemoProducer;
use Hyperf\Amqp\Consumer;
use Hyperf\Amqp\Producer;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class ServerStatusCustomerCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('server:status:consumer');
    }

    public function handle()
    {

        $consumer = $this->container->get(Consumer::class);

        $serverStatusConsumer = $this->container->get(ServerStatusConsumer::class);

        $consumer->consume($serverStatusConsumer);
    }
}
