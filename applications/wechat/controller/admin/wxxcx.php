<?php

/**
 * 微信免登陆配置.
 */
class wechat_ctl_admin_wxxcx extends desktop_controller
{
    /*
     * @param object $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
    }//End Function

    public function errorlog()
    {
        $appid = app::get('wechat')->getConf('wxxcx_appid');
        if (!$appid) {
            $this->redirect('index.php?app=wechat&ctl=admin_wxxcx&act=setting');
        }
        $this->finder('wechat_mdl_xcxerrorlog', array(
            'title' => ('小程序错误日志'.'<a href="index.php?app=wechat&ctl=admin_wxxcx&act=setting" style="font-size:14px;" class="pull-right">当前收集小程序APPID:<b> '.$appid.'  </b><i class="fa fa-edit"></i></a>'),
            'use_buildin_recycle' => true,
            'use_buildin_set_tag' => true,
        ));
    }

    public function setting()
    {
        if ($_POST) {
            $this->begin();
            foreach ($_POST as $key => $value) {
                $this->app->setConf('wxxcx_'.$key, $value);
            }
            $this->end(true, '保存成功');
        }

        $this->page('admin/wxxcx/setting.html');
    }

    public function qrcode()
    {
        
        $this->page('admin/wxxcx/qrcode.html');
    }

    private function hex2rgb($colour)
    {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list($r, $g, $b) = array($colour[0].$colour[1], $colour[2].$colour[3], $colour[4].$colour[5]);
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0].$colour[0], $colour[1].$colour[1], $colour[2].$colour[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array('r' => $r, 'g' => $g, 'b' => $b);
    }
    public function qrdownload($show_page_url = false)
    {
        $page_url = $_POST['page'];
        switch ($_POST['page']) {
            case '/pages/index/index'://首页
                break;
            case '/pages/product/product'://商品详情
                $page_url .= '?'.http_build_query(array(
                    'product_id' => $_POST['product_id'],
                ));
                break;
            case '/pages/gallery/gallery'://商品列表
                $params = array();
                if ($_POST['cat_id']) {
                    $params['cat_id'] = $_POST['cat_id'];
                }
                if ($_POST['keyword']) {
                    $params['keyword'] = $_POST['keyword'];
                }
                $page_url .= '?'.http_build_query($params);
                break;
        }
        if ($_POST['more_options']) {
            $page_url .= (strpos($page_url, '?') ? '' : '?') + '&'.$_POST['more_options'];
        }
        if ($show_page_url) {
            echo $page_url;
            exit;
        }

        $_POST['qrcode_width'] = $_POST['qrcode_width']?$_POST['qrcode_width']:430;
        $_POST['qrcode_type'] = $_POST['qrcode_type']?$_POST['qrcode_type']:'normal';
        $_POST['xcxqrcode_rgb'] = $_POST['xcxqrcode_rgb']?$_POST['xcxqrcode_rgb']:'#000000';
        $qrcode_file = vmc::singleton('wechat_xcxstage')->get_qrcode($page_url, $_POST['qrcode_type'], $_POST['qrcode_width'], $this->hex2rgb($_POST['xcxqrcode_rgb']));
        if($_GET['preview']){
            header('Content-type: image/png');
        }else{
            header('Content-type: octet/stream');
            $filename = urlencode($page_url);
            header('Content-disposition:attachment;filename='.$filename.'.png;');
        }
        echo $qrcode_file;
        exit;
    }

    public function indexlayout()
    {
        $this->page('admin/wxxcx/indexlayout.html');
    }

    public function xcxtplmsg()
    {
        $appid = app::get('wechat')->getConf('wxxcx_appid');
        if (!$appid) {
            $this->redirect('index.php?app=wechat&ctl=admin_wxxcx&act=setting');
        }
        $this->finder('wechat_mdl_xcxtplmsg', array(
            'title' => '微信小程序模板消息'.'<a href="index.php?app=wechat&ctl=admin_wxxcx&act=setting" style="font-size:14px;" class="pull-right">当前小程序APPID:<b> '.$appid.'  </b><i class="fa fa-edit"></i></a>',
            'use_buildin_recycle' => true,
        ));
    }
}
