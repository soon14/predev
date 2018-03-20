<?php

class ectools_qrcode_api extends base_openapi
{
    public function encode($options)
    {
        $size = isset($options['size']) ? $options['size'] : 5;
        $margin = isset($options['margin']) ? $options['margin'] : 5;
        $txt = urldecode($_GET['txt']);
        if (!$txt) {
            return;
        }

        ectools_qrcode_QRcode::png($txt, false, 0 , $size, $margin);
    }
}
