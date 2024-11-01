<?php

/*
|--------------------------------------------------------------------------
| Vafpress Framework Constants
|--------------------------------------------------------------------------
*/

defined('VP_W2GM_VERSION')     or define('VP_W2GM_VERSION'    , '2.0-beta');
defined('VP_W2GM_NAMESPACE')   or define('VP_W2GM_NAMESPACE'  , 'VP_W2GM_');
defined('VP_W2GM_DIR')         or define('VP_W2GM_DIR'        , untrailingslashit(dirname(__FILE__)));
defined('VP_W2GM_DIR_NAME')    or define('VP_W2GM_DIR_NAME'   , basename(VP_W2GM_DIR));
defined('VP_W2GM_IMAGE_DIR')   or define('VP_W2GM_IMAGE_DIR'  , VP_W2GM_DIR . '/public/img');
defined('VP_W2GM_CONFIG_DIR')  or define('VP_W2GM_CONFIG_DIR' , VP_W2GM_DIR . '/config');
defined('VP_W2GM_DATA_DIR')    or define('VP_W2GM_DATA_DIR'   , VP_W2GM_DIR . '/data');
defined('VP_W2GM_CLASSES_DIR') or define('VP_W2GM_CLASSES_DIR', VP_W2GM_DIR . '/classes');
defined('VP_W2GM_VIEWS_DIR')   or define('VP_W2GM_VIEWS_DIR'  , VP_W2GM_DIR . '/views');
defined('VP_W2GM_INCLUDE_DIR') or define('VP_W2GM_INCLUDE_DIR', VP_W2GM_DIR . '/includes');

// finally framework base url
$vp_w2gm_url         = trim(plugins_url('/', __FILE__), '/');

defined('VP_W2GM_URL')         or define('VP_W2GM_URL'        , untrailingslashit($vp_w2gm_url));
defined('VP_W2GM_PUBLIC_URL')  or define('VP_W2GM_PUBLIC_URL' , VP_W2GM_URL        . '/public');
defined('VP_W2GM_IMAGE_URL')   or define('VP_W2GM_IMAGE_URL'  , VP_W2GM_PUBLIC_URL . '/img');
defined('VP_W2GM_INCLUDE_URL') or define('VP_W2GM_INCLUDE_URL', VP_W2GM_URL        . '/includes');

// Get the start time and memory usage for profiling
defined('VP_W2GM_START_TIME')  or define('VP_W2GM_START_TIME', microtime(true));
defined('VP_W2GM_START_MEM')   or define('VP_W2GM_START_MEM',  memory_get_usage());

/**
 * EOF
 */