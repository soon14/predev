<?php

// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------
function JpushClassLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__.'/'.$path.'.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_unregister(array(
        'vmc',
        'autoload',
    ));
spl_autoload_register('JpushClassLoader');
use JPush\Client as JPush;
spl_autoload_register(array(
        'vmc',
        'autoload',
    ));
class jpush_stage
{
    public function __construct($app)
    {
        $app_key = $app->getConf('appkey');
        $master_secret = $app->getConf('masterscrect');

        $this->client = new JPush($app_key, $master_secret);
    }
    /**
     * @params plantform ios\android\all
     * @params title string
     * @params content string
     * @params send_time xxxx-xx-xx xx:xx:xx
     * @params extras array('event_type'=>'push','event_params'=>array('style'=>'style01','present':'NO','url':'http://xxx'))
     */
    public function create_task($params, &$err_msg)
    {
        if (!empty($params['send_time'])) {
            $params['send_time'] = strtotime($params['send_time']);
        }
        switch ($params['platform']) {
            case 'ios':
                $platform = array('ios');
                break;
            case 'android':
                $platform = array('android');
                break;
            default:
                $platform = array('ios','android');
                break;
        }
        try {
            $task = $this->client->push()
            ->setPlatform($platform)
            ->addAllAudience()
            ->setNotificationAlert($params['content'])
            ->iosNotification($params['content'], array(
            'sound'=>'default',
            'extras' => $params['extras'],
            ))
            ->androidNotification($params['content'], array(
            'title' => $params['title'],
            'extras' => $params['extras'],
            ))
            ->message($params['content'], array(
                'title' => $params['title'],
                'extras' => $params['extras'],
            ));
            if (empty($params['send_time']) || $params['send_time'] < time()) {
                $_return = $task->send(); //直接发送
            } else {
                $task_build = $task->build();
                $schedule = $this->client->schedule();
                $_return = $schedule->createSingleSchedule($params['task_mark'], $task_build, array('time' => date('Y-m-d H:i:s', $params['send_time']))); //定时发送
            }
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
            logger::error('Jpush report error:'.$err_msg);

            return false;
        }

        return $_return;
    }

    public function report($msg_id, &$err_msg)
    {
        try {
            $report = $this->client->report()->getReceived($msg_id);
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
            logger::error('Jpush report error:'.$err_msg);

            return false;
        }

        return $report;
    }
}
