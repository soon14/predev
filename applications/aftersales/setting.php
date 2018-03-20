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



$setting = array(
    'default_assrule' => array(
        'type' => 'html',
        'desc' => '默认售后服务规则' ,
        'default'=>'<h5>服务说明</h5>
        <ol>
            <li>附件赠品，退换货时请一并退回。</li>
            <li>关于物流损：请您在收货时务必仔细验货，如发现商品外包装或商品本身外观存在异常，需当场向配送人员指出，并拒收整个包裹；如您在收货后发现外观异常，请在收货24小时内提交退换货申请。如超时未申请，将无法受理。</li>
            <li>关于商品实物与网站描述不符：我们保证所出售的商品均为正品行货，并与时下市场上同样主流新品一致。但因厂家会在没有任何提前通知的情况下更改产品包装、产地或者一些附件，所以我们无法确保您收到的货物与商城图片、产地、附件说明完全一致。</li>
            <li>如果您在使用时对商品质量表示置疑，您可出具相关书面鉴定，我们会按照国家法律规定予以处理。</li>
        </ol>
',
        'helpinfo'=>'在具体<a href="index.php?app=b2c&ctl=admin_goods_type&act=index">商品类型</a>具体类型售后服务规则。当商品没有关联类型时，在售后中心默认展示该规则'
    ) ,
    'return_item_helpinfo'=>array(
        'type'=>'textarea',
        'desc'=>'售后商品返回说明',
        'default'=>'商品返回地址将在服务单审核通过后进行消息通知，通知方式包括但不限于(短信、邮件、站内信)，您也可以在“售后处理列表”中查询申请状态。',
        'helpinfo'=>'售后商品返回方式说明.'
    ),
    'request_image_size' => array(
        'type' => 'number',
        'desc' => '售后请求附件上传限制(单位:MB)' ,
        'default'=>get_cfg_var('upload_max_filesize')?intval(get_cfg_var('upload_max_filesize')):2,
        'helpinfo'=>(get_cfg_var('upload_max_filesize') ? '<span class="text-danger">服务器当前限制'.get_cfg_var('upload_max_filesize').'</span>' : '')
    ) ,
);
