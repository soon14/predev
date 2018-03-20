!function(){
    /**
     * 此段脚本用于模板开发预览提示;
     * 模板预览是因为COOKIE中存在CURRENT_THEME,当COOKIE中存在CURRENT_THEME,则系统会认为您正在预览模板,其他用户不受影响。
     * 适合开发者开发模板用
     */
    var tmp_tip = document.createElement("div"),body = document.getElementsByTagName("body")[0];
        tmp_tip.innerHTML = "<strong>您当前正在模板预览模式</strong>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' onclick='this.parentNode.style.display=\"none\"'>[隐藏提示]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='?_theme_preview_exit=1'>&nbsp;[退出预览]</a>";
        tmp_tip.style.cssText='background:#fffee0;text-align:center;font-size:12px;padding:5px;position:fixed;width:100%;color:#333;top:0;z-index:65535;border-bottom:4px #ffdd00 solid;top:0;left:0;';
        setTimeout(function(){
            body.insertBefore(tmp_tip, body.childNodes[0]);
        },5000);
}();
