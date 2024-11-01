<?php $plug_url = plugins_url(); ?>
<script type="text/javascript">
	var _paradigm_is_detail_config = false;
	// 配置完成更新后的回调
	function paradigmPostCallBack(plugConfig) {
		if (plugConfig.type === 'modify') {
			if (plugConfig.showDetailConfig === 1) {
				// 打开详细配置画面
				_paradigm_is_detail_config = true;
				_paradigm_is_detail_config_sceneId = plugConfig.sceneId;
				var baseUrl =
					'<?php echo XIANJIAN_HOST?>' ||
					'https://nbrecsys.4paradigm.com'
				var siteId = '<?php echo XIANJIAN_ID?>' || ''
				const paradigm_detail_url = baseUrl +
					'/#/plugInBk/feDetailconfig?id=' + plugConfig.sceneId +
					'&type=wordpress&siteId=' + siteId + '&isOfficial=true'
				window.open(paradigm_detail_url, '_blank')
			} else {
				var phpUrl = window.location.origin + window.location.pathname;
				window.location.href = phpUrl +
					'?page=rec_xianjian_rec_options' + (plugConfig.showCodeDialog ? '&sceneId=' + plugConfig.sceneId : "")
			}

		}
	}

	function paradigmPostPlugConfigToWordPress(dic, callBack) {
		if (dic == "") {
			return;
		}
		var form = document.getElementById("rec_xianjian-form");

		var input = document.getElementById('rec_xianjian-input');
		input.value = "true";

		var input_config = document.getElementById('rec_xianjian-config');
		input_config.value = JSON.stringify(dic);
		form.appendChild(input_config);

		form.submit();
	}
	function paradigmPostResetMate() {
        var form = document.getElementById("rec_xianjian-form");
		var input = document.getElementById('rec_xianjian-reset');
		input.value = "true";
		form.submit();
    }
</script>
<?php
defined('ABSPATH') || die();
if (!empty($_POST) && strcmp($_POST['rec_xianjian-input'], "true") == 0) {
    if (check_admin_referer('rec_xianjian-nonce') && current_user_can('manage_options')) {
        $paradimg_config_key = "paradigm_render_config";
        $new_config_str      = $_POST['rec_xianjian-config'];
        $new_config_str      = sanitize_text_field($new_config_str);
        if (is_string($new_config_str)) {
            $new_config_str      = stripslashes($new_config_str);
            $new_config          = json_decode($new_config_str, true);
            $original_config_str = get_option($paradimg_config_key);
            $original_config     = null;
            if ($original_config_str == '') {
                $original_config = array();
            } else {
                $original_config = json_decode($original_config_str, true);
            }
            $scene_id = $new_config['sceneId'];
            $type     = $new_config['type'];
            if (strcmp('delete', $type) == 0) {
                $delete_arr      = array($scene_id => "1");
                $original_config = array_diff_key($original_config, $delete_arr);
            } elseif (strcmp('modify', $type) == 0) {
                $original_config[$scene_id] = $new_config;
            }
            $total_config_str = json_encode($original_config);
            // if (strlen($total_config_str) > 5) {
            update_option($paradimg_config_key, $total_config_str);
            // }
            echo "<script>paradigmPostCallBack(" . $new_config_str . ")</script>";
        }
    }
}
if (!empty($_POST) && strcmp($_POST['rec_xianjian-reset'], "true") == 0) {
    global $rec_xianjian_access_token;
    $last_upload_id_key = 'last_upload_id_' . $rec_xianjian_access_token;
    update_option($last_upload_id_key, 0);
}
?>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<input hidden id='paradigm_sitId' value='<?php  echo XIANJIAN_ID; ?>'>
	<input hidden id='paradigm_wpVersion' value='<?php global $wp_version;echo $wp_version ?>'>
	<input hidden id='paradigm_plugChannel' value='<?php echo XIANJIAN_CHANNEL?>'>
	<input hidden id='paradigm_baseUrl' value='<?php echo XIANJIAN_HOST;?>'>
</head>

<body>
	<form id="rec_xianjian-form" action="#" method="post" name="setting" target="">
		<input type="hidden" name="rec_xianjian-input" id="rec_xianjian-input">
		<input type="hidden" name="rec_xianjian-config" id="rec_xianjian-config">
		<input type="hidden" name="rec_xianjian-reset" id="rec_xianjian-reset">
		<?php wp_nonce_field("rec_xianjian-nonce"); ?>
	</form>
</body>