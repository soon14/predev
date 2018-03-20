<?php

class myfun {

    public static function vard() {
        $exit = true;
        $args = func_get_args();
        if (count($args) > 1) {
            $_tmp = $args;
            $_last = array_pop($_tmp);
            if (is_bool($_last)) {
                $args = $_tmp;
                $exit = $_last;
            }
            unset($_tmp, $_last);
        }
        count($args) == 1 && $args = current($args);
        echo("<code class=\"dp_show_box\"><pre>");
        print_r($args);
        echo("</pre></code>");
        $exit && exit;
    }

}
