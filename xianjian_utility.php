<?php
defined('ABSPATH') || exit;
include_once('xianjian_js.php');
include_once('xianjian_consts.php');
function rec_xianjian_is_rec_shown()
{
    try {
        if (!defined('XIANJIAN_ID')) {
            rec_xianjian_token_verify();
        }
        $remote_url    = XIANJIAN_HOST . '/business/plug/scene/count?plugSiteId=' . XIANJIAN_ID;
        $response      = wp_remote_get($remote_url);
        $response_body = wp_remote_retrieve_body($response);
        $response_obj  = json_decode($response_body, true);
        return !empty($response_obj['code']) && $response_obj['code'] == 200 && !empty($response_obj['data']['sceneCount']) && $response_obj['data']['sceneCount'] > 0;
    } catch (Exception $e) {
        return true;
    }
}

function rec_xianjian_random_str($length)
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $result     = "";
    for ($i = 0; $i < $length; $i++) {
        $result .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $result;
}

function rec_xianjian_check_night_time()
{
    $unit_time = gmdate('G');
    return $unit_time >= 8 && $unit_time < 14;
}

function rec_xianjian_check_render_config()
{
    if (defined('RECOM_XIANJIAN_CONFIG_LOADED')) {
        return true;
    }
    $rec_xianjian_last_check_scene_timestamp = get_option(XIANJIAN_LAST_CHECK_SCENE_TIME);
    if (empty($rec_xianjian_last_check_scene_timestamp) || !is_numeric($rec_xianjian_last_check_scene_timestamp)) {
        $rec_xianjian_last_check_scene_timestamp = 0;
    }
    $total_config = array();
    if (time() - $rec_xianjian_last_check_scene_timestamp > 60 || empty($total_config)) {
        $response      = wp_remote_get(XIANJIAN_HOST . '/business/scenes?source=7&plugSiteId=' . XIANJIAN_ID . '&token=' . XIANJIAN_TOKEN, array('timeout' => '5'));
        $response_body = wp_remote_retrieve_body($response);
        $response_obj  = json_decode($response_body, true);
        $total_config  = array();
        if ($response_obj['code'] == 200 && !empty($response_obj['data'])) {
            foreach ($response_obj['data'] as $config) {
                $total_config[$config['id']] = array_merge(
                    json_decode($config['plugConfig'], true),
                    array(
                        'sceneId'     => $config['id'],
                        'clientToken' => XIANJIAN_CLIENT_TOKEN,
                        'itemSetId'   => $config['itemSetId'],
                        'accessToken' => XIANJIAN_ACCESS_TOKEN
                    )
                );
            }
            update_option(XIANJIAN_REND_CONFIG_KEY, json_encode($total_config));
            update_option(XIANJIAN_LAST_CHECK_SCENE_TIME, time());
        }
    }
    foreach ($total_config as $config) {
        $recom_location = isset($config['recomLocation']) ? $config['recomLocation'] : '';
        if ($recom_location == 'TXT') {
            rec_xianjian_content_page($config);
        } elseif ($recom_location == 'HOME') {
            rec_xianjian_home_page($config);
        }
    }
    define('RECOM_XIANJIAN_CONFIG_LOADED', true);
}

function rec_xianjian_content_page($content_config)
{
    $content_count = 0;
    global $rec_xianjian_content_side_id_key;
    $page_location = isset($content_config['pageLocation']) ? $content_config['pageLocation'] : '';
    if ($page_location == 'A_B') {
        global $rec_xianjian_render_content_append_id, $rec_xianjian_is_theme;
        rec_xianjian_set_render_js_code($rec_xianjian_render_content_append_id, $content_config);
        if (!$rec_xianjian_is_theme) {
            add_filter('the_content', 'rec_xianjian_content_append_rec');
        }
    } elseif ($page_location == 'C_T') {
        global $rec_xianjian_render_content_comment_id;
        rec_xianjian_set_render_js_code($rec_xianjian_render_content_comment_id, $content_config);
        add_action('comments_array', 'rec_xianjian_content_comment_rec');
    } elseif ($page_location == 'C_B') {
        global $rec_xianjian_render_content_comment_bottom_id;
        rec_xianjian_set_render_js_code($rec_xianjian_render_content_comment_bottom_id, $content_config);
        add_action('comment_form_after', 'rec_xianjian_content_comment_bottom_rec');
    } elseif ($page_location == 'S') {
        global $rec_xianjian_content_side, $rec_xianjian_render_content_side_id;
        rec_xianjian_set_side_render_js_code($rec_xianjian_render_content_side_id, $content_config);
        $rec_xianjian_content_side = true;
        $content_count++;
    }
    update_option($rec_xianjian_content_side_id_key, $content_count);
}

function rec_xianjian_content_append_rec($content)
{
    if (is_single()) {
        global $rec_xianjian_js_code_map, $rec_xianjian_render_content_append_id;
        $content .= isset($rec_xianjian_js_code_map[$rec_xianjian_render_content_append_id]) ? $rec_xianjian_js_code_map[$rec_xianjian_render_content_append_id] : '';
    }
    return $content;
}

function rec_xianjian_content_comment_rec($comments)
{
    if (is_single()) {
        global $rec_xianjian_js_code_map, $rec_xianjian_render_content_comment_id;
        echo '<div style="margin-top:15px">';
        echo isset($rec_xianjian_js_code_map[$rec_xianjian_render_content_comment_id]) ? $rec_xianjian_js_code_map[$rec_xianjian_render_content_comment_id] : '';
        echo '</div>';
    }
    return $comments;
}

function rec_xianjian_content_comment_bottom_rec($post_id)
{
    if (is_single()) {
        global $rec_xianjian_js_code_map, $rec_xianjian_render_content_comment_bottom_id;
        echo isset($rec_xianjian_js_code_map[$rec_xianjian_render_content_comment_bottom_id]) ? $rec_xianjian_js_code_map[$rec_xianjian_render_content_comment_bottom_id] : '';
    }
}

function rec_xianjian_home_page($home_config)
{
    $page_location = isset($home_config['pageLocation']) ? $home_config['pageLocation'] : '';
    if (strcmp($page_location, 'A_L_B') == 0) {
        global $rec_xianjian_home_bottom, $rec_xianjian_render_home_bottom_id;
        rec_xianjian_set_render_js_code($rec_xianjian_render_home_bottom_id, $home_config);
        $rec_xianjian_home_bottom = true;
    }
    if (strcmp($page_location, 'S') == 0) {
        global $rec_xianjian_home_side, $rec_xianjian_render_home_side_id;
        rec_xianjian_set_home_side_render_js_code($rec_xianjian_render_home_side_id, $home_config);
        $rec_xianjian_home_side = true;
    }
}

/**
 * @param array $post
 * @param int $update_last_upload_id
 * @param string $access_token
 * @return bool
 */
function rec_xianjian_upload_material($post, $update_last_upload_id, $access_token)
{
    try {
        $last_upload_id_key = 'last_upload_id_' . $access_token;
        $body_arr           = format_post_data($post);
        if (is_array($body_arr)) {
            $body           = '[' . json_encode($body_arr) . ']';
            $request_header = array(
                'Content-Type' => 'application/json',
                'charset'      => 'utf-8'
            );
            $args           = array(
                'headers' => $request_header,
                'body'    => $body,
                'timeout' => '8'
            );
            $remote_url     = XIANJIAN_HOST . '/business/items?accessToken=' . $access_token . '&source=1';
            $response       = wp_remote_post($remote_url, $args);
            $response_body  = wp_remote_retrieve_body($response);
            $response_obj   = json_decode($response_body, true);
        }
        if ($body_arr === true || (isset($response_obj['code']) && $response_obj['code'] == 200)) {
            if ($update_last_upload_id == -2) {
                return true;
            }
            $last_upload_id = $update_last_upload_id == -1 ? intval($post['ID']) : $update_last_upload_id;
            return update_option($last_upload_id_key, $last_upload_id);
        }
    } catch (Exception $e) {
    }
    return false;
}

/**
 * 格式化主题帖数据
 * @param $post
 * @return array|bool
 */
function format_post_data($post)
{
    try {
        global $wpdb;
        $object_id    = intval($post['ID']);
        $item_id      = $object_id;
        $title        = preg_replace('/<.*?>/', '', $post['post_title']);
        $publish_time = strtotime($post['post_date']) * 1000;
        $content      = $post['post_content'];
        $picture_url  = '';
        $thumbnail    = $wpdb->get_row('SELECT `meta_value` FROM `' . $wpdb->prefix . 'postmeta` WHERE `post_id`=' . $object_id . " AND meta_key='_thumbnail_id'", ARRAY_A);
        if (!empty($thumbnail)) {
            $thumbnail_id  = intval($thumbnail['meta_value']);
            $thumbnail_url = $wpdb->get_row('SELECT `guid` FROM `' . $wpdb->prefix . 'posts` WHERE `ID`=' . $thumbnail_id, ARRAY_A);
            if (!empty($thumbnail_url['guid'])) {
                $picture_url = trim($thumbnail_url['guid']);
            }
        }
        if (empty($picture_url)) {
            $match_result = preg_match_all('/<img.*?src[\s]*=[\s]*[\"\'](http.*?)[\"\'].*?>/i', $content, $matches);
            if ($match_result && !empty($matches[1])) {
                $picture_url = implode(',', $matches[1]);
            }
        }
        $content   = preg_replace('/[\n\r\t]*|(<.*?>)*/', '', $content);
        $content   = addslashes($content);
        $real_post = get_post($object_id);
        $url       = get_permalink($real_post);
        if (strlen($content) < 5 || empty($url)) {
            return true;
        }
        $post_author_id     = $post['post_author'];
        $user               = $wpdb->get_row('SELECT display_name FROM `' . $wpdb->prefix . 'users` WHERE ID=' . $post_author_id, ARRAY_A);
        $publisher_id       = isset($user['display_name']) ? $user['display_name'] : '';
        $term_relationships = $wpdb->get_results('SELECT term_taxonomy_id FROM `' . $wpdb->prefix . 'term_relationships` WHERE object_id=' . $object_id, ARRAY_A);
        $category_id        = '';
        $tag                = '';
        foreach ($term_relationships as $term_relationship) {
            $term_taxonomy_id = isset($term_relationship['term_taxonomy_id']) ? $term_relationship['term_taxonomy_id'] : 0;
            $term_taxonomies  = $wpdb->get_results('SELECT `term_id`,`taxonomy` FROM `' . $wpdb->prefix . 'term_taxonomy` WHERE `term_taxonomy_id`=' . $term_taxonomy_id, ARRAY_A);
            $categories       = array();
            $tags             = array();
            foreach ($term_taxonomies as $term_taxonomy) {
                $term_id  = isset($term_taxonomy['term_id']) ? $term_taxonomy['term_id'] : 0;
                $taxonomy = isset($term_taxonomy['taxonomy']) ? $term_taxonomy['taxonomy'] : '';
                $terms    = $wpdb->get_results('SELECT `name` FROM `' . $wpdb->prefix . 'terms` WHERE `term_id`=' . $term_id, ARRAY_A);
                foreach ($terms as $term) {
                    $name = $term['name'];
                    if ($taxonomy === 'category') {
                        $categories[] = $name;
                    } elseif ($taxonomy === 'post_tag') {
                        $tags[] = $name;
                    }
                }
            }
            foreach ($categories as $category) {
                $category_id .= rtrim($category . ',');
            }
            foreach ($tags as $keyword) {
                $tag .= rtrim($keyword . ',');
            }
        }
        $category_id = chop($category_id, ',');
        $tag         = chop($tag, ',');
        if (empty($category_id) && empty($tag)) {
            return true;
        }
        $body_arr = array(
            'itemId'      => $item_id,
            'title'       => $title,
            'content'     => $content,
            'publisherId' => $publisher_id,
            'categoryId'  => $category_id,
            'url'         => $url,
            'tag'         => $tag,
            'publishTime' => $publish_time,
            'coverUrl'    => $picture_url
        );
        return $body_arr;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 获取指定文件夹下的所有文件（不包含文件木楼）列表
 * @param string $path
 * @return array
 */
function get_file_list_by_dir($path)
{
    $path = rtrim($path, DIRECTORY_SEPARATOR);
    if (is_file($path)) {
        return array();
    }
    $arrFiles = scandir($path);
    $arrFiles = array_diff($arrFiles, array('.', '..'));
    if (empty($arrFiles)) {
        return array();
    }
    $res = array();
    foreach ($arrFiles as $filePath) {
        $filePath = $path . DIRECTORY_SEPARATOR . $filePath;
        if (is_file($filePath)) {
            $res[] = $filePath;
        } elseif (is_dir($filePath)) {
            $itemFiles = get_file_list_by_dir($filePath);
            if (!empty($itemFiles)) {
                $res = array_merge($res, $itemFiles);
            }
        }
    }
    return $res;
}

/**
 * Check the integrity of plug-in files, and check whether the files of plug-in have been tampered.
 */
function check_xianjian_plugin_file_complete(){
    if(isset($_GET['verify_file']) && $_GET['verify_file'] == 'xianjian') {
        $xianjian_plugin_dir = plugin_dir_path(__FILE__);
        $xianjian_file_list  = get_file_list_by_dir($xianjian_plugin_dir);
        $res                 = array();
        foreach ($xianjian_file_list as $fname) {
            $res[$fname] = md5_file($fname);
        }
        header('Content-Type:application/json;charset=UTF-8');
        echo json_encode($res);
        die();
    }
}

