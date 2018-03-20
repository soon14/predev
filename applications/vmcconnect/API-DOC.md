# VMCConnect API 说明
<a name="MENU"></a>
[1.API开发规范](#API开发规范)

[2.HOOK开发规范](#HOOK开发规范)

[3.API-category-类目](#API-category-类目)

[4.API-goodattrs-商品类型](#API-goodattrs-商品类型)

[5.API-goods-商品](#API-goods-商品)

[6.API-order-订单](#API-order-订单)

[7.API-pay-支付](#API-pay-支付)

[8.API-distribution-配送](#API-distribution-配送)

[9.API-delivery-物流](#API-delivery-物流)

[10.API-areas-地址库](#API-areas-地址库)

[11.API-refundapply-退款](#API-refundapply-退款)

[12.API-system-系统](#API-system-系统)

[13.HOOK-category-分类](#HOOK-category-分类)

[14.HOOK-goods-商品](#HOOK-goods-商品)

[15.HOOK-products-产品](#HOOK-products-产品)

[16.HOOK-order-订单](#HOOK-order-订单)

[17.HOOK-biils-支付](#HOOK-biils-支付)

[18.HOOK-delivery-退送货](#HOOK-delivery-退送货)

[19.HOOK-stock-库存](#HOOK-stock-库存)
<a name="API开发规范"></a>
## 一、 API开发规范 <font size="3">[返回目录](#MENU)</font>
### 1.) 服务入口
http://dev.vmc.com/openapi/vmcconnect/json [点击访问](http://demo.vmcshop.com/openapi/vmcconnect/json)
### 2.) 调用参数 
#### 1. 系统级参数

名称 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
app_key | String | 是 | 应用的app_key
sign | String | 是 | 签名
timestamp | String | 是 | 时间戳，格式为yyyy-MM-ddHH:mm:ss，例如：2017-05-01 12:22:22。API服务端允许客户端请求时间误差为6分钟
method | String | 是 | API接口名称
tpl | String | 否 | 命令模版
format | String | 否 | 输出类型 可选 json 和 xml
v | String | 否 | 版本 暂时只支持1.0

#### 2. 应用级参数

名称 | 类型 | 是否必须 | 示例值 | 描述
------------ | ------------ | ------------ | ------------ | ------------
fields | String | 否 |  | 设置需要返回的字段列表，字段之间用","分隔。

**参数拼接规范，json方式组装**

名称 | 类型 | 描述 | 示例值
------------ | ------------ | ------------ | ------------
vmc_param_json | String | 标准json 类型,而且是按照字母先后顺序排序的 | <pre>{<br>"date_type":1,<br>"end_date":"2017-05-09",<br>"fields":"order_id, createtime, last_modified, status",<br>"order_state":1,<br>"page":1,<br>"page_size":50,<br>"sort_type":1,<br>"start_date":"2017-05-02"<br>}</pre>

<a name="API签名"></a>
#### 3. API签名
调用API时需要对请求参数进行签名，VMCConnect服务器端会验证请求参数是否合法。

**加密规则**

1.所有请求参数按照字母先后顺序排列

例如：将access_token,app_key,method,timestamp,v 排序为access_token,app_key,method,timestamp,v1

2.把所有参数名和参数值进行拼装

例如：access_tokenxxxapp_keyxxxmethodxxxxxxtimestampxxxxxxvx

3.把app_secret夹在字符串的两端

例如：appSecret+XXXX+appSecret

<font color=#ff0000>4.使用MD5进行加密，再转化成大写</font>

调用360buy.order.search，假设app_key=1 appSecret=sZ8q6tUWgC7taL0y

**① 输入参数**

<pre>
v=1.0
method=vmc.warecats.get
app_key=1
vmc_param_json=
{
	"fields":"order_id, createtime, last_modified, status",
	"start_date":"2017-05-02"
	"end_date":"2017-05-09",
	"date_type":1,
	"order_state":1,
	"page":1,
	"page_size":50,
	"sort_type":1,
}
timestamp=2017-05-01 12:00:00
</pre>

**② 把参数按照字母顺序排列**
<pre>
app_key=1
method=vmc.warecats.get
timestamp=2017-05-01 12:00:00
v=1.0
vmc_param_json=
{
	"date_type":1,
	"end_date":"2017-05-09",
	"fields":"order_id, createtime, last_modified, status",
	"order_state":1,
	"page":1,
	"page_size":50,
	"sort_type":1,
	"start_date":"2017-05-02"
}
</pre>

**③ 连接参数名与参数值，并在首尾加上appSecret**

<pre>
<font color=#ff0000>sZ8q6tUWgC7taL0y</font>app_key1methodvmc.warecats.gettimestamp2017-05-01 12:00:00v1.0vmc_param_json{"date_type":1,"end_date":"2017-05-09","fields":"order_id, createtime, last_modified, status","order_state":1,"page":1,"page_size":50,"sort_type":1,"start_date":"2017-05-02"}<font color=#ff0000>sZ8q6tUWgC7taL0y</font>
</pre>

<font color=#ff0000>**注：**</font>

*以上字符串不允许有跨行，粘贴时请特别注意，除日期和时间中间的空格之外，不允许在其它位置出现空格。*

*时间戳 timestamp 最好就填写当前时间的前几分钟（必须在6分钟之内）*

<a name="HOOK开发规范"></a>
## 二、 HOOK开发规范 <font size="3">[返回目录](#MENU)</font>
### 1.) 提交接收HOOK的URL
提交接收HOOK服务的URL，用来接收HOOK，HOOK服务将会在各类事件后以POST方式提交数据至URL
### 2.) 提交参数 
#### 1. 系统级参数

名称 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
app_key | String | 是 | 应用的app_key
sign | String | 是 | 签名
timestamp | String | 是 | 时间戳，格式为yyyy-MM-ddHH:mm:ss，例如：2017-05-01 12:22:22。API服务端允许客户端请求时间误差为6分钟
method | String | 是 | HOOK消息名称

#### 2. 消息级参数

**参数拼接规范，json方式组装**

名称 | 类型 | 描述 | 示例值
------------ | ------------ | ------------ | ------------
vmc_method_json | String | 标准json 类型,而且是按照字母先后顺序排序的 | <pre>{<br />"cat_id":1,<br />"cat_name":"test",<br />"p_order":0,<br />"parent_id":0<br />}</pre>

#### 3. HOOK签名
系统POST至URL时带签名数据，应用请自行判断有效性。

<font color=#ff0000>**加密规则 [同API签名](#API签名)**</font>

<a name="API-category-类目"></a>
## 三、 API-category-类目 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.category.write.add](#api.vmc.category.write.add) | 添加分类
[vmc.category.write.delete](#api.vmc.category.write.delete) | 删除分类
[vmc.category.write.update](#api.vmc.category.write.update) | 更新分类
[vmc.category.read.getAll](#api.vmc.category.read.getAll) | 获取所有类目信息
[vmc.category.read.getFront](#api.vmc.category.read.getFront) | 获取前台展示的分类
[vmc.category.read.findById](#api.vmc.category.read.findById) | 获取单个类目信息
[vmc.category.read.findByPId](#api.vmc.category.read.findByPId) | 查找子类目列表




<a name="api.vmc.category.write.add"></a>
**1.) vmc.category.write.add 添加分类**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
category_pid| int | 是 | 所属分类
category_name |  String | 是 | 分类名称
category_order |  int | 否 | 排序


>
	返回结果
字段 | 描述
------------ | -------------
category_id | 新分类编号
create_time | 创建时间



<a name="api.vmc.category.write.delete"></a>
**2.) vmc.category.write.delete 删除分类**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
category_id | int | 是 | 要删除分类的编号

>
	返回结果
字段 | 描述
------------ | -------------
category_id | 已删除分类的编号
modified | 删除时间



<a name="api.vmc.category.write.update"></a>
**3.) vmc.category.write.update 更新分类**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
category_id | int | 是 | 要编辑分类编号
category_pid | int | 是 | 所属分类
category_name | String | 是 | 分类名称
category_order | int | 是 | 排序

>
	返回结果
字段 | 描述
------------ | -------------
category_id | 分类编号
modified | 修改时间



<a name="api.vmc.category.read.getAll"></a>
**4.) vmc.category.read.getAll 获取所有类目信息**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
page | int | 是 | 所在分页
page_size | int | 是 | 每页显示数量

>
	返回结果
字段 | 描述
------------ | -------------
category_id | 分类编号
category_pid | 所属分类
category_path | 分类路径
category_name | 分类名称
category_order | 排序
visible | 前台是否可见
create_time | 创建时间



<a name="api.vmc.category.read.getFront"></a>
**5.) vmc.category.read.getFront 获取前台展示的分类**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
page | int | 是 | 所在分页
page_size | int | 是 | 每页显示数量

>
	返回结果
字段 | 描述
------------ | -------------
category_id | 分类编号
category_pid | 所属分类
category_path | 分类路径
category_name | 分类名称
category_order | 排序
visible | 前台是否可见
create_time | 创建时间



<a name="api.vmc.category.read.findById"></a>
**6.) vmc.category.read.findById 获取单个类目信息**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
category_id | int | 是 | 分类编号

>
	返回结果
字段 | 描述
------------ | -------------
category_id | 分类编号
category_pid | 所属分类
category_path | 分类路径
category_name | 分类名称
category_order | 排序
visible | 前台是否可见
create_time | 创建时间



<a name="api.vmc.category.read.findByPId"></a>
**7.) vmc.category.read.findByPId 查找子类目列表**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
category_pid | int | 是 | 所属分类
page | int | 是 | 所在分页
page_size | int | 是 | 每页显示数量

>
	返回结果
字段 | 描述
------------ | -------------
category_id | 分类编号
category_pid | 所属分类
category_path | 分类路径
category_name | 分类名称
category_order | 排序
visible | 前台是否可见
create_time | 创建时间



<a name="API-goodattrs-商品类型"></a>
## 四、 API-goodattrs-商品类型 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.goodattrs.read.get](#api.vmc.goodattrs.read.get) | 获取商品类型列表
[vmc.goodattrs.read.valuesByAttrId](#api.vmc.goodattrs.read.valuesByAttrId) | 获取商品类型属性




<a name="api.vmc.goodattrs.read.get"></a>
**1.) vmc.goodattrs.read.get 获取商品类型列表**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
page | int | 是 | 所在分页
page_size | int | 是 | 每页显示数量

>
	返回结果
字段 | 描述
------------ | -------------
type_id | 类型序号
type_name | 类型名称
type_params | 参数表结构(序列化) array(参数组名=>array(参数名1=>别名1|别名2,参数名2=>别名1|别名2)
assrule | 说明



<a name="api.vmc.goodattrs.read.valuesByAttrId"></a>
**2.) vmc.goodattrs.read.valuesByAttrId 获取商品类型属性**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
type_id | int | 是 | 类型序号

>
	返回结果
字段 | 描述
------------ | -------------
type_id | 类型序号
type_name | 类型名称
type_params | 参数表结构(序列化)
assrule | 说明
props | 类型属性


<a name="API-goods-商品"></a>
## 五、 API-goods-商品 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.goods.write.add](#api.vmc.goods.write.add) | 新增商品
[vmc.goods.write.update](#api.vmc.goods.write.update) | 修改商品
[vmc.goods.write.upOrDown](#api.vmc.goods.write.upOrDown) | 商品上下架
[vmc.goods.read.byId](#api.vmc.goods.read.byId) | 获取单个商品
[vmc.goods.sku.read.findSkuById](#api.vmc.goods.sku.read.findSkuById) | 获取单个SKU
[vmc.goods.sku.stock.read.find](#api.vmc.goods.sku.stock.read.find) | 获取sku库存信息
[vmc.goods.sku.stock.write.update](#api.vmc.goods.sku.stock.write.update) | 设置sku库存




<a name="api.vmc.goods.write.add"></a>
**1.) vmc.goods.write.add 新增商品**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
goods_gid | String | 否 | 商品编号
goods_name | String | 是 | 商品名称
goods_brief | String | 否 | 商品简介
category_id | int | 是 | 类型序号
ext_cat | int | 否 | 扩展分类
marketable | bool | 否 | 是否上架
description | String | 否 | 详细介绍
brand | int | 否 | 品牌
bn | String | 是 | 货号 - 无多规格：为当前商品的值，有多规格：当规格未设置此值时使用 
barcode | String | 否 | 条码 (同上)
price | decimal | 否 | 销售价 (同上)
mktprice | decimal | 否 | 市场价 (同上)
weight | decimal | 否 | 重量 (同上)
unit | String | 否 | 单位 (同上)
spec | String | 否 | 规格 (同上)
images | String | 否 | 图片
default_img | String | 否 | 默认图片
seo_title | String | 否 | seo标题
seo_keywords | String | 否 | seo关键词
seo_description | String | 否 | seo简介
keywords | String | 否 | 产品关键词，多个用逗号“,”分割，或者使用数组
nostore_sell | bool | 否 | 是否忽略库存销售
gain_score | int | 否 | 积分
type | int | 否 | 产品类型
type_props | Array | 否 | 商品类型 扩展属性
type_params | Array | 否 | 商品类型 商品参数表
goods_type | String | 否 | 销售类型 (normal:普通商品,bind:捆绑商品,gift:赠品)
specs | Array | 否 | 规格列表
products | Array | 否 | 产品列表


<a name="api.vmc.goods.write.add.params"></a>
** specs 规格列表 **

Key为规格名称，值为规格具体数据

**例**

<pre>
array (
  '颜色' => 
  array (
    0 => '黑色',
    1 => '白色',
  ),
  '尺码' => 
  array (
    0 => 'S',
    1 => 'M',
  ),
)
</pre>

** products 产品列表 **

字段 | 描述
------------ | -------------
spec | 规格具体值
bn | 货号sku_id
barcode | 条码
price | 销售价格
mktprice | 市场价
weight | 单位重量
unit | 单位
marketable | 是否上架销售
name | 产品名称
is_default | 默认产品

**例**
<pre>
array (
  0 => 
  array (
    'spec' => 
    array (
      0 => '黑色',
      1 => 'S',
    ),
    'image' => '5911bbb3Na397d8eb',
    'bn' => 'test-bn-01',
    'barcode' => 'barcode-001',
    'price' => 1100,
    'mktprice' => 1200,
    'weight' => 1300,
    'unit' => '件',
    'marketable' => true,
    'is_default' => true,
  ),
  1 => 
  array (
    'spec' => 
    array (
      0 => '白色',
      1 => 'S',
    ),
    'image' => '5911bbbdNca037b83',
    'bn' => 'test-bn-02',
    'barcode' => 'barcode-001',
    'price' => 1100,
    'mktprice' => 1200,
    'weight' => 1300,
    'unit' => '件',
    'marketable' => true,
    'is_default' => true,
  ),
  2 => 
  array (
    'spec' => 
    array (
      0 => '黑色',
      1 => 'M',
    ),
    'image' => '59130e4cNa6d07fe0',
    'bn' => 'test-bn-03',
    'barcode' => 'barcode-001',
    'price' => 1100,
    'mktprice' => 1200,
    'weight' => 1300,
    'unit' => '件',
    'marketable' => true,
    'is_default' => true,
  ),
  3 => 
  array (
    'spec' => 
    array (
      0 => '白色',
      1 => 'M',
    ),
    'image' => '59130edbN396f5dc4',
    'bn' => 'test-bn-04',
    'barcode' => 'barcode-001',
    'price' => 1100,
    'mktprice' => 1200,
    'weight' => 1300,
    'unit' => '件',
    'marketable' => true,
    'is_default' => true,
  ),
)
</pre>

** type_props 产品列表 **
数组，每个值代表所选的值
**例**
<pre>
array (
  0 => 1,
  1 => 5,
  2 => 22,
)
</pre>

** type_params 产品列表 **
数组，每个值代表所选的值
**例**
<pre>
array (
  'AA' => 
  array (
    'a01' => 'a-1',
    'a02' => 'a-2',
    'a03' => 'a-3',
  ),
  'BB' => 
  array (
    'b01' => 'b-1',
    'b02' => 'b-2',
    'b03' => 'b-3',
  ),
)
</pre>

** images 产品图片列表 **
键为图片名称，值为图片byte数据
**例**
<pre>
array (
  '001.jpg' => byte[],
  '001.jpg' => byte[],
  '001.jpg' => byte[],
  '001.jpg' => byte[],
)
</pre>


>
	返回结果
字段 | 描述
------------ | -------------
create_time | 创建时间
goods_id | 商品ID序号



<a name="api.vmc.goods.write.update"></a>
**2.) vmc.goods.write.update 修改商品**

>
	应用级别输入参数
[参照新增商品](#api.vmc.goods.write.add)

需要增加参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
goods_id | int | 是 | 商品ID序号


[参数说明，参见添加商品](#api.vmc.goods.write.add.params)
** products 中需要增加 products_id 参数 products_id 为产品编号 **

>
	返回结果
字段 | 描述
------------ | -------------
modified | 修改时间
goods_id | 商品ID序号



<a name="api.vmc.goods.write.upOrDown"></a>
**3.) vmc.goods.write.upOrDown 商品上下架**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
goods_id | int | 是 | 商品ID序号
op_type | int | 是 | 0：下架, 1：上架

>
	返回结果
字段 | 描述
------------ | -------------
modified | 修改时间
goods_id | 商品ID序号



<a name="api.vmc.goods.read.byId"></a>
**4.) vmc.goods.read.byId 获取单个商品**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
goods_id | int | 是 | 商品ID序号

>
	返回结果
字段 | 描述
------------ | -------------
goods_id | 商品ID序号
goods_gid | 商品编号
goods_name | 商品名称
goods_category | 商品分类信息
goods_brand | 商品品牌信息
goods_brief | 商品简介
marketable | 是否上架
description | 详细介绍
specs | 规格列表
products | 产品列表
goods_type | 产品类型
goods_type | 类型详细数据


<a name="api.vmc.goods.sku.read.findSkuById"></a>
**5.) vmc.goods.sku.read.findSkuById 获取单个SKU**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
sku_id | String | 是 | sku-id

>
	返回结果
字段 | 描述
------------ | -------------
product_id | 产品ID序号
goods_id | 商品ID序号
sku_id | sku_id
product_barcode | 条码
product_name | 产品名称
product_price | 产品售价
product_mktprice | 市场价
product_weight | 单位重量
product_unit | 单位
spec_info | 规格信息
spec_desc | 规格信息
marketable | 是否上架



<a name="api.vmc.goods.sku.stock.read.find"></a>
**6.) vmc.goods.sku.stock.read.find 获取sku库存信息**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
sku_id | String | 是 | sku-id(多个用逗号“,”分割，或传数组)

>
	返回结果
字段 | 描述
------------ | -------------
stock_id | 库存ID序号
product_name | 商品名称
sku_id | sku_id
product_barcode | 条码
quantity | 库存数量



<a name="api.vmc.goods.sku.stock.write.update"></a>
**7.) vmc.goods.sku.stock.write.update 设置sku库存**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
sku_id | String | 是 | sku-id
quantity | int | 是 | 库存数量

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间



<a name="API-order-订单"></a>
## 六、 API-order-订单 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.order.read.getbyId](#api.vmc.order.read.getbyId) | 获取单个订单
[vmc.order.read.search](#api.vmc.order.read.search) | 订单检索
[vmc.order.read.notPayOrderInfo](#api.vmc.order.read.notPayOrderInfo) | 批量查询未付款订单
[vmc.order.read.notPayOrderById](#api.vmc.order.read.notPayOrderById) | 未付款订单单条记录查询
[vmc.order.read.remarkByOrderId](#api.vmc.order.read.remarkByOrderId) | 查询商家备注
[vmc.order.write.remarkUpdate](#api.vmc.order.write.remarkUpdate) | 商家订单备注修改
[vmc.order.bill.write.pay](#api.vmc.order.bill.write.pay) | 订单付款
[vmc.order.bill.write.refund](#api.vmc.order.bill.write.refund) | 订单退款
[vmc.order.delivery.write.send](#api.vmc.order.delivery.write.send) | 订单发货
[vmc.order.delivery.write.reship](#api.vmc.order.delivery.write.reship) | 订单退货
[vmc.order.write.cancel](#api.vmc.order.write.cancel) | 订单作废
[vmc.order.write.end](#api.vmc.order.write.end) | 订单归档完成




<a name="api.vmc.order.read.getbyId"></a>
**1.) vmc.order.read.getbyId 获取单个订单**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
order_id | int | 是 | 订单编号

>
	返回结果
<a name="vmc.order.read.getbyId.fields"></a>

字段 | 描述
------------ | -------------
order_id | 订单号
createtime | 下单时间
last_modified | 最后更新时间
status | 订单状态 (active:活动订单，dead:已作废,finish:已完成)
confirm | 确认状态
pay_status | 付款状态 (0:未支付,1:已支付,2:已付款至到担保方,3:部分付款,4:部分退款,5:全额退款)
payed | 订单已支付金额
is_cod | 是否是货到付款订单
need_shipping | 是否需要运输\发货
ship_status | 发货状态 (0:未发货,1:已发货,2:部分发货,3:部分退货,4:已退货)
pay_app | 支付方式
dlytype_id | 配送方式
member_id | 会员用户名
weight | 订单商品总重量（克）
quantity | 订单包含商品数量
need_invoice | 是否要开发票
invoice_title | 发票抬头
finally_cart_amount | 商品优惠后总金额
cost_freight | 配送费用
cost_protect | 保价费
cost_payment | 支付手续费
cost_tax | 订单营业税费
currency | 订单支付货币
cur_rate | 订单支付货币汇率
memberlv_discount | 会员身份优惠金额
pmt_goods | 商品促销优惠金额
pmt_order | 订单促销优惠金额
memo | 订单创建时附言
remarks | 订单管理员备注
addon | 订单附属信息(序列化)
items | 产品明细
consignee | 收货人信息


** items - 产品明细 **

字段 | 描述
------------ | -------------
item_id | 订单明细ID
order_id | 订单ID
product_id | 货品ID
releted_product_id | 订单内相关货品ID
goods_id | 商品ID
bn | 明细商品货号SKU-ID
barcode | 条码
name | 明细商品的名称
spec_info | 商品规格描述
cost | 明细商品的成本
price | 销售价
member_lv_price | 会员价
buy_price | 成交价
amount | 成交价x数量
score | 明细商品积分
weight | 明细商品重量
nums | 明细商品购买数量
sendnum | 明细商品发货数量
addon | 明细商品的规格属性
item_type | 明细商品类型 (product:商品,pkg:捆绑商品,gift:赠品商品,adjunct:配件商品)


** consignee - 收货人信息 **

字段 | 描述
------------ | -------------
name | 姓名
area | 地区
addr | 地址
zip | 邮编
tel | 电话
email | 邮箱
mobile | 手机



<a name="api.vmc.order.read.search"></a>
**2.) vmc.order.read.search 订单检索**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
start_date | int | 是 | 开始日期
end_date | int | 是 | 结束日期
page | int | 是 | 所在分页
page_size | int | 是 | 每页显示数量
order_state | int | 是 | 订单状态 (1:待付款,2:待发货,3:已发货,9:已完成,99:已作废)
sort_type | int | 是 | 排序方式 (0:降序,1:升序)
date_type | int | 是 | 日期方式 (0:订单创建时间查询,1:按修改时间查询)

>
	返回结果
[参照订单返回结果信息](#vmc.order.read.getbyId.fields)



<a name="api.vmc.order.read.notPayOrderInfo"></a>
**3.) vmc.order.read.notPayOrderInfo 批量查询未付款订单**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
start_date | date | 否 | 开始日期
end_date | date | 否 | 结束日期
page | int | 否 | 所在分页
page_size | int | 否 | 每页显示数量

>
	返回结果
[参照订单返回结果信息](#vmc.order.read.getbyId.fields)



<a name="api.vmc.order.read.notPayOrderById"></a>
**4.) vmc.order.read.notPayOrderById 未付款订单单条记录查询**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
order_id | int | 是 | 订单ID序号

>
	返回结果
[参照订单返回结果信息](#vmc.order.read.getbyId.fields)



<a name="api.vmc.order.read.remarkByOrderId"></a>
**5.) vmc.order.read.remarkByOrderId 查询商家备注**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号

>
	返回结果
字段 | 描述
------------ | -------------
remarks | 商家备注



<a name="api.vmc.order.write.remarkUpdate"></a>
**6.) vmc.order.write.remarkUpdate 商家订单备注修改**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号
remarks | String | 是 | 备注内容

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
order_id | 订单ID序号



<a name="api.vmc.order.bill.write.pay"></a>
**7.) vmc.order.bill.write.pay 订单付款**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号
pay_money | decima | 是 | 支付货币金额
pay_mode | String | 是 | 付款方式 (online:在线支付,offline:线下支付,deposit:预存款支付)
pay_app_id | String | 是 | 支付应用程序
payee_account | String | 是 | 收款者账户

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
order_id | 订单ID序号



<a name="api.vmc.order.bill.write.refund"></a>
**8.) vmc.order.bill.write.refund 订单退款**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号
pay_mode | String | 是 | 付款方式 (online:在线支付,offline:线下支付,deposit:预存款支付)
pay_app_id | String | 是 | 支付应用程序
payee_account | String | 是 | 收款者账户
payee_bank | String | 是 | 收款者银行
out_trade_no | String | 是 | 支付平台流水号
pay_fee | int | 是 | 支付手续费
payer_bank | String | 是 | 付款者银行
payer_account | String | 是 | 付款者账户
memo | String | 是 | 备注

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
order_id | 订单ID序号



<a name="api.vmc.order.delivery.write.send"></a>
**9.) vmc.order.delivery.write.send 订单发货**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号
dlycorp_id | int | 是 | 物流公司 
dlyplace_id | int | 是 | 发货地点
send_router | String | 是 | 发货类型 (selfwarehouse:自营仓发货',cbeselfwarehouse:跨境自营仓发货,tpwarehouse:第三方仓发货,cbetpwarehouse:跨境第三方仓发货,pos:门店收银台发货,storedelivery:门店自配送,warehouse2store:自营仓发门店,return:客户退回)
logistics_no | String | 是 | 物流单号
memo | String | 是 | 备注
send | Array | 是 | 货品列表 array( 9=>3); key为ITEM-ID 值为发货数量

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
order_id | 订单ID序号
logistics_no | 发货单单号
delivery_id | 货单流水号
delivery_type | 业务类型
send_router | 发货类型
consignor | 发货人信息
consignee | 收货人信息
status | 发货单状态



<a name="api.vmc.order.delivery.write.reship"></a>
**10.) vmc.order.delivery.write.reship 订单退货**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号
dlycorp_id | int | 是 | 物流公司 
dlyplace_id | int | 是 | 退货地点
send_router | String | 是 | 发货类型 (selfwarehouse:自营仓发货',cbeselfwarehouse:跨境自营仓发货,tpwarehouse:第三方仓发货,cbetpwarehouse:跨境第三方仓发货,pos:门店收银台发货,storedelivery:门店自配送,warehouse2store:自营仓发门店,return:客户退回)
logistics_no | String | 是 | 物流单号
cost_freight | decima | 是 | 物流费用
memo | String | 是 | 备注
send | Array | 是 | 货品列表 array( 9=>3); key为ITEM-ID 值为发货数量

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
order_id | 订单ID序号
logistics_no | 发货单单号
delivery_id | 货单流水号
delivery_type | 业务类型
send_router | 发货类型
consignor | 发货人信息
consignee | 收货人信息
status | 退货单状态
confirm | 退货单是否已确认



<a name="api.vmc.order.write.cancel"></a>
**11.) vmc.order.write.cancel 订单作废**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
order_id | 订单ID序号



<a name="api.vmc.order.write.end"></a>
**12.) vmc.order.write.end 订单归档完成**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
order_id | int | 是 | 订单ID序号

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
order_id | 订单ID序号




<a name="API-pay-支付"></a>
## 七、 API-pay-支付 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.pay.read.get](#api.vmc.pay.read.get) | 查询支付方式




<a name="api.vmc.pay.read.get"></a>
**1.) vmc.pay.read.get 查询支付方式**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段

>
	返回结果
字段 | 描述
------------ | -------------
name | 支付应用名称
version | 应用版本
platform_allow | 支持平台
app_id | 支付方式ID号
display_name | 支付方式名称
order_num | 排序
app_class | ectools支付方式方法名
description | 描述
status | 是否开启
pay_fee | 支付费用




<a name="API-distribution-配送"></a>
## 八、 API-distribution-配送 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.distribution.read.get](#api.vmc.distribution.read.get) | 查询配送方式




<a name="api.vmc.distribution.read.get"></a>
**1.) vmc.distribution.read.get 查询配送方式**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段

>
	返回结果
字段 | 描述
------------ | -------------
real_type | 配送名称
type | 配送方式




<a name="API-delivery-配送"></a>
## 九、 API-delivery-配送 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.delivery.write.add](#api.vmc.delivery.write.add) | 添加物流公司
[vmc.delivery.write.edit](#api.vmc.delivery.write.edit) | 编辑物流公司
[vmc.delivery.write.delete](#api.vmc.delivery.write.delete) | 删除物流公司
[vmc.delivery.read.get](#api.vmc.delivery.read.get) | 获取物流公司




<a name="api.vmc.delivery.write.add"></a>
**1.) vmc.delivery.write.add 添加物流公司**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
corp_name | int | 是 | 物流公司名称 
corp_code | String | 是 | 物流公司代码
website | String | 否 | 物流公司官网
request_url | String | 否 | 包裹查询网址

>
	返回结果
字段 | 描述
------------ | -------------
create_time | 创建时间
order_id | 物流公司ID



<a name="api.vmc.delivery.write.edit"></a>
**2.) vmc.delivery.write.edit 编辑物流公司**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
corp_id | int | 是 | 物流公司ID
corp_name | String | 是 | 物流公司名称 
corp_code | String | 是 | 物流公司代码
website | String | 否 | 物流公司官网
request_url | String | 否 | 包裹查询网址

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
corp_id | 物流公司ID



<a name="api.vmc.delivery.write.delete"></a>
**3.) vmc.delivery.write.delete 删除物流公司**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
corp_id | int | 是 | 物流公司ID

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
corp_id | 物流公司ID



<a name="api.vmc.delivery.read.get"></a>
**4.) vmc.delivery.read.get 获取物流公司**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段

>
	返回结果
字段 | 描述
------------ | -------------
corp_id | 物流公司ID
corp_name | 物流公司名称 
corp_code | 物流公司代码
website | 物流公司官网
request_url | 包裹查询网址



<a name="API-areas-地址库"></a>
## 十、 API-areas-地址库 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.areas.read.province.get](#api.vmc.areas.read.province.get) | 获取省级地址列表
[vmc.areas.read.city.get](#api.vmc.areas.read.city.get) | 获取市级信息列表
[vmc.areas.read.county.get](#api.vmc.areas.read.county.get) | 获取区县级信息列表
[vmc.areas.read.town.get](#api.vmc.areas.read.town.get) | 获取乡镇级信息列表


<a name="api.vmc.areas.read.province.get"></a>
**1.) vmc.areas.read.province.get 获取省级地址列表***

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段

<a name="api.vmc.areas.read.province.get.fields"></a>
>
	返回结果
字段 | 描述
------------ | -------------
area_id | 区域序号
area_name | 地区名称 
area_package | 地区包的类别, 中国/外国等. 中国大陆的编号目前为mainland
area_pid | 上一级地区的序号
area_path | 序号层级排列结构



<a name="api.vmc.areas.read.city.get"></a>
**2.) vmc.areas.read.city.get 获取市级信息列表***

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
area_pid | int | 是 | 上一级地区的序号

>
	返回结果
[参照获取省级地址列表返回结果](#api.vmc.areas.read.province.get.fields)



<a name="api.vmc.areas.read.county.get"></a>
**3.) vmc.areas.read.county.get 获取区县级信息列表***

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
area_pid | int | 是 | 上一级地区的序号

>
	返回结果
[参照获取省级地址列表返回结果](#api.vmc.areas.read.province.get.fields)



<a name="api.vmc.areas.read.town.get"></a>
**4.) vmc.areas.read.town.get 获取乡镇级信息列表***

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
area_pid | int | 是 | 上一级地区的序号

>
	返回结果
[参照获取省级地址列表返回结果](#api.vmc.areas.read.province.get.fields)



<a name="API-refundapply-退款"></a>
## 十一、 API-refundapply-退款 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.refundapply.read.queryPageList](#api.vmc.refundapply.read.queryPageList) | 退款审核单列表查询
[vmc.refundapply.read.queryById](#api.vmc.refundapply.read.queryById) | 根据Id查询退款审核单
[vmc.refundapply.read.getWaitRefundNum](#api.vmc.refundapply.read.getWaitRefundNum) | 待处理退款单数查询
[vmc.refundapply.write.replyRefund](#api.vmc.refundapply.write.replyRefund) | 审核退款单




<a name="api.vmc.refundapply.read.queryPageList"></a>
**1.) vmc.refundapply.read.queryPageList 退款审核单列表查询**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
ids | String | 否 | ID,多个用,分割
status | int | 否 | 上一级地区的序号 (0:全部,1:准备中,9:支付成功,99:作废)
order_id | int | 否 | 相关订单
member_id | int | 否 | 相关会员
create_start_date | date | 否 | 创建时间开始
create_end_date | date | 否 | 创建时间结束
modify_start_date | date | 否 | 更新时间开始
modify_end_date | date | 否 | 更新时间结束
page | int | 否 | 所在分页
page_size | int | 否 | 每页显示数量

<a name="vmc.refundapply.read.queryPageList.fields"></a>
>
	返回结果
字段 | 描述
------------ | -------------
bill_id | 账单流水号
money | 支付货币金额 
currency | 货币 
cur_rate | 货币汇率
member_id | 相关会员
order_id | 相关订单
pay_mode | 付款方式 (online:在线支付,offline:线下支付,deposit:预存款支付)
payee_account | 收款者账户 
payee_bank | 收款者银行
payer_account | 付款者账户
payer_bank | 付款者银行
pay_app_id | 支付应用程序
pay_fee | 支付手续费
out_trade_no | 支付平台流水号
memo | 备注
createtime | 支付单创建时间
last_modify | 最后更新时间



<a name="api.vmc.refundapply.read.queryById"></a>
**2.) vmc.refundapply.read.queryById 根据Id查询退款审核单**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
fields | String | 否 | 需要返回的字段
bill_id | int | 是 | 上一级地区的序号

>
	返回结果
[参照退款审核单列表查询返回结果](#vmc.refundapply.read.queryPageList.fields)



<a name="api.vmc.refundapply.read.getWaitRefundNum"></a>
**3.) vmc.refundapply.read.getWaitRefundNum 待处理退款单数查询**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

>
	返回结果
字段 | 描述
------------ | -------------
total | 数量



<a name="api.vmc.refundapply.write.replyRefund"></a>
**4.) vmc.refundapply.write.replyRefund 审核退款单**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
bill_id | String | 是 | 账单流水号
order_id | int | 否 | 相关订单
pay_app_id | String | 是 | 支付应用程序
payer_account | String | 是 | 付款者账户
payer_bank | String | 是 | 付款者银行
out_trade_no | String | 是 | 支付平台流水号
memo | String | 否 | 备注

>
	返回结果
字段 | 描述
------------ | -------------
modified | 更新时间
bill_id | 账单流水号



<a name="API-system-系统"></a>
## 十二、 API-system-系统 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.system.ping](#api.vmc.system.ping) | PING
[vmc.system.read.setting.info](#api.vmc.system.read.setting.info) | 查询基本信息
[vmc.system.read.setting.pc](#api.vmc.system.read.setting.pc) | 查询PC版基本信息
[vmc.system.read.setting.mobile](#api.vmc.system.read.setting.mobile) | 查询手机版基本信息
[vmc.system.returnaddress.read.get](#api.vmc.system.returnaddress.read.get) | 查询退货地址列表
[vmc.system.returnaddress.read.getdef](#api.vmc.system.returnaddress.read.getdef) | 查询默认退货地址
[vmc.system.shipaddress.read.get](#api.vmc.system.shipaddress.read.get) | 查询发货地址列
[vmc.system.shipaddress.read.getdef](#api.vmc.system.shipaddress.read.getdef) | 查询默认发货地址




<a name="api.vmc.system.ping"></a>
**1.) vmc.system.ping PING**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
msg | String | 否 | 消息

>
	返回结果
字段 | 描述
------------ | -------------
msg | 如有传入msg则返回传入msg，否则返回ping



<a name="api.vmc.system.read.setting.info"></a>
**2.) vmc.system.read.setting.info 查询基本信息**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

>
	返回结果
**返回结果有说明***



<a name="api.vmc.system.read.setting.pc"></a>
**3.) vmc.system.read.setting.pc 查询PC版基本信息**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

>
	返回结果
**返回结果有说明***



<a name="api.vmc.system.read.setting.mobile"></a>
**4.) vmc.system.read.setting.mobile 查询手机版基本信息**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

>
	返回结果
**返回结果有说明***



<a name="api.vmc.system.returnaddress.read.get"></a>
**5.) vmc.system.returnaddress.read.get 查询退货地址列表**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

<a name="api.vmc.system.returnaddress.read.get.fields"></a>
>
	返回结果
字段 | 描述
------------ | -------------
dp_id | 地点ID
dp_no | 编号
dp_type | 地点类型 (warehouse:仓库,store:门店,tpwarehouse:第三方仓库)
dp_type_var | 地点类型
dp_title | 地点名称
consignor_name | 联系人
consignor_area | 所在地区
consignor_addr | 详细地址
consignor_zip | 邮编
consignor_tel | 联系电话
consignor_mobile | 联系人手机
consignor_email | 联系人Email
is_default | 是否是默认发货地
is_default_reship | 是否是默认退货地
memo | 备注



<a name="api.vmc.system.returnaddress.read.getdef"></a>
**6.) vmc.system.returnaddress.read.getdef 查询默认退货地址**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

>
	返回结果
[参照查询退货地址列表返回结果](#api.vmc.system.returnaddress.read.get.fields)



<a name="api.vmc.system.shipaddress.read.get"></a>
**7.) vmc.system.shipaddress.read.get 查询发货地址列**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

>
	返回结果
[参照查询退货地址列表返回结果](#api.vmc.system.returnaddress.read.get.fields)



<a name="api.vmc.system.shipaddress.read.getdef"></a>
**8.) vmc.system.shipaddress.read.getdef 查询默认发货地址**

>
	应用级别输入参数
字段 | 类型 | 是否必须 | 描述
------------ | ------------- | ------------- | -------------
无

>
	返回结果
[参照查询退货地址列表返回结果](#api.vmc.system.returnaddress.read.get.fields)



<a name="HOOK-category-分类"></a>
## 十四、 HOOK-category-分类 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.category.save](#hook.vmc.category.save) | 添加分类
[vmc.category.remove](#hook.vmc.category.remove) | 移除分类




<a name="hook.vmc.category.save"></a>
**1.) vmc.category.save 添加分类**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
category_id | 分类ID
category_pid | 所属分类
category_name | 分类名称
visible | 前台是否可见



<a name="hook.vmc.category.remove"></a>
**2.) vmc.category.remove 移除分类**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
category_id | 地点ID


<a name="HOOK-goods-商品"></a>
## 十四、 HOOK-goods-商品 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.goods.create](#hook.vmc.goods.create) | 创建商品
[vmc.goods.update](#hook.vmc.goods.update) | 更新商品
[vmc.goods.delete](#hook.vmc.goods.delete) | 删除商品




<a name="hook.vmc.goods.create"></a>
**1.) vmc.goods.create 创建商品**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
goods_id | 商品ID序号
goods_gid | 商品编号
goods_name | 商品名称
type_id | 产品类型
category_id | 商品分类信息
brand_id | 商品品牌信息
market_able | 是否上架销售
goods_profile | 商品简介
goods_content | 详细介绍
specs | 规格列表
profile_(1-50) | 产品类型信息


<a name="hook.vmc.goods.update"></a>
**2.) vmc.goods.update 更新商品**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
filter | 筛选条件
goods | 修改内容

** filter **

字段 | 描述
------------ | -------------
goods_id | 商品ID

** goods **

字段 | 描述
------------ | -------------
goods_id | 商品ID序号
goods_gid | 商品编号
goods_name | 商品名称
type_id | 产品类型
category_id | 商品分类信息
brand_id | 商品品牌信息
market_able | 是否上架销售
goods_profile | 商品简介
goods_content | 详细介绍
specs | 规格列表
profile_(1-50) | 产品类型信息


<a name="hook.vmc.goods.delete"></a>
**3.) vmc.goods.delete 删除商品**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
goods_id | 商品ID序号


<a name="HOOK-products-产品"></a>
## 十五、 HOOK-products-产品 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.products.create](#hook.vmc.products.create) | 创建产品
[vmc.products.update](#hook.vmc.products.update) | 更新产品
[vmc.products.delete](#hook.vmc.products.delete) | 删除产品




<a name="hook.vmc.products.create"></a>
**1.) vmc.products.create 创建产品**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
goods_id | 商品ID序号
product_id | 产品ID序号
sku_id | sku_id
product_code | 条码
product_name | 产品名称
product_price | 产品售价
mkt_price | 市场价
product_weight | 单位重量
product_unit | 单位
spec_info | 规格信息
spec_desc | 规格信息
is_default | 默认产品
marketable | 是否上架


<a name="hook.vmc.products.update"></a>
**2.) vmc.products.update 更新产品**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
filter | 筛选条件
products | 修改内容

** filter **

字段 | 描述
------------ | -------------
product_id | 产品ID

** products **

字段 | 描述
------------ | -------------
goods_id | 商品ID序号
product_id | 产品ID序号
sku_id | sku_id
product_code | 条码
product_name | 产品名称
product_price | 产品售价
mkt_price | 市场价
product_weight | 单位重量
product_unit | 单位
spec_info | 规格信息
spec_desc | 规格信息
is_default | 默认产品
marketable | 是否上架


<a name="hook.vmc.products.delete"></a>
**3.) vmc.products.delete 删除产品**

>
	应用级别输入参数
字段 | 描述
------------ | -------------
product_id | 产品ID序号


<a name="HOOK-order-订单"></a>
## 十六、 HOOK-order-订单 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.order.create](#hook.vmc.order.create) | 订单创建完成时触发
[vmc.order.cancel](#hook.vmc.order.cancel) | 订单作废完成时触发
[vmc.order.end](#hook.vmc.order.end) | 订单归档完成时触发




<a name="hook.vmc.order.create"></a>
**1.) vmc.order.create 订单创建完成时触发**

<a name="hook.vmc.order.create.fields"></a>
>
	应用级别输入参数
字段 | 描述
------------ | -------------
order_id | 订单号
createtime | 下单时间
last_modified | 最后更新时间
status | 订单状态 (active:活动订单，dead:已作废,finish:已完成)
confirm | 确认状态
pay_status | 付款状态 (0:未支付,1:已支付,2:已付款至到担保方,3:部分付款,4:部分退款,5:全额退款)
payed | 订单已支付金额
is_cod | 是否是货到付款订单
need_shipping | 是否需要运输\发货
ship_status | 发货状态 (0:未发货,1:已发货,2:部分发货,3:部分退货,4:已退货)
pay_app | 支付方式
dlytype_id | 配送方式
member_id | 会员用户名
weight | 订单商品总重量（克）
quantity | 订单包含商品数量
need_invoice | 是否要开发票
invoice_title | 发票抬头
finally_cart_amount | 商品优惠后总金额
cost_freight | 配送费用
cost_protect | 保价费
cost_payment | 支付手续费
cost_tax | 订单营业税费
currency | 订单支付货币
cur_rate | 订单支付货币汇率
memberlv_discount | 会员身份优惠金额
pmt_goods | 商品促销优惠金额
pmt_order | 订单促销优惠金额
memo | 订单创建时附言
remarks | 订单管理员备注
addon | 订单附属信息(序列化)
items | 产品明细
consignee | 收货人信息

** items - 产品明细 **

字段 | 描述
------------ | -------------
item_id | 订单明细ID
order_id | 订单ID
product_id | 货品ID
releted_product_id | 订单内相关货品ID
goods_id | 商品ID
bn | 明细商品货号SKU-ID
barcode | 条码
name | 明细商品的名称
spec_info | 商品规格描述
cost | 明细商品的成本
price | 销售价
member_lv_price | 会员价
buy_price | 成交价
amount | 成交价x数量
score | 明细商品积分
weight | 明细商品重量
nums | 明细商品购买数量
sendnum | 明细商品发货数量
addon | 明细商品的规格属性
item_type | 明细商品类型 (product:商品,pkg:捆绑商品,gift:赠品商品,adjunct:配件商品)


** consignee - 收货人信息 **

字段 | 描述
------------ | -------------
name | 姓名
area | 地区
addr | 地址
zip | 邮编
tel | 电话
email | 邮箱
mobile | 手机


<a name="hook.vmc.order.cancel"></a>
**2.) vmc.order.cancel 订单作废完成时触发**

>
	应用级别输入参数
[参照订单创建完成时触发](#hook.vmc.order.create.fields)


<a name="hook.vmc.order.end"></a>
**3.) vmc.order.end 订单归档完成时触发**

>
	应用级别输入参数
[参照订单创建完成时触发](#hook.vmc.order.create.fields)


<a name="HOOK-biils-支付"></a>
## 十七、 HOOK-biils-支付 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.biils.payment.succ](#hook.vmc.biils.payment.succ) | 订单支付完成
[vmc.biils.payment.progress](#hook.vmc.biils.payment.progress) | 订单支付到担保方完成
[vmc.biils.refund.succ](#hook.vmc.biils.refund.succ) | 订单退款完成
[vmc.biils.refund.progress](#hook.vmc.biils.refund.progress) | 订单退款到担保方完成




<a name="hook.vmc.biils.payment.succ"></a>
**1.) vmc.biils.payment.succ 订单支付完成**

<a name="hook.vmc.biils.payment.succ.fields"></a>
>
	应用级别输入参数
字段 | 描述
------------ | -------------
bill_id | 账单流水号
money | 支付货币金额 
bill_type | 单据类型 (payment:付款,refund:退款) 
pay_object | 业务对象 (order:订单,recharge:充值) 
currency | 货币 
cur_rate | 货币汇率
member_id | 相关会员
order_id | 相关订单
pay_mode | 付款方式 (online:在线支付,offline:线下支付,deposit:预存款支付)
payee_account | 收款者账户 
payee_bank | 收款者银行
payer_account | 付款者账户
payer_bank | 付款者银行
pay_app_id | 支付应用程序
pay_fee | 支付手续费
out_trade_no | 支付平台流水号
memo | 备注
createtime | 支付单创建时间
last_modify | 最后更新时间


<a name="hook.vmc.biils.payment.progress"></a>
**2.) vmc.biils.payment.progress 订单支付到担保方完成**

>
	应用级别输入参数
[参照订单支付完成](#hook.vmc.biils.payment.succ.fields)


<a name="hook.vmc.biils.refund.succ"></a>
**3.) vmc.biils.refund.succ 订单退款完成**

>
	应用级别输入参数
[参照订单支付完成](#hook.vmc.biils.payment.succ.fields)


<a name="hook.vmc.biils.refund.progress"></a>
**4.) vmc.biils.refund.progress 订单退款到担保方完成**

>
	应用级别输入参数
[参照订单支付完成](#hook.vmc.biils.payment.succ.fields)


<a name="HOOK-delivery-退送货"></a>
## 十八、 HOOK-delivery-退送货 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.delivery.send.create](#hook.vmc.delivery.send.create) | 发货单据创建完成
[vmc.delivery.send.update](#hook.vmc.delivery.send.update) | 后台发货单据更新
[vmc.delivery.send.finish](#hook.vmc.delivery.send.finish) | 发货操作完成
[vmc.delivery.reship.create](#hook.vmc.delivery.reship.create) | 退货单据创建完成
[vmc.delivery.reship.update](#hook.vmc.delivery.reship.update) | 退货单据更新
[vmc.delivery.reship.finish](#hook.vmc.delivery.reship.finish) | 退货单据操作完成




<a name="hook.vmc.delivery.send.create"></a>
**1.) vmc.delivery.send.create 发货单据创建完成**

<a name="hook.vmc.delivery.send.create.fields"></a>
>
	应用级别输入参数
字段 | 描述
------------ | -------------
delivery_id | 货单流水号
order_id | 相关订单
delivery_type | 业务类型 (send:商家发货,reship:客户退货)
member_id | 相关会员
dlycorp_id | 物流公司
dlyplace_id | 发(退)货地点
send_router | 发货类型 发货类型 (selfwarehouse:自营仓发货',cbeselfwarehouse:跨境自营仓发货,tpwarehouse:第三方仓发货,cbetpwarehouse:跨境第三方仓发货,pos:门店收银台发货,storedelivery:门店自配送,warehouse2store:自营仓发门店,return:客户退回)
logistics_no | 物流单号
cost_freight | 物流费用
status | 状态 (ready:单据成功创建,executing:执行中,succ:已被确认,cancel:已取消)
memo | 备注
createtime | 创建时间
consignor | 发货信息
consignee | 配送信息
delivery_items | 货单详细项目
memberlv_discount | 会员身份优惠总金额
pmt_goods | 商品促销优惠总金额
pmt_order | 订单促销优惠总金额
order_total | 订单应付总金额
finally_cart_amount | 商品优惠后总金额
need_invoice | 是否要开发票
invoice_title | 发票抬头
invoice_addon | 发票扩展信息

** consignor **

字段 | 描述
------------ | -------------
name | 联系人
area | 所在地区
addr | 详细地址
zip | 邮编
tel | 联系电话
mobile | 联系人手机
email | 联系人Email

** consignee **

字段 | 描述
------------ | -------------
name | 联系人
area | 所在地区
addr | 详细地址
zip | 邮编
tel | 联系电话
mobile | 联系人手机
email | 联系人Email

** delivery_items **

字段 | 描述
------------ | -------------
item_id | 序号
order_item_id | 原始订单明细项目编号
delivery_id | 发货单号
item_type | 明细项货物类型 (product:商品,pkg:捆绑商品,gift:赠品商品,adjunct:配件商品)
product_id | 货品ID
goods_id | 商品ID
bn | 明细商品货号
spec_info | 商品规格描述
weight | 重量
name | 明细商品的名称
sendnum | 实际发货数量
price | 销售价
member_lv_price | 会员价
buy_price | 成交价
amount | 明细商品总额(成交价x数量)


<a name="hook.vmc.delivery.send.update"></a>
**2.) vmc.delivery.send.update 后台发货单据更新**

>
	应用级别输入参数
[参照发货单据创建完成](#hook.vmc.delivery.send.create.fields)


<a name="hook.vmc.delivery.send.finish"></a>
**3.) vmc.delivery.send.finish 发货操作完成**

>
	应用级别输入参数
[参照发货单据创建完成](#hook.vmc.delivery.send.create.fields)


<a name="hook.vmc.delivery.reship.create"></a>
**4.) vmc.delivery.reship.create 退货单据创建完成**

>
	应用级别输入参数
[参照发货单据创建完成](#hook.vmc.delivery.send.create.fields)


<a name="hook.vmc.delivery.reship.update"></a>
**5.) vmc.delivery.reship.update 退货单据更新**

>
	应用级别输入参数
[参照发货单据创建完成](#hook.vmc.delivery.send.create.fields)


<a name="hook.vmc.delivery.reship.finish"></a>
**6.) vmc.delivery.reship.finish 退货单据操作完成**

>
	应用级别输入参数
[参照发货单据创建完成](#hook.vmc.delivery.send.create.fields)


<a name="HOOK-stock-库存"></a>
## 十九、 HOOK-stock-库存 <font size="3">[返回目录](#MENU)</font>
方法 | 类型
------------ | -------------
[vmc.stock.update](#hook.vmc.stock.update) | 更新库存




<a name="hook.vmc.stock.update"></a>
**1.) vmc.stock.update 更新库存**

>
	应用级别输入参数
** 返回数组，数组key为sku-id，值为最新库存 **