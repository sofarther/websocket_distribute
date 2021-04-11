<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Guzzle\ClientFactory;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class DemoCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('demo:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        //$this->line(generate_server_key('248.243.54.32',454), 'info');

        $clientFactory =  $this->container->get(ClientFactory::class);
        $client = $clientFactory->create([
            'base_uri' => 'http://openresty:8899'
        ]);
        $rsp = $client->get('/lua');

        var_dump($rsp->getBody()->getContents());

    }
}
