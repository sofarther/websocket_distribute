<?php


namespace App\Unity;


use App\Message\ServerStatusMessage;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Di\Annotation\Inject;


class Http
{
    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */
    private $clientFactory;

    /** @Inject()
     * @var \Hyperf\Contract\StdoutLoggerInterface
     */
    private $logger;


    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function sendServerModifyMessage(ServerStatusMessage $message)
    {
        $client = $this->clientFactory->create([
           'base_uri' => 'http://openresty:8899'
        ]);

        $rsp = $client->get('/lua', ['query' => $message->toArray()]);
        $res = $rsp->getBody()->getContents();

        $this->logger->debug('server_modify_rsp', [$res]);
    }
}