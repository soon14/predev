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


class ectools_payment_parent
{
    /**
     * 构造方法.
     *
     * @params string - app id
     */
    public function __construct($app)
    {
        $this->app = $app ? $app : app::get('ectools');
    }

    /**
     * 设置属性.
     *
     * @params string key
     * @params string value
     */
    protected function add_field($key, $value = '')
    {
        if (!$key) {
            trigger_error(app::get('ectools')->_('Key不能为空！'), E_USER_ERROR);
            exit;
        }

        $this->fields[$key] = $value;
    }

    protected function is_fields_valiad()
    {
        return true;
    }

    /**
     * 得到配置参数.
     *
     * @params string key
     * @payment api interface class name
     */
    protected function getConf($key, $pkey)
    {
        $val = app::get('ectools')->getConf($pkey);
        $val = unserialize($val);

        return $val[$key];
    }

    /**
     * 生成支付方式提交的表单的请求.
     *
     * @params null
     *
     * @return string
     */
    protected function get_html()
    {
        // 简单的form的自动提交的代码。
        header('Content-Type: text/html;charset='.$this->submit_charset);
        $strHtml = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\" lang=\"en-US\" dir=\"ltr\">
		<head>
        <title>前往支付平台</title>
		<style>
		body{font-size:12px;padding:10px;}
		</style>
		</head><body><p>正在跳转到支付平台<span id='loading'>...</span></p>";
        $strHtml .= '<form action="'.$this->submit_url.'" method="'.$this->submit_method.'" name="pay_form" id="pay_form">';

        // Generate all the hidden field.
        foreach ($this->fields as $key => $value) {
            $strHtml .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
        }
        $strHtml .= '</form><script type="text/javascript">
						window.onload=function(){
							document.getElementById("pay_form").submit();
							var timer = 0;
							!function(){
								clearTimeout(timer);
								document.getElementById("loading").innerHTML+="...";
								timer = setTimeout(arguments.callee,1000);
							}();
						}
					</script>';
        $strHtml .= '</body></html>';

        return $strHtml;
    }
}
