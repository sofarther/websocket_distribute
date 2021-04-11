<?php

declare(strict_types=1);

namespace App\Command;

use App\Amqp\Consumer\DemoConsumer;
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
class AmqpCustomerCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('amqp:consumer');
    }

    public function handle()
    {

//        go(function (){
//            $producer =  ApplicationContext::getContainer()->get(Producer::class);
//
//            while (true){
//                \Swoole\Coroutine::sleep(0.5);
//                $msg = new DemoProducer(['now' => date('H:i:s')]);
//                $producer->produce($msg);
//            }
//        });

//        go(function (){
//            while (true){
//                \Swoole\Coroutine::sleep(1);
//                echo "Now:" . date("H:i:s") .PHP_EOL;
//
//            }
//        });

        $consumer = $this->container->get(Consumer::class);

        $consumer->consume(new DemoConsumer);
    }
}
