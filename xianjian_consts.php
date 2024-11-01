<?php
defined('ABSPATH') || exit;
function register_session(){
    if(!session_id() )
        session_start();
}
add_action('init','register_session');
define('XIANJIAN_CHANNEL', 'plugin_official');
define('XIANJIAN_HOST', 'https://nbrecsys.4paradigm.com');
define('XIANJIAN_AUTH_TOKEN','xianjian_paradigm_token');
define('XIANJIAN_LAST_FETCH_SERVER_CONFIG', 'paradigm_last_fetch_server_config');
define('XIANJIAN_SERVER_CONFIG_KEY', 'paradigm_server_config');
define('XIANJIAN_REND_CONFIG_KEY', 'paradigm_render_config');
define('XIANJIAN_LAST_UPLOAD_TIME', 'xianjian_paradigm_last_upload_time');
define('XIANJIAN_LAST_CHECK_SCENE_TIME', 'paradigm_last_check_scene_timestamp');
define('XIANJIAN_UPLOAD_LIMIT', 5);

$rec_xianjian_channel       = 'plugin_official';
$rec_xianjian_version       = '1.2.0';
$rec_xianjian_inner_version = 120;
$rec_xianjian_config_key    = 'paradigm_render_config';
$rec_xianjian_is_theme      = false;
$rec_xianjian_content_side  = false;
$rec_xianjian_home_side     = false;
$rec_xianjian_home_bottom   = false;
$rec_xianjian_config_loaded = false;
$rec_xianjian_host          = "https://nbrecsys.4paradigm.com";
$rec_xianjian_sdk_obj       = "ParadigmSDKv3";
if (strcmp($rec_xianjian_host, "https://recsys-free.4paradigm.com") == 0) {
    $rec_xianjian_sdk_obj = "ParadigmSDKv3Test";
}
$rec_xianjian_render_content_side_id           = 'paradigm_render_content_side_id';
$rec_xianjian_render_content_append_id         = 'paradigm_render_content_append_id';
$rec_xianjian_render_content_comment_id        = 'paradigm_render_content_comment_id';
$rec_xianjian_render_content_comment_bottom_id = 'paradigm_render_content_comment_bottom_id';
$rec_xianjian_render_home_side_id              = 'paradigm_render_home_side_id';
$rec_xianjian_render_home_bottom_id            = 'paradigm_render_home_bottom_id';
$rec_xianjian_content_side_id_key              = 'paradigm_content_side_id';
$rec_xianjian_last_check_update_timestamp_key  = 'paradigm_last_check_update_timestamp';
$rec_xianjian_last_upload_timestamp_key        = 'paradigm_last_upload_timestamp';
$rec_xianjian_last_fetch_server_config_key     = 'paradigm_last_fetch_server_config';
$rec_xianjian_server_config_key                = 'paradigm_server_config';
$rec_xianjian_last_check_scene_timestamp_key   = 'paradigm_last_check_scene_timestamp';
$rec_xianjian_access_token                     = '';
$rec_xianjian_js_code_map                      = array();
$rec_xianjian_side_js_code_map                 = array();
$rec_xianjian_home_side_js_code_map            = array();
?>