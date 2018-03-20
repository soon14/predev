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
return array(
    1 =>
    array(
        'msg_cn' => '服务不可用',
        'msg_en' => 'missing system error',
        'solution' => '1.多数是由未知异常引起的，仔细检查传入的参数是否符合文档描述，是否缺少系统级参数？'
        . '2.将问题和请求参数发送到jos@vmcshop.com',
    ),
    2 =>
    array(
        'msg_cn' => '限制时间内调用失败次数',
        'msg_en' => 'App Call Limited',
        'solution' => '1.调整程序合理调用API，请隔日再调用？'
        . '2.请将您的appkey发送至jos@vmcshop.com，要求提升流量（请提供一下您的商家id、商品数量及日订单量，以便我们核实您的调用量）'
        . '3.您可经常登录jos.vmcshop.com，在应用管理中查看您应用的日调用次数，当持续接近调用次数时，请您及时进行处理',
    ),
    3 =>
    array(
        'msg_cn' => '请求被禁止',
        'msg_en' => 'Forbidden Request',
        'solution' => ' ',
    ),
    4 =>
    array(
        'msg_cn' => '缺少版本参数',
        'msg_en' => 'Missing Version',
        'solution' => '传入的参数加入v字段（API协议版本号）',
    ),
    5 =>
    array(
        'msg_cn' => '不支持的版本号',
        'msg_en' => 'Unsupported Version',
        'solution' => '用户传入的版本号没有被提供，即您的版本号不属于目前京东正在使用的版本号',
    ),
    6 =>
    array(
        'msg_cn' => '非法的版本参数',
        'msg_en' => 'Invalid Version',
        'solution' => '用户传入的版本号格式错误，必须为数字格式',
    ),
    7 =>
    array(
        'msg_cn' => '缺少时间戳参数',
        'msg_en' => 'Missing Timestamp',
        'solution' => '传入的参数中必须包含timestamp参数',
    ),
    8 =>
    array(
        'msg_cn' => '非法的时间戳参数',
        'msg_en' => 'Invalid Timestamp',
        'solution' => '时间戳，格式为yyyy-mm-dd hh:mm:ss，例如：2008-01-25 20:23:30。京东API服务端允许客户端请求时间误差为6分钟。',
    ),
    9 =>
    array(
        'msg_cn' => '缺少商家Id参数',
        'msg_en' => 'Missing VenderId',
        'solution' => ' ',
    ),
    10 =>
    array(
        'msg_cn' => '无效的商家Id参数',
        'msg_en' => 'Invalid VenderId',
        'solution' => ' ',
    ),
    11 =>
    array(
        'msg_cn' => '缺少签名参数',
        'msg_en' => 'Missing Signature',
        'solution' => '传入的参数中必须包含sign字段，请参考https://help.vmcshop.com/jos/question-580.html#A6',
    ),
    12 =>
    array(
        'msg_cn' => '无效签名',
        'msg_en' => 'Invalid Signature',
        'solution' => '签名必须根据正确的算法算出来的, 算法请见https://help.vmcshop.com/jos/question-580.html#A6',
    ),
    13 =>
    array(
        'msg_cn' => '无效数据格式',
        'msg_en' => 'Invalid Format',
        'solution' => ' ',
    ),
    14 =>
    array(
        'msg_cn' => '缺少方法名参数',
        'msg_en' => 'Missing Method',
        'solution' => '传入的参数中必须包含method参数,即您需要调用的接口名',
    ),
    15 =>
    array(
        'msg_cn' => '不存在的方法名',
        'msg_en' => 'Invalid Method',
        'solution' => '传入的method字段必须是你所调用的API的名称，并且该API名称是确实存在的。某些接口（如无线买家应用、CPS应用）在沙箱中未部署，因此在沙箱中会报此错误',
    ),
    16 =>
    array(
        'msg_cn' => '缺少流水号参数',
        'msg_en' => 'Missing TradeNo',
        'solution' => ' ',
    ),
    17 =>
    array(
        'msg_cn' => '流水号已经存在',
        'msg_en' => ' ',
        'solution' => ' ',
    ),
    18 =>
    array(
        'msg_cn' => '缺少access_token参数',
        'msg_en' => 'Missing access_token',
        'solution' => '传入的参数中必须包含access_token字段',
    ),
    19 =>
    array(
        'msg_cn' => '无效access_token',
        'msg_en' => 'Invalid access_token',
        'solution' => '您传入的token不正确，请确认您的token值与您使用商家账号和appkey应用获取到的token参数一致。 access_token获取方式请见： https://help.vmcshop.com/jos/question-594.html',
    ),
    20 =>
    array(
        'msg_cn' => '缺少app_key参数',
        'msg_en' => 'Missing app_key',
        'solution' => '传入的参数中必须包含app_key字段',
    ),
    21 =>
    array(
        'msg_cn' => '无效app_key',
        'msg_en' => 'Invalid app_key',
        'solution' => '所选的appkey必须是正式环境的，而非沙箱环境的appkey',
    ),
    22 =>
    array(
        'msg_cn' => '授权者不是商家',
        'msg_en' => 'the franchisor not  businessmen',
        'solution' => '部分应用类型需要商家账号，此错误说明授权时输入的账号非POP商家账号',
    ),
    23 =>
    array(
        'msg_cn' => '该API已经停用',
        'msg_en' => 'API stop',
        'solution' => '系统升级，将接口临时关闭，升级完毕后即可正常调用',
    ),
    24 =>
    array(
        'msg_cn' => '无权调用API',
        'msg_en' => 'cannot use  this API',
        'solution' => '1.通用应用？   调用商家服务中的所有接口，但需要商家授权'
        . '2.商家应用？   调用商家服务中的所有接口，但需要商家授权'
        . '3.无线买家应用？   移动手机端适用，无需授权，可调用无线API'
        . '4.网站应用？  广告网盟，无需授权，可调用CPS接口',
    ),
    25 =>
    array(
        'msg_cn' => '此应用不是上线状态',
        'msg_en' => 'this application is not on-line',
        'solution' => ' ',
    ),
    26 =>
    array(
        'msg_cn' => '缺少mobile参数',
        'msg_en' => 'Missing mobile',
        'solution' => ' ',
    ),
    27 =>
    array(
        'msg_cn' => '无效mobile',
        'msg_en' => 'Invalid mobile',
        'solution' => ' ',
    ),
    43 =>
    array(
        'msg_cn' => '系统处理错误',
        'msg_en' => 'system error',
        'solution' => ' 将问题和请求参数发送到jos@vmcshop.com',
    ),
    ' 50' =>
    array(
        'msg_cn' => '无效调用',
        'msg_en' => ' invalid get program',
        'solution' => '',
    ),
    60 =>
    array(
        'msg_cn' => '参数[%s]不合法，请参照帮助文档确认！',
        'msg_en' => 'parameter [%s] is not  valid ,please  refer to  the help document and confirm',
        'solution' => '',
    ),
    61 =>
    array(
        'msg_cn' => '参数[%s]值不合法，请参照帮助文档确认！',
        'msg_en' => 'parameter [%s] is not  valid ,please  refer to  the help document and confirm',
        'solution' => '',
    ),
    62 =>
    array(
        'msg_cn' => 'json转换时错误，错误的请求参数',
        'msg_en' => 'Missing Version',
        'solution' => '',
    ),
    63 =>
    array(
        'msg_cn' => 'json格式不合法',
        'msg_en' => 'json格式不合法',
        'solution' => '',
    ),
    64 =>
    array(
        'msg_cn' => '此类型商家无权调用本接口',
        'msg_en' => 'this type of  businesses have no right to call this interface',
        'solution' => '',
    ),
    65 =>
    array(
        'msg_cn' => '平台连接后端服务超时   ',
        'msg_en' => 'platform connecting service timeout',
        'solution' => '',
    ),
    66 =>
    array(
        'msg_cn' => '平台连接后端服务不可用',
        'msg_en' => 'platform to connect to the back-end service not available',
        'solution' => '',
    ),
    67 =>
    array(
        'msg_cn' => '平台连接后端服务处理过程中出现未知异常信息',
        'msg_en' => 'platform connecting service process unknown exception information',
        'solution' => '',
    ),
    68 =>
    array(
        'msg_cn' => '验证可选字段异常信息',
        'msg_en' => 'validation of  optional field anomaly information',
        'solution' => '',
    ),
    69 =>
    array(
        'msg_cn' => '获取数据失败',
        'msg_en' => 'failed to get data',
        'solution' => '',
    ),
    70 =>
    array(
        'msg_cn' => '该订单正在出库中',
        'msg_en' => 'missing order out  storage',
        'solution' => '',
    ),
    71 =>
    array(
        'msg_cn' => '当前的ID不属于此商家',
        'msg_en' => 'Id is not belong to  the special vender',
        'solution' => '',
    ),
    72 =>
    array(
        'msg_cn' => '当前的用户不是此类型（如FBP, SOP等）的商家',
        'msg_en' => 'The current user is  not the type of businesses (FBP, SOP, etc.)',
        'solution' => '',
    ),
    73 =>
    array(
        'msg_cn' => '该api是增值api，请将您的app入住云鼎平台方可调用',
        'msg_en' => ' this api is increased in value;if you want to visit,please put your app on our Cloud platform',
        'solution' => '',
    ),
    
    1100 =>
    array(
        'msg_cn' => '参数错误',
        'msg_en' => '参数错误',
        'solution' => '',
    ),
    
    21201 =>
    array(
        'msg_cn' => '该分类下面还有子分类',
        'msg_en' => '该分类下面还有子分类',
        'solution' => '',
    ),
    21202 =>
    array(
        'msg_cn' => '该分类下面还有商品',
        'msg_en' => '该分类下面还有商品',
        'solution' => '',
    ),
);
