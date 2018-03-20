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


class vmcocean_vhelper
{
    public function function_SYSTEM_FOOTER($params, &$smarty)
    {
        $req_obj = $smarty->_request;
        $app_name = $req_obj->get_app_name();
        $ctl_name = $req_obj->get_ctl_name();
        $act_name = $req_obj->get_act_name();
        $req_params = $req_obj->get_params();
        $remote_ip = base_request::get_remote_addr();

        return $this->_track($app_name, $ctl_name, $act_name, $req_params, $smarty->pagedata, $remote_ip);
    }//End Function

    public function function_SYSTEM_FOOTER_M($params, &$smarty)
    {
        $req_obj = $smarty->_request;
        $app_name = $req_obj->get_app_name();
        $ctl_name = $req_obj->get_ctl_name();
        $act_name = $req_obj->get_act_name();
        $req_params = $req_obj->get_params();
        $remote_ip = base_request::get_remote_addr();

        return $this->_track($app_name, $ctl_name, $act_name, $req_params, $smarty->pagedata, $remote_ip);
    }//End Function

    public function _track($app_name, $ctl_name, $act_name, $params, $pagedata, $remote_ip)
    {
        $hook = implode('_', array($app_name, $ctl_name, $act_name));
        if (!in_array($app_name, array('site', 'mobile'))) {
            if (!in_array(explode('_', $ctl_name)[0], array('site', 'mobile'))) {
                return;
            }
        }

        //return var_export($pagedata,1);
        switch ($hook) {
            /*
             * 首页统计
             */
            case 'site_index_index':
            case 'mobile_index_index':
                $event_name = 'ViewHomePage';
                $track_params = array();
            break;
            /*
             * 商品搜索结果页（列表页统计)
             */
            case 'b2c_site_list_index':
            case 'b2c_mobile_list_index':
                $event_name = 'SearchProduct';
                $track_params['FilterCat'] = '全部分类';
                $track_params['FilterBrand'] = '全部品牌';
                $track_params['KeyWord'] = ($params['keyword'] ? $params['keyword'] : '不限');
                if (!empty($pagedata['cat_path'])) {
                    $end_path = end($pagedata['cat_path']);
                    $track_params['FilterCat'] = $end_path['title']; //分类
                }
                if (!empty($pagedata['brand']) && !empty($pagedata['brand']['brand_name'])) {
                    $track_params['FilterBrand'] = $pagedata['brand']['brand_name'];//品牌
                }
                $track_params['ResultCount'] = (int) $pagedata['all_count'];
            break;
            /*
             * 商品详情页
             */
            case 'b2c_site_product_index':
            case 'b2c_mobile_product_index':
                $event_name = 'ViewProduct';
                $data_detail = $pagedata['data_detail'];
                $track_params['ProductCatalog'] = $data_detail['category']['cat_name'];
                $track_params['ProductBrand'] = $data_detail['brand']['brand_name'];
                $track_params['ProductDBGID'] = $data_detail['product']['goods_id'];
                $track_params['ProductDBPID'] = $data_detail['product']['product_id'];
                $track_params['ProductName'] = $data_detail['product']['name'];
                $track_params['ProductSKU'] = $data_detail['product']['bn'];
                $track_params['ProductBarcode'] = $data_detail['product']['barcode'];
                $track_params['ProductWeightG'] = (float) $data_detail['product']['weight'];
                $track_params['ProductUnit'] = $data_detail['product']['unit'];
                $track_params['ProductSpec'] = $data_detail['product']['spec_info'];
                $track_params['ProductPrice'] = (float) $data_detail['product']['price'];
            break;
            default:
            $event_name = 'ViewPage';
            $track_params['PageTitle'] = $pagedata['title'];
            $track_params['PageController'] = $hook.':'.http_build_query($params);
            break;
        }

        return $this->_callback_render($event_name, $track_params);
    }

    private function _callback_render($event_name, $track_params)
    {
        $encrypt_arr = array(
                'event_name' => $event_name,
                'track_params' => $track_params,
            );
        $encrypt_str = utils::encrypt($encrypt_arr);
        $encrypt_str = vmc::singleton('site_router')->encode_args($encrypt_str);
        $tracker_url = vmc::openapi_url('openapi.vmcoceananlytics', 'tracker', array('track' => $encrypt_str));
        $render_date = date('Y-m-d H:i:s');
        $html = <<<HTML
            <div class="vmcocean-tracker hide hidden" style="display:none;">
                <script>
                    /**
                     * VMC Ocean Tracker
                     * @event_name $event_name
                     * @tracker render at $render_date
                     */
                </script>
                <img src="$tracker_url" alt="VMC Ocean Tracker"/>
            </div>
HTML;

        return $html;
    }
}//End Class
