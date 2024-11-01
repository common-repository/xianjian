<?php

defined('ABSPATH') || exit;

include_once('xianjian_consts.php');

add_action( 'trash_post', 'rec_xianjian_trash_post');
add_action( 'untrash_post', 'rec_xianjian_untrash_post');
add_action( 'save_post', 'rec_xianjian_save_post');
add_action( 'get_footer', 'rec_xianjian_insert_item_id');

/**
 * @param int $post_id
 */
function rec_xianjian_trash_post($post_id) {
    $body           = '["' . $post_id . '"]';
    $request_header = array(
        'Content-Type' => 'application/json',
        'charset'      => 'utf-8'
    );
    $args           = array(
        'headers' => $request_header,
        'body'    => $body,
        'timeout' => '8'
    );
    $response       = wp_remote_post(XIANJIAN_HOST . '/business/items/remove?accessToken=' . XIANJIAN_ACCESS_TOKEN . '&type=1', $args);
    wp_remote_retrieve_body($response);
}

/**
 * @param int $post_id
 */
function rec_xianjian_untrash_post($post_id)
{
    global $wpdb;
    $update_posts = $wpdb->get_results('SELECT `ID`,`post_author`,`post_date`,`post_content`,`post_title` FROM `' . $wpdb->prefix . 'posts` WHERE ID=' . $post_id, ARRAY_A);
    foreach ($update_posts as $update_post) {
        rec_xianjian_upload_material($update_post, -2, XIANJIAN_ACCESS_TOKEN);
    }
}

/**
 * @param int $post_id
 */
function rec_xianjian_save_post($post_id)
{
    $true_post_id = wp_is_post_revision($post_id);
    if (empty($true_post_id)) {
        $true_post_id = $post_id;
    }
    global $wpdb;
    $update_posts = $wpdb->get_results('SELECT ID,post_author,post_date,post_content,post_title,post_status FROM `' . $wpdb->prefix . 'posts` WHERE ID=' . $true_post_id, ARRAY_A);
    foreach ($update_posts as $update_post) {
        $post_status = isset($update_post['post_status']) ? $update_post['post_status'] : '';
        if ($post_status == 'publish') {
            rec_xianjian_upload_material($update_post, -2, XIANJIAN_ACCESS_TOKEN);
        } else {
            $post_id = isset($update_post['ID']) ? $update_post['ID'] : '';
            rec_xianjian_trash_post($post_id);
        }
    }
}

/**
 * @param $content
 * @return mixed
 */
function rec_xianjian_insert_item_id($content)
{
    if (is_single()) {
        $post_id = get_queried_object_id();
        echo '<div id="paradigm_detail_page_item_id" data-paradigm-item-id="' . $post_id . '"></div>';
    }
    return $content;
}