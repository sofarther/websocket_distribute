<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use App\Amqp\Producer\DemoProducer;
use App\Model\BrandWallet;
use App\Test;
use Hyperf\Framework\Bootstrap\TaskCallback;
use Hyperf\HttpServer\Annotation\AutoController;

use Hyperf\Amqp\Producer;
use Hyperf\Task\Task;
use Hyperf\Task\TaskExecutor;
use Hyperf\Utils\ApplicationContext;

/**
 * @AutoController()
 */
class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }
    public function amqp_produce()
    {
        $msg = new DemoProducer(['now' => date('H:i:s')]);
        $producer =  ApplicationContext::getContainer()->get(Producer::class);
        $res = $producer->produce($msg);

        return $res;
    }
    public function db_query()
    {

        $activity_id = $this->request->query('activity_id');
        $brand_id = $this->request->query('brand_id');
        var_dump($activity_id, $brand_id);

        $query = BrandWallet::where(['activity_id' => $activity_id,'brand_id' => $brand_id]);
        go(function () use($query, $activity_id, $brand_id){
            $i = 0;
            while ($i<100){

                $info = $query->first();

                file_put_contents('a.txt',"brand_wallet_:$activity_id:$brand_id:" . $info->id .PHP_EOL, FILE_APPEND) ;
               // \Swoole\Coroutine::sleep(0.001);
                usleep(10);
                $i++;
            }

        });

        go(function (){
            $i = 0;
            while ($i<100){


                file_put_contents('a.txt', date('H:i:s') .PHP_EOL, FILE_APPEND) ;
                \Swoole\Coroutine::sleep(0.001);
                $i++;
            }

        });

    }

    public function task()
    {
        $container = ApplicationContext::getContainer();
        $exec = $container->get(TaskExecutor::class);
        $result = $exec->execute(new Task([new Test(), 'test'], ['tttt']));

    }

    public function t($d)
    {
        file_put_contents('b.txt', $d . date('H:i:s'));
    }
}
