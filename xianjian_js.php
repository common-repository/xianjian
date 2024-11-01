<?php
defined('ABSPATH') || exit;

include_once('xianjian_consts.php');

/**
 *
 * @param int $render_div_id
 * @param array $config
 * @return string
 */
function rec_xianjian_get_js_code($render_div_id, $config)
{
    global $rec_xianjian_js_code_map, $rec_xianjian_sdk_obj, $rec_xianjian_inner_version;
    $rec_xianjian_js_code = '
<div id="' . $render_div_id . "_" . $config['sceneId'] . '">
    <script type="application/javascript">
        (function($){
            window._paradigm_plug_sdkV3_loaded=true;
            ' . $rec_xianjian_sdk_obj . ".init('" . $config['clientToken'] . "',{isDisableArticleFetch:true});
            " . $rec_xianjian_sdk_obj . ".setPluginVersion(" . $rec_xianjian_inner_version . ");
            " . $rec_xianjian_sdk_obj . ".renderArticle('" . $render_div_id . "_" . $config['sceneId'] . "'," . $config['itemSetId'] . "," . $config['sceneId'] . ");
            $.post('/', {xianjian_apply:'upload'});
        })(jQuery);
    </script>
</div>
";
    return $rec_xianjian_js_code;
}

/**
 * @param int $render_div_id
 * @param array $config
 */
function rec_xianjian_set_render_js_code($render_div_id, $config)
{
    global $rec_xianjian_js_code_map;
    $rec_xianjian_js_code = rec_xianjian_get_js_code($render_div_id, $config);
    $pre_js_code = isset($rec_xianjian_js_code_map[$render_div_id]) ? $rec_xianjian_js_code_map[$render_div_id] : '';
    $total_js_code = $pre_js_code.$rec_xianjian_js_code;
    $rec_xianjian_js_code_map[$render_div_id] = $total_js_code;
}

/**
 * @param int $render_div_id
 * @param array $config
 */
function rec_xianjian_set_side_render_js_code($render_div_id, $config)
{
    global $rec_xianjian_side_js_code_map;
    $scene_id = isset($config['sceneId']) ? $config['sceneId'] : '';
    $rec_xianjian_js_code = rec_xianjian_get_js_code($render_div_id, $config);
    $code_dic = array();
    $code_dic['used'] = false;
    $code_dic['code'] = $rec_xianjian_js_code;
    $code_dic['title'] = isset($config['recomTitle']) ? $config['recomTitle'] : "";
    $rec_xianjian_side_js_code_map[$scene_id] = $code_dic;
}

/**
 * @param int $render_div_id
 * @param array $config
 */
function rec_xianjian_set_home_side_render_js_code($render_div_id, $config)
{
    global $rec_xianjian_home_side_js_code_map;
    $scene_id = isset($config['sceneId']) ? $config['sceneId'] : '';
    $rec_xianjian_js_code = rec_xianjian_get_js_code($render_div_id, $config);
    $code_dic = array();
    $code_dic['used'] = false;
    $code_dic['code'] = $rec_xianjian_js_code;
    $code_dic['title'] = isset($config['recomTitle']) ? $config['recomTitle'] : "";
    $rec_xianjian_home_side_js_code_map[$scene_id] = $code_dic;
}

/**
 * @param array $args
 * @param int $render_div_id
 */
function insert_rec_xianjian_js($args, $render_div_id)
{
    global $rec_xianjian_js_code_map;
    echo $args['before_widget'];
    echo $args['before_title'] . __('先荐', 'rec_xianjian') . $args['after_title'];
    echo isset($rec_xianjian_js_code_map[$render_div_id]) ? $rec_xianjian_js_code_map[$render_div_id] : '';
    echo $args['after_widget'];
}

/**
 *
 * @param array $args
 * @param int $render_div_id
 * @return bool
 */
function insert_rec_xianjian_side_js($args, $render_div_id)
{
    global $rec_xianjian_side_js_code_map;
    if (empty($rec_xianjian_side_js_code_map) || !is_array($rec_xianjian_side_js_code_map)) {
        return false;
    }
    $current_key = null;
    foreach ($rec_xianjian_side_js_code_map as $key => $code_dic) {
        if (!isset($code_dic['used'])) {
            $current_key = $key;
            $current_arr = $code_dic;
            break;
        }
    }
    if (empty($current_key)) {
        return false;
    }
    $title = isset($current_arr['title']) ? $current_arr['title'] : '';
    $code  = isset($current_arr['code']) ? $current_arr['code'] : '';
    $rec_xianjian_side_js_code_map[$current_key]['used'] = true;
    echo $args['before_widget'];
    echo $args['before_title'] . __($title, 'rec_xianjian') . $args['after_title'];
    echo $code;
    echo $args['after_widget'];
}

/**
 * @param array $args
 * @param int $render_div_id
 * @return bool
 */
function insert_rec_xianjian_home_side_js($args, $render_div_id)
{
    global $rec_xianjian_home_side_js_code_map;
    if (empty($rec_xianjian_home_side_js_code_map) || !is_array($rec_xianjian_home_side_js_code_map)) {
        return false;
    }
    $current_key = null;
    foreach ($rec_xianjian_home_side_js_code_map as $key => $code_dic) {
        if (!isset($code_dic['used'])) {
            $current_key = $key;
            $current_arr = $code_dic;
            break;
        }
    }
    if (empty($current_key)) {
        return false;
    }
    $title = isset($current_arr['title']) ? $current_arr['title'] : '';
    $code  = isset($current_arr['code']) ? $current_arr['code'] : '';
    $rec_xianjian_home_side_js_code_map[$current_key]['used'] = true;
    echo $args['before_widget'];
    echo $args['before_title'] . __($title, 'rec_xianjian') . $args['after_title'];
    echo $code;
    echo $args['after_widget'];
}