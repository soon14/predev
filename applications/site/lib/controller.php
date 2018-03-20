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


class site_controller extends base_controller
{
    /*
     * @var string $__theme
     * @access private
    */
    private $__theme = null;
    /*
     * @var string $__tmpl
     * @access private
    */
    private $__tmpl = null;
    /*
     * @var string $__tmpl_file
     * @access private
    */
    private $__tmpl_file = null;
    /*
     * @var string $__tmpl_main_app_id
     * @access private
    */
    private $__tmpl_main_app_id = null;
    /*
     * @var array $__widgets_css
     * @access private
    */
    private $__widgets_css = array();
    /*
     * @var array $__finish_modifier
     * @access private
    */
    private $__finish_modifier = array();
    /*
     * @var array $__cachecontrol
     * @access private
    */
    private $__cachecontrol = array(
        'access' => 'public',
        'no-cache' => '',
        'no-store' => '',
        'max-age' => 'max-age=1',
    );
    /*
     * @var string $transaction_start
     * @access public
    */
    public $transaction_start = false;
    /*
     * @var string $contentType
     * @access public
    */
    public $contentType = 'text/html;charset=utf-8';
    /*
     * @var string $enable_strip_whitespace
     * @access public
    */
    public $enable_strip_whitespace = true;
    /*
     * @var string $is_splash
     * @access public
    */
    public $is_splash = false;
    /*
     * 构造
     * @var object $app
     * @access public
     * @return void
    */
    public function __construct(&$app)
    {
        parent::__construct($app);
        if (@constant('WITHOUT_STRIP_HTML')) {
            $this->enable_strip_whitespace = false;
        }
        $this->app = $app;
        $this->_request = vmc::singleton('base_component_request');
        $this->_response = vmc::singleton('base_component_response');

        foreach (vmc::servicelist('site.controller.construct') as $object) {
            if (method_exists($object, 'exec')) {
                $object->exec($this->_request);
            }
        }
    } //End Function
    /*
     * 构成链接
     * @var array $params
     * @access public
     * @return string
    */
    final public function gen_url($params = array())
    {
        return app::get('site')->router()->gen_url($params);
    } //End Function
    /*
     * 设置主题模版类型
     * @var string $tmpl
     * @access public
     * @return void
    */
    final public function set_tmpl($tmpl)
    {
        $this->__tmpl = $tmpl;
    } //End Function
    /*
     * 读取主题模版类型
     * @access public
     * @return string
    */
    final public function get_tmpl()
    {
        return $this->__tmpl ? $this->__tmpl : 'default';
    } //End Function
    /*
     * 设置主题模版文件
     * @var string $tmpl
     * @access public
     * @return void
    */
    final public function set_tmpl_file($tmpl_file)
    {
        $this->__tmpl_file = $tmpl_file;
    } //End Function
    /*
     * 读取主题模版文件
     * @access public
     * @return string
    */
    final public function get_tmpl_file()
    {
        return $this->__tmpl_file;
    } //End Function
    /*
     * 设置主题
     * @var string $theme
     * @access public
     * @return void
    */
    final public function set_theme($theme)
    {
        $this->__theme = $theme;
    } //End Function
    /*
     * 读取主题
     * @access public
     * @return string
    */
    final public function get_theme()
    {
        return $this->__theme;
    } //End Function
    /*
     * 主题模板嵌套
     * @var string $tmpl
     * @access public
     * @return string
    */
    protected function _fetch_tmpl_compile_require($tmpl_file, $is_preview = false)
    {
        $html = $this->fetch_tmpl($tmpl_file, $is_preview);

        return $html;
    } //End Function
    private function fix_statics_dir($code)
    {
        $from = array(
            '/((?:background|src|href)\s*=\s*["|\'])(?:\.\/|\.\.\/)?(statics\/.*?["|\'])/is',
            '/((?:background|background-image):\s*?url\()(?:\.\/|\.\.\/)?(statics\/)/is',
            '/<!--[^<|>|{|\n]*?-->/',
        );
        $theme_url = vmc::get_themes_host_url();
        $to = array(
            '\1<?php echo \$theme_url, "/", \$this->get_theme(), "/";?>\2',
            '\1<?php echo \$theme_url, "/", \$this->get_theme(), "/";?>\2',
            '',
        );

        return preg_replace($from, $to, $code);
    } //End Function
    /*
     * 设置模块main区域app_id
     * @var string $app_id
     * @access public
     * @return void
    */
    final public function set_tmpl_main_app_id($app_id)
    {
        $this->__tmpl_main_app_id = $app_id;
    } //End Function
    /*
     * 读取模块main区域app_id
     * @access public
     * @return string
    */
    final public function get_tmpl_main_app_id()
    {
        return $this->__tmpl_main_app_id;
    } //End Function
    public function display($tmpl_file, $app_id = null, $fetch = false, $is_theme = false,$is_m_theme = false){
        if ($fetch === true) {
            return parent::display($tmpl_file,$app_id,$fetch,$is_theme,$is_m_theme);
        }
        $content = parent::display($tmpl_file,$app_id,true,$is_theme,$is_m_theme);
        $this->_response->set_body($content);//为了缓存
    }
    /*
     * 显示模板
     * @var string $tmpl
     * @access public
     * @return void
    */
    final public function display_tmpl($tmpl, $fetch = false, $is_preview = false)
    {
        vmc::singleton('site_theme_install')->monitor_change($this->get_theme());
        array_unshift($this->_files, $this->get_theme().'/'.$tmpl);
        //title description
        $title = $this->title ?
        $this->title :
        app::get('site')->getConf('site_name', app::get('site')->getConf('page_default_title'));
        $keywords = $this->keywords ?
        $this->keywords :
        app::get('site')->getConf('page_default_keywords', $title);
        $description = $this->description ?
        $this->description :
        app::get('site')->getConf('page_default_description', $title);
        $this->pagedata = array_merge($this->pagedata, array(
            'title' => htmlspecialchars($title),
            'keywords' => htmlspecialchars($keywords),
            'description' => htmlspecialchars($description),
        ));
        $this->_vars = $this->pagedata;
        $this->_vars['base_url'] = vmc::base_url(true);
        $this->_vars['site_theme_url'] = vmc::get_themes_host_url().'/'.$this->get_theme();

        $tmpl_file = realpath(vmc::get_themes_root_dir().'/'.$this->get_theme().'/'.$tmpl);
        if (!$tmpl_file) {
            $tmpl_file = realpath(vmc::get_themes_root_dir().'/'.$this->get_theme().'/default.html');
            if (!$tmpl_file) {
                $unexists_path = vmc::get_themes_root_dir().'/'.$this->get_theme().'/'.$tmpl;
                setcookie('CURRENT_THEME', '', time() - 1000, '/');
                unset($_COOKIE['CURRENT_THEME']);
                setcookie('CURRENT_THEME_M', '', time() - 1000, '/');
                unset($_COOKIE['CURRENT_THEME_M']);
                setcookie('THEME_DIR', '', time() - 1000, '/');
                unset($_COOKIE['THEME_DIR']);
                setcookie('THEME_M_DIR', '', time() - 1000, '/');
                unset($_COOKIE['THEME_M_DIR']);
                trigger_error('File not exists ['.$unexists_path.']', E_USER_ERROR);
            }
        }
        $tmpl_content = file_get_contents($tmpl_file);
        $compile_code = $this->_compiler()->compile($tmpl_content);
        if ($compile_code !== false) {
            $compile_code = $this->fix_statics_dir($compile_code);
        }
        $theme_url = vmc::get_themes_host_url();
        ob_start();
        eval('?>'.$compile_code);
        $content = ob_get_contents();
        ob_end_clean();
        array_shift($this->_files);
        $this->pre_display($content);
        if ($fetch === true) {
            return $content;
        } else {
            echo $content;
        }
    } //End Function

    public function pre_display(&$content){
        parent::pre_display($content);

        if($this->_ignore_pre_display === false){
            foreach(vmc::serviceList('site_render_pre_display') AS $service){
                if(method_exists($service, 'pre_display')){
                    $service->pre_display($content);
                }
            }
        }
    }
    /*
     * 取出模板结果
     * @var string $tmpl
     * @access public
     * @return string
    */
    final public function fetch_tmpl($tmpl, $is_preview = false)
    {
        return $this->display_tmpl($tmpl, true, $is_preview);
    } //End Function
    /*
     * page调用 view
     * @var string $view
     * @var boolean $no_theme
     * @var string $app_id
     * @access public
     * @return string
    */
    final public function page($view, $no_theme = false, $app_id = null)
    {
        $current_theme = vmc::singleton('site_theme_base')->get_default();
        $views = vmc::singleton('site_theme_base')->get_theme_views($current_theme);
        if ($no_theme == false && $current_theme) {
            $this->set_theme($current_theme);
            $this->pagedata['_MAIN_'] = $view; //强制替换
            $this->pagedata['_THEME_'] = vmc::get_themes_host_url().'/'.$this->get_theme(); //模版地址
            $tmpl_type = $this->get_tmpl(); //模板文件类型
            $tmpl_file = $this->get_tmpl_file(); //指定模板文件
            //没有指定模板文件
            if (!$tmpl_file || $tmpl_file == '') {
                if ($views[$tmpl_type] && $views[$tmpl_type][0]) {
                    $tmpl_file = $views[$tmpl_type][0]['value'];//当没有指定模板文件时，找到的第一个模板文件即默认模板文件
                } else {
                    if ($tmpl_type == 'index') {
                        $tmpl_file = 'index.html';
                    } else {
                        $tmpl_file = 'default.html';
                    }
                }
            } //如果有模版，检测当前theme下是否有此模板
            $this->set_tmpl_main_app_id($app_id);
            $html = $this->fetch_tmpl($tmpl_file, $is_preview);
        } else {
            $html = $this->fetch($view, $app_id, $is_preview);
        }

        if (!$this->_response->get_header('Content-type', $header)) {
            $this->_response->set_header('Content-type', $this->contentType, true);
        } //如果没有定义Content-type，默认加text/html;charset=utf-8
        if (!$this->_response->get_header('Cache-Control', $header)) {
            $$cache_control = array();
            foreach ($this->__cachecontrol as $val) {
                $val = trim($val);
                if (empty($val)) {
                    continue;
                }
                $cache_control[] = $val;
            }
            $this->_response->set_header('Cache-Control', implode(',', $cache_control), true);
        } //如果没有定义Content-Control，使用系统配置
        $this->_response->set_body($html);
    }

    /*
     * 跳转
     * @var string $app
     * @var string $ctl
     * @var string $act
     * @var array $args
     * @var boolean $js_jump
     * @access public
     * @return void
    */
    final public function redirect($url, $js_jump = false)
    {
        if (is_array($url)) {
            $url = $this->gen_url($url);
        }
        if ($js_jump) {
            echo "<header><meta http-equiv=\"refresh\" content=\"0; url={$url}\"></header>";
            exit;
        } else {
            $this->_response->set_redirect($url)->send_headers();
        }
        exit;
    } //End Function
    /*
     * 错误处理开始
     * @var string $url
     * @var string $errAction
     * @var string $shutHandle
     * @access public
     * @return void
    */
    public function begin($url = null, $errAction = null, $shutHandle = null)
    {
        $this->_action_url = $url;
        $this->_errAction = $errAction;
        $this->_shutHandle = $shutHandle ? $shutHandle : (E_USER_ERROR | E_ERROR | E_USER_WARNING);
        set_error_handler(array(&$this,
            '_errorHandler',
        ), $this->_shutHandle);
        if ($this->transaction_start) {
            trigger_error('The transaction has been started', E_USER_ERROR);
        }
        $db = vmc::database();
        $this->transaction_status = $db->beginTransaction();
        $this->transaction_start = true;
    }

    public function end($result = true, $message = null, $redirect_url = null, $ajax = false)
    {
        if (!$this->transaction_start) {
            trigger_error('The transaction has not started yet', E_USER_ERROR);
        }
        $this->transaction_start = false;
        restore_error_handler();
        if (is_null($redirect_url)) { //如果是错误则在当前页面返回错误信息
            $redirect_url = $this->_action_url;
        }
        $db = vmc::database();
        if ($result) {
            $db->commit($this->transaction_status);
        } else {
            $db->rollback();
        }
        $this->splash($result ? 'success' : 'failed', $redirect_url, $result ? $message : ($message ? $message : '操作失败'), $ajax);
    }
    /*
     * 错误处理结束
     * @access public
     * @return void
    */
    public function end_only()
    {
        if (!$this->transaction_start) {
            trigger_error('The transaction has not started yet', E_USER_ERROR);
        }
        $this->transaction_start = false;
        restore_error_handler();
    }
    /*
     * 结果处理
     * @var string $status
     * @var string $url
     * @var string $msg
     * @var boolean $ajax
     * @var array $data
     * @access public
     * @return void
    */
    public function splash($status = 'success', $url = '', $msg = null, $ajax = false, $data = null)
    {
        $status = ($status == 'failed') ? 'error' : $status;
        $url = is_array($url) ? $this->gen_url($url) : $url;
        $params = $this->_request->get_params(true);
        if ($ajax || $this->_request->is_ajax()) {
            header('Cache-Control:no-store, no-cache, must-revalidate'); // HTTP/1.1
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // 强制查询etag
            header('Progma: no-cache');
            header('Content-Type:application/json; charset=utf-8');
            if (is_array($msg)) {
                $data = $msg;
                $msg = ($status == 'success' ? '操作成功' : '操作失败');
            }
            $result = array(
                $status => $msg,
                'redirect' => $url,
            );
            if ($data) {
                $result['data'] = $data;
            }
            echo json_encode($result);
            exit;
        }
        if ($url && !$msg) { //如果有url地址但是没有信息输出则直接跳转
            $this->redirect($url);
            exit;
        }
        $this->set_tmpl('splash');
        $this->pagedata['msg'] = $msg;
        if ($url != '') {
            $this->pagedata['redirect'] = $url;
        }
        $this->_response->set_header('Cache-Control', 'no-store, no-cache')->set_header('Content-type', $this->contentType)->send_headers();
        $this->pagedata['status'] = $status;
        $this->page('splash.html', false, 'site');
        echo implode("\n", $this->_response->get_bodys());
        exit;
    }
    /*
     * 设置超时
     * @var int $time
     * @access public
     * @return void
    */
    public function set_max_age($time)
    {
        $this->__cachecontrol['max-age'] = 'max-age='.(($time >= 0) ? intval($time) : 1);
    } //End Function
    /*
     * 设置no_cache
     * @var boolean $status
     * @access public
     * @return void
    */
    public function set_no_cache($status = true)
    {
        if ($status) {
            $this->__cachecontrol['no-cache'] = 'no-cache';
            $this->set_max_age(0);
        } else {
            $this->__cachecontrol['no-cache'] = '';
        }
    } //End Function
    /*
     * 设置no_store
     * @var boolean $status
     * @access public
     * @return void
    */
    public function set_no_store($status = true)
    {
        if ($status) {
            $this->__cachecontrol['no-store'] = 'no-store';
            $this->set_max_age(0);
        } else {
            $this->__cachecontrol['no-store'] = '';
        }
    } //End Function
    /*
     * 设置cache access
     * @var string $access
     * @access public
     * @return void
    */
    public function set_cache_access($access = 'public')
    {
        $this->__cachecontrol['access'] = ($access == 'public') ? 'public' : ((empty($access)) ? '' : 'private');
    } //End Function
} //End Class
