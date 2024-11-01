<?php
defined('ABSPATH') || exit;

include_once('xianjian_consts.php');
include_once('xianjian_utility.php');
include_once('xianjian_item.php');

add_action( 'wp_loaded', 'rec_xianjian_setup');
function rec_xianjian_setup()
{
    rec_xianjian_token_verify();
    rec_xianjian_check_render_config();
    if(isset($_POST['xianjian_apply']) && $_POST['xianjian_apply']=='upload'){
        rec_xianjian_check_item();
        exit;
    }
}

function rec_xianjian_token_verify()
{
    try {
        if (defined('XIANJIAN_ID')) {
            return true;
        }
        $auth_info = get_option(XIANJIAN_AUTH_TOKEN);
        if (!empty($auth_info)) {
            $auth_info = json_decode($auth_info, true);
        }
        $site_url = home_url();
        $site_id = empty($auth_info['siteId']) ? md5($site_url . date('Y-m-d', time())) : $auth_info['siteId'];
        $body_arr      = array(
            'domain'       => $site_url,
            'plugSiteId'   => $site_id,
            'terminalType' => 7,
            'plugChannel'  => XIANJIAN_CHANNEL,
        );
        $args          = array(
            'body'    => $body_arr,
            'timeout' => '8'
        );
        $remote_url    = XIANJIAN_HOST . '/business/plug/register/login';
        $response      = wp_remote_post($remote_url, $args);
        $response_body = wp_remote_retrieve_body($response);
        $response_obj  = json_decode($response_body, true);
        $code          = isset($response_obj['code']) ? $response_obj['code'] : 0;
        if ($code != 200 || empty($response_obj['data'])) {
            return false;
        }
        $data                = $response_obj['data'];
        $auth_info['siteId'] = $site_id;
        if (!empty($data['token'])) {
            $auth_info['token'] = $data['token'];
        }
        if (!empty($data['clientToken'])) {
            $auth_info['clientToken'] = $data['clientToken'];
        }
        if (!empty($data['accessToken'])) {
            $auth_info['accessToken'] = $data['accessToken'];
        }
        foreach ($data as $key => $value) {
            if (!empty($value['accessToken'])) {
                $auth_info['accessToken'] = $value['accessToken'];
            }
        }
        if(empty( $auth_info['token'])) {
            update_option(XIANJIAN_AUTH_TOKEN, '');
            return false;
        }
        if (empty($auth_info['accessToken']) || $auth_info['clientToken']) {
            $arrToken = getclientAccessTokens($auth_info['siteId'], $auth_info['token']);
            if (false === $arrToken) {
                update_option(XIANJIAN_AUTH_TOKEN, '');
                return false;
            }
            $auth_info['accessToken'] = $arrToken['accessToken'];
            $auth_info['clientToken'] = $arrToken['clientToken'];
        }
        update_option(XIANJIAN_AUTH_TOKEN, json_encode($auth_info));
        define('XIANJIAN_ID', $auth_info['siteId']);
        define('XIANJIAN_TOKEN', $auth_info['token']);
        define('XIANJIAN_CLIENT_TOKEN', $auth_info['clientToken']);
        define('XIANJIAN_ACCESS_TOKEN', $auth_info['accessToken']);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 上传物料
 * @return bool
 */
function rec_xianjian_check_item()
{
    if(function_exists('fastcgi_finish_request')){
        fastcgi_finish_request();
    } else {
        header('Content-Length: 0');
        header('Connection: Close');
        flush();
        ob_flush();
    }
    session_write_close();
    if (!defined('XIANJIAN_ACCESS_TOKEN')) {
        return false;
    }
    $last_upload_time = get_option(XIANJIAN_LAST_UPLOAD_TIME);
    $last_upload_time = (empty($last_upload_time) || !is_numeric($last_upload_time)) ? 0 : intval($last_upload_time);
    if ($last_upload_time + 4 > time()) {
        return true;
    }
    $res = update_option(XIANJIAN_LAST_UPLOAD_TIME, time());
    if (empty($res)) {
        return true;
    }
    global $wpdb;
    $last_upload_id_key = 'last_upload_id_' . XIANJIAN_ACCESS_TOKEN;
    $last_upload_id     = get_option($last_upload_id_key);
    if (is_numeric($last_upload_id) && $last_upload_id == 0) {
        return true;
    }
    $request_header = array(
        'Content-Type' => 'application/json',
        'charset'      => 'utf-8'
    );
    $remote_url = XIANJIAN_HOST . '/business/items?accessToken=' . XIANJIAN_ACCESS_TOKEN . '&source=1';
    while ($last_upload_id !== 0) {
        $conds = is_numeric($last_upload_id) && $last_upload_id > 0 ? (' `ID` < ' . ceil($last_upload_id) . ' AND ') : '';
        # 获取文章信息
        $posts   = $wpdb->get_results('SELECT `ID`,`post_author`,`post_date`,`post_content`,`post_title`,`post_status`,`post_parent` FROM `' . $wpdb->prefix . 'posts` WHERE ' . $conds . "`post_status`='publish' ORDER BY `ID` DESC LIMIT " . XIANJIAN_UPLOAD_LIMIT, ARRAY_A);
        if(!is_array($posts)){
            return false;
        }
        if(empty($posts)){
            if(!is_numeric($last_upload_id)){
                return false;
            }
            $min_id = $wpdb->get_results('SELECT `ID` FROM `' . $wpdb->prefix . "posts` WHERE `post_status`='publish' ORDER BY `ID` ASC LIMIT 1", ARRAY_A);
            if(!isset($min_id[0]['ID']) || $min_id[0]['ID'] > $last_upload_id){
                return false;
            }
            return update_option($last_upload_id_key, 0);
        }
        $arrData = array();
        foreach ($posts as $post) {
            $last_upload_id = intval($post['ID']);
            $post = format_post_data($post);
            if (false === $post) {
                return false;
            }
            if (is_array($post)) {
                $arrData[] = $post;
            }
        }
        $args = array(
            'headers' => $request_header,
            'body'    => json_encode($arrData),
            'timeout' => '8'
        );
        $response      = wp_remote_post($remote_url, $args);
        $response_body = wp_remote_retrieve_body($response);
        $response_obj  = json_decode($response_body, true);
        if (isset($response_obj['code']) && $response_obj['code'] == 200) {
            $res = update_option($last_upload_id_key, $last_upload_id);
            if (false === $res) {
                return false;
            }
            $res = update_option(XIANJIAN_LAST_UPLOAD_TIME, time());
            if (false === $res) {
                return false;
            }
        } else {
            return false;
        }
    }
}

/**
 * 获取accessToken与clientToken
 * @param string  $plugSiteId
 * @param string $token
 * @return array|bool
 */
function getclientAccessTokens($plugSiteId, $token){
    $remote_url = XIANJIAN_HOST . '/business/scenes?source=7&plugSiteId=' . $plugSiteId . '&token=' . $token;
    $response = wp_remote_get($remote_url);
    if(!isset($response['body'])) {
        return false;
    }
    $body = json_decode($response['body'],true);
    if(!isset($body['code']) || $body['code']!=200 || empty($body['data']) || !is_array($body['data'])){
        return false;
    }
    $sceneId = ceil($body['data'][0]['id']);
    $remote_url = XIANJIAN_HOST . '/business/scene?sceneId=' . $sceneId . '&token=' . $token;
    $response = wp_remote_get($remote_url);
    if(!isset($response['body'])) {
        return false;
    }
    $body = json_decode($response['body'], true);
    if(!isset($body['code']) || $body['code']!=200 || empty($body['data']) || !is_array($body['data'])){
        return false;
    }
    $result = [
        'clientToken' => $body['data']['clientToken'],
        'accessToken' => $body['data']['itemSet']['accessToken']
    ];
    return $result;
}
