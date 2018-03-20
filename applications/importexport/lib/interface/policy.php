<?php

 interface importexport_interface_policy{

     /**
      * 连接到存储导出导入文件服务器
      *
      * @params $params array 连接服务器参数，后台配置
      * @params $msg string   连接错误返回信息
      *
      * @return bool 
      */
     public function connect($params,&$msg);

    /**
     * 检查存储服务器配置文件是否正确
     * 错误信息用 trigger_error($msg, E_USER_ERROR); 抛出即可
     *
     * @params array $params 配置信息参数
     * @return bool  成功返回true 失败则返回 false
     */
    public function check($params);

    /**
     * 将本地文件上传到存储服务器
     *
     * @params array $params 参数 array('local'=>'本地文件路径','remote'=>'远程文件路径')
     * @params string $msg 
     * @return bool
     */
     public function push($params, &$msg);

    /**
     * 将存储服务器中的文件下载到本地
     *
     * @params array $params 参数 array('local'=>'本地文件路径','remote'=>'远程文件路径','resume'=>'文件指针位置')
     * @params string $msg 
     * @return bool 
     */
    public function pull( $params, &$msg);

     /**
      * 获取传入文件在存储服务器中的大小
      *
      * @params string $filename 文件名称(无路径)
      * @return ini    文件存在则返回文件大小，文件不存在则返回 -1 或者 false
      */
    public function size($filename);

    /**
     * 根据传入文件名称参数删除存储服务器中的文件
     *
      * @params string $filename 文件名称(无路径)
      * @return bool
     */
    public function delete($filename);
}
