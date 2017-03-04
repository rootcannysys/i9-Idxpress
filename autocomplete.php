<?php
add_action('init', array('i9_autocomplete', 'RegisterScripts'));

class i9_autocomplete {

	public static function RegisterScripts() {

		if (defined('DOING_CRON') && DOING_CRON)

			return;

		

		// register auto-complete script for use outside the plugin

		wp_register_script('i9idx-autocomplete', plugins_url('js/autocomplete.js?v1.0', __FILE__), array('jquery-ui-autocomplete'), i9_PLUGIN_VERSION, true);
		
		//wp_register_script('i9idx-login', plugins_url('js/logindata.js', __FILE__),array(), i9_PLUGIN_VERSION, true);
		
		//wp_register_script('google_maps_geocode_api', '//maps.googleapis.com/maps/api/js?key=AIzaSyBwc41AA_F20W7NYuaO7w5uLtItCpJyjsc&sensor=false&libraries=drawing,geometry');
		//wp_enqueue_script('google_maps_geocode_api', 'https://maps.googleapis.com/maps/api/js?sensor=false');
  		//wp_enqueue_script('google_jsapi','https://www.google.com/jsapi'); 
		
	}
	

	public static function AddScripts($needs_plugin_url = false) {

		wp_enqueue_script('i9idx-autocomplete');
		
		//wp_enqueue_script('google_maps_geocode_api');
		//wp_enqueue_script('google_jsapi');
		
		//wp_enqueue_script('i9idx-login');
		
		//wp_enqueue_script('bootstrap', plugins_url('js/bootstrap.min.js', __FILE__));

		if ($needs_plugin_url) {

			$home_url   = get_home_url();

			//$plugin_url = i9_ApiRequest::MakePluginsUrlRelative(plugin_dir_url(__FILE__));
			$plugin_url = plugin_dir_url(__FILE__);

			$qstr1 = preg_replace('/&sort=(\w*)/','',$_SERVER['QUERY_STRING']);
		
			$qrystr = $qstr1;

			echo <<<HTML


				<script type="text/javascript">

				if (typeof locali9idx == "undefined" || !locali9idx) { var locali9idx = {}; };

				locali9idx.pluginUrl = "{$plugin_url}";

				locali9idx.homeUrl = "{$home_url}";
				
				locali9idx.qrystr = "{$qrystr}";

				</script>
                
               <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyBwc41AA_F20W7NYuaO7w5uLtItCpJyjsc&sensor=false&libraries=drawing,geometry"></script> 
               


HTML;

		}

	}

}

