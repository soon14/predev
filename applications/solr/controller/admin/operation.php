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


class solr_ctl_admin_operation extends desktop_controller
{


    public function index()
    {
        $solr_stage = vmc::singleton('solr_stage');

        $base_data = $solr_stage->getList('*',null,0,3);
        $data = $base_data['rows'];
        foreach ($data as $row) {

                echo '<hr/><table>';

                // the documents are also iterable, to get all fields
                foreach ($row as $field => $value) {
                    // this converts multivalue fields to a comma-separated string
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
                }

                echo '</table>';
            }


        $this->page('admin/operation.html');
    }

    public function test(){
        $solr_stage = vmc::singleton('solr_stage');

        vmc::dump($solr_stage->highlight());
    }
}
