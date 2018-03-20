#!/usr/bin/env php
<?php
    function command_hardware()
    {
        if(function_exists('zend_loader_enabled') && zend_loader_enabled())
        {
            foreach (zend_get_id() as $hardware) {
                echo $hardware, "\n";
            }
        } else {
            echo 'zend guard loader not installed or not enabled!';
            exit;
        }
    }
command_hardware();
