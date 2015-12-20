<?php

$site_path = realpath(dirname(__FILE__));
$site_link = "http://$_SERVER[HTTP_HOST]/mymvc/";
define ('__SITE_PATH', $site_path);
define('__SITE_LINK',$site_link);
define('__USING_REWRITE',false);

function route($route,$r_str = '?r=')
{
    return __USING_REWRITE === true ? __SITE_LINK . $route : __SITE_LINK . $r_str . $route;
}

include __SITE_PATH  . '/' . 'app/init.php';

$controller_path = __SITE_PATH . '/controller';
$layout_path = __SITE_PATH . '/views/layout';

$registry->router = new app\Router($registry, $controller_path);
$registry->template = new app\Template($registry,$layout_path);
$registry->router->loader();
