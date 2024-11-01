<?php
/*
Plugin Name: 先荐-猜你喜欢
Description: 一款简单易用的智能推荐插件，由第四范式提供. <a href="plugins.php?page=rec_xianjian_rec_options">启用插件后，可以点击这里进行配置</a>。
Version: 1.1.15
Author: xianjian-rec
Author URI: https://www.4paradigm.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: xianjian
*/

defined('ABSPATH') || exit;

include_once('xianjian_consts.php');
include_once('xianjian_utility.php');
include_once('xianjian_item.php');
include_once('xianjian_js.php');
include_once('xianjian_token.php');

add_action('plugins_loaded', 'rec_xianjian_plugin_setup');
add_action('admin_menu', 'rec_xianjian_rec_menu');
add_filter('wp_enqueue_script', 'loaded_src', 20, 1);
add_action('wp_head', 'rec_xianjian_call_paradigmPluginSDKjs');
add_action('wp_enqueue_scripts','rec_xianjian_load_jquery');

function rec_xianjian_call_paradigmPluginSDKjs(){
    echo '<script type="application/javascript" src="' . XIANJIAN_HOST . '/sdk/js/paradigmPluginSDK.js"></script>';
}

function rec_xianjian_load_jquery(){
    wp_enqueue_script('jquery');
}

function loaded_src($src){
    return preg_replace('/^(http|https):/', '', $src);
}

add_filter('wp_enqueue_style', 'loaded_src', 20, 1);
add_action('admin_enqueue_scripts', 'rec_xianjian_custom_scripts');
add_filter('plugin_action_links_xianjian/xianjian.php', 'rec_xianjianLinks');

function rec_xianjian_plugin_setup()
{
    check_xianjian_plugin_file_complete(); //Check the integrity of plug-in files, and check whether the files of plug-in have been tampered.
    wp_register_sidebar_widget('rec_xianjian_home_bottom', '先荐推荐栏-首页底部栏', 'widget_rec_xianjian_home_bottom');
    wp_register_sidebar_widget('rec_xianjian_home_side_1', '先荐推荐栏-首页侧边栏1', 'widget_rec_xianjian_home_side_1');
    wp_register_sidebar_widget('rec_xianjian_home_side_2', '先荐推荐栏-首页侧边栏2', 'widget_rec_xianjian_home_side_2');
    wp_register_sidebar_widget('rec_xianjian_content_side_1', '先荐推荐栏-正文页侧边栏1', 'widget_rec_xianjian_content_side_1');
    wp_register_sidebar_widget('rec_xianjian_content_side_2', '先荐推荐栏-正文页侧边栏2', 'widget_rec_xianjian_content_side_2');
    wp_register_sidebar_widget('rec_xianjian_content_side_3', '先荐推荐栏-正文页侧边栏3', 'widget_rec_xianjian_content_side_3');
}

function rec_xianjian_rec_menu()
{
    add_submenu_page('plugins.php', __('先荐选项'), __('先荐-猜你喜欢'), 'manage_options', 'rec_xianjian_rec_options', 'rec_xianjian_rec_options');
    add_submenu_page('plugins.php', __('先荐选项1'), __('先荐-猜你喜欢1'), 'manage_options', 'rec_xianjian_rec_options1', 'rec_xianjian_rec_options1');
    add_submenu_page('plugins.php', __('先荐选项2'), __('先荐-猜你喜欢2'), 'manage_options', 'rec_xianjian_rec_options2', 'rec_xianjian_rec_options2');
    add_submenu_page('plugins.php', __('先荐选项3'), __('先荐-猜你喜欢3'), 'manage_options', 'rec_xianjian_rec_options3', 'rec_xianjian_rec_options3');
    add_submenu_page('plugins.php', __('先荐选项4'), __('先荐-猜你喜欢4'), 'manage_options', 'rec_xianjian_rec_options4', 'rec_xianjian_rec_options4');
}

function rec_xianjianLinks($links)
{
    $mylinks = array('<a href="plugins.php?page=rec_xianjian_rec_options">设置</a>');
    return array_merge($links, $mylinks);
}

function rec_xianjian_rec_options()
{
    include 'config/sceneList.php';
}

function rec_xianjian_rec_options1()
{
    include 'config/styleConfig.php';
}

function rec_xianjian_rec_options2()
{
    include 'config/report.php';
}

function rec_xianjian_rec_options3()
{
    include 'config/mateManege.php';
}

function rec_xianjian_rec_options4()
{
    include 'config/brower.php';
}

function widget_rec_xianjian_home_bottom($args)
{
    if (is_front_page()) {
        global $rec_xianjian_home_bottom, $rec_xianjian_render_home_bottom_id;
        if ($rec_xianjian_home_bottom) {
            insert_rec_xianjian_js($args, $rec_xianjian_render_home_bottom_id);
        }
    }
}

function widget_rec_xianjian_home_side_1($args)
{
    if (is_front_page()) {
        global $rec_xianjian_home_side, $rec_xianjian_render_home_side_id;
        if ($rec_xianjian_home_side) {
            insert_rec_xianjian_home_side_js($args, $rec_xianjian_render_home_side_id);
        }
    }
}

function widget_rec_xianjian_home_side_2($args)
{
    if (is_front_page()) {
        global $rec_xianjian_home_side, $rec_xianjian_render_home_side_id;
        if ($rec_xianjian_home_side) {
            insert_rec_xianjian_home_side_js($args, $rec_xianjian_render_home_side_id);
        }
    }
}

function widget_rec_xianjian_content_side_1($args)
{
    if (is_single()) {
        global $rec_xianjian_content_side, $rec_xianjian_render_content_side_id;
        if ($rec_xianjian_content_side) {
            insert_rec_xianjian_side_js($args, $rec_xianjian_render_content_side_id);
        }
    }
}

function widget_rec_xianjian_content_side_2($args)
{
    if (is_single()) {
        global $rec_xianjian_content_side, $rec_xianjian_render_content_side_id;
        if ($rec_xianjian_content_side) {
            insert_rec_xianjian_side_js($args, $rec_xianjian_render_content_side_id);
        }
    }
}

function widget_rec_xianjian_content_side_3($args)
{
    if (is_single()) {
        global $rec_xianjian_content_side, $rec_xianjian_render_content_side_id;
        if ($rec_xianjian_content_side) {
            insert_rec_xianjian_side_js($args, $rec_xianjian_render_content_side_id);
        }
    }
}

function rec_xianjian_custom_scripts()
{
    global $rec_xianjian_version, $rec_xianjian_channel;
    if ($rec_xianjian_channel == 'plugin_official_beepress') {
        wp_enqueue_style('rec_xianjian_plugin_beer', plugins_url('config/static/beerPress.css', __FILE__), null, $rec_xianjian_version);
    }
    wp_enqueue_style('rec_xianjian_element', plugins_url('config/static/element-theme/index.css', __FILE__), null, $rec_xianjian_version);
    wp_enqueue_style('rec_xianjian_plugin', plugins_url('config/static/plugin.css', __FILE__), array(), $rec_xianjian_version);
    wp_enqueue_script('rec_xianjian_vue', plugins_url('config/static/vue.js', __FILE__), null, $rec_xianjian_version);
    wp_enqueue_script('rec_xianjian_elementjs', plugins_url('config/static/element-ui/index.js', __FILE__), null, $rec_xianjian_version);
    wp_enqueue_script('rec_xianjian_axios', plugins_url('config/api/axios.js', __FILE__), null, $rec_xianjian_version);
    wp_enqueue_script('rec_xianjian_api', plugins_url('config/api/api.js', __FILE__), null, $rec_xianjian_version);
    wp_enqueue_script('rec_xianjian_util', plugins_url('config/util/util.js', __FILE__), null, $rec_xianjian_version);
    wp_enqueue_script('rec_xianjian_echarts', plugins_url('config/static/echarts.js', __FILE__), null, $rec_xianjian_version);
}
