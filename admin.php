<?php

add_action("admin_init", array("i9SearchAgent_Admin", "Initialize"));

add_Action("admin_enqueue_scripts", array("i9SearchAgent_Admin", "Enqueue"));

add_action("admin_menu", array("i9SearchAgent_Admin", "AddMenu"), 40);

add_action("admin_notices", array("i9SearchAgent_Admin", "DisplayAdminNotices"));

add_action("wp_ajax_i9idxpress-dismiss-notification", array("i9SearchAgent_Admin", "DismissNotification"));

//add_filter("manage_nav-menus_columns", array("i9SearchAgent_Admin", "CreateLinkBuilderMenuWidget"), 9);

//add_action("admin_print_scripts", array("i9SearchAgent_Admin", "SetPluginUri"));


if (defined('ZPRESS_API') && ZPRESS_API != '') {

	add_filter('nav_menu_items_zpress-page', array('i9SearchAgent_Admin', 'NavMenus'));

}

class i9SearchAgent_Admin {

	static $HeaderLoaded = null;

	static $capabilities = array();

	static function AddMenu() {

	$options = get_option(i9_OPTION_NAME);

		i9SearchAgent_Admin::GenerateAdminMenus(i9_PLUGIN_URL . 'images/i9-icon.png');

		//i9SearchAgent_Admin::GenerateAdminSubMenus();

		//add_filter("mce_external_plugins", array("i9SearchAgent_Admin", "AddTinyMcePlugin"));

		//add_filter("mce_buttons", array("i9SearchAgent_Admin", "RegisterTinyMceButton"));

		// won't work until this <http://core.trac.wordpress.org/ticket/12207> is fixed

		//add_filter("tiny_mce_before_init", array("i9SearchAgent_Admin", "ModifyTinyMceSettings"));

	}

	static function GenerateAdminMenus($icon_url){		

		add_menu_page('I-9 IDX', 'I-9 IDX', "manage_options", "i9xpress", "", $icon_url);


		$activationPage = add_submenu_page("i9xpress", "IDX Activation", "Activation", "manage_options", "i9xpress", array("i9SearchAgent_Admin", "Activation"));

		add_action("admin_print_scripts-{$activationPage}", array("i9SearchAgent_Admin", "LoadHeader"));

	}

	

	static function GenerateAdminSubMenus() {

		$options = get_option(i9_OPTION_NAME);



		if (isset($options["Activated"])) {

			$optionsPage = add_submenu_page("i9xpress", "IDX Options", "General", "manage_options", "i9idxpress-options", array("i9SearchAgent_Admin", "EditOptions"));

			add_action("admin_print_scripts-{$optionsPage}", array("i9SearchAgent_Admin", "LoadHeader"));

		}

		

		if (isset($options["Activated"])) {

			$filtersPage = add_submenu_page("i9xpress", "IDX Filters", "Filters", "manage_options", "i9idxpress-filters", array("i9SearchAgent_Admin", "FilterOptions"));

			add_action("admin_print_scripts-{$filtersPage}", array("i9SearchAgent_Admin", "LoadHeader"));

		}

		

		if (isset($options["Activated"])) {

			$seoSettingsPage = add_submenu_page("i9xpress", "IDX SEO Settings", "SEO Settings", "manage_options", "i9idxpress-seo-settings", array("i9SearchAgent_Admin", "SEOSettings"));

			add_action("admin_print_scripts-{$seoSettingsPage}", array("i9SearchAgent_Admin", "LoadHeader"));

		}

		

		if (isset($options["Activated"])) {

			$xmlSitemapsPage = add_submenu_page("i9xpress", "IDX XML Sitemaps", "XML Sitemaps", "manage_options", "i9idxpress-xml-sitemaps", array("i9SearchAgent_Admin", "XMLSitemaps"));

			add_action("admin_print_scripts-{$xmlSitemapsPage}", array("i9SearchAgent_Admin", "LoadHeader"));

		}

		

		if (isset($options["Activated"])) {

			$detailsPage = add_submenu_page("i9xpress", "IDX Details", "More Options", "manage_options", "i9idxpress-details", array("i9SearchAgent_Admin", "DetailsOptions"));

			add_action("admin_print_scripts-{$detailsPage}", array("i9SearchAgent_Admin", "LoadHeader"));

		}

	}

	static function AddTinyMcePlugin($plugins) {

		$plugins["idxlisting"] = i9_PLUGIN_URL . "tinymce/single_listing/editor_plugin.js";

		$plugins["idxlistings"] = i9_PLUGIN_URL . "tinymce/multi_listings/editor_plugin.js";

		$plugins["idxlinkbuilder"] = i9_PLUGIN_URL . "tinymce/link_builder/editor_plugin.js";

		$plugins["idxquicksearch"] = i9_PLUGIN_URL . "tinymce/idx_quick_search/editor_plugin.js";

		

		return $plugins;

	}

	static function RegisterTinyMceButton($buttons) {

		array_push($buttons, "separator", "idxlisting", "idxlistings", "idxlinkbuilder", "idxquicksearch");

		return $buttons;

	}

	static function ModifyTinyMceSettings($settings) {

		$settings["wordpress_adv_hidden"] = 0;

		return $settings;

	}

	static function Initialize() {

		register_setting("i9xpress_activation", i9_OPTION_NAME, array("i9SearchAgent_Admin", "SanitizeOptions"));

		//register_setting("i9idxpress_options", i9_OPTION_NAME, array("i9SearchAgent_Admin", "SanitizeOptions"));

		//register_setting("i9idxpress_options", i9_API_OPTIONS_NAME, array("i9SearchAgent_Admin", "SanitizeApiOptions"));

		//register_setting("i9idxpress_api_options", i9_API_OPTIONS_NAME, array("i9SearchAgent_Admin", "SanitizeApiOptions"));

		//register_setting("i9idxpress_api_options", i9_OPTION_NAME, array("i9SearchAgent_Admin", "SanitizeOptions"));

		//register_setting("i9idxpress_xml_sitemap", i9_OPTION_NAME, array("i9SearchAgent_Admin", "SanitizeOptions"));

		/*$capabilities = i9_ApiRequest::FetchData('MlsCapabilities');

		if (isset($capabilities['body'])) {

			self::$capabilities = json_decode($capabilities['body'], true);

		}*/

	}

	static function Enqueue($hook) {

		//every admin should have admin-options.js cept i9idx_footer-util

		if(!isset($_GET['page'])){	

			wp_enqueue_script('i9idxpress_admin_options', plugins_url('js/admin-options.js', __FILE__ ), array(), i9_PLUGIN_VERSION, true);	

		}

		

		if (isset($_GET['page']) && ($_GET['page'] == 'i9idxpress-details' || $_GET['page'] == 'i9idxpress-seo-settings' || $_GET['page'] == 'i9idxpress-options' || $_GET['page'] == 'i9idxpress-xml-sitemaps')) {

			wp_enqueue_script('i9idxpress_admin_options', plugins_url('js/admin-options.js', __FILE__ ), array(), i9_PLUGIN_VERSION, true);		

		}

		

		//We need the options script loaded in the header for this page

		if (isset($_GET['page']) && $_GET['page'] == 'i9idxpress-xml-sitemaps') {

			wp_enqueue_script('i9idxpress_admin_options', plugins_url('js/admin-options.js', __FILE__ ), array(), i9_PLUGIN_VERSION);		

		}

		

		if (isset($_GET['page']) && $_GET['page'] == 'i9idxpress-filters') {

			wp_enqueue_script('i9idxpress_admin_filters', plugins_url('js/admin-filters.js', __FILE__ ), array(), i9_PLUGIN_VERSION);		

		}



		if ($hook == 'nav-menus.php' || $hook == 'post.php' || $hook == 'post-new.php') {

			//wp_enqueue_script('i9idxpress_google_maps_geocode_api', '//maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing,geometry');
			wp_enqueue_script('i9idxpress_google_maps_geocode_api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCnB7KK9xwqmXUEe8Ak-k7sdb9L5t-YzT0&v=3.exp&sensor=true&libraries=drawing');

			wp_enqueue_script('i9idxpress_admin_utilities', plugins_url('js/admin-utilities.js', __FILE__ ), array(), i9_PLUGIN_VERSION, true);

			wp_localize_script('i9idxpress_admin_utilities', 'mlsCapabilities', self::$capabilities);

			wp_enqueue_style('i9idxpress_admin_options_style', plugins_url('css/admin-options.css?v1.0', __FILE__ ), array(), i9_PLUGIN_VERSION);

			wp_enqueue_script( 'jquery-ui-autocomplete', plugins_url('js/autocomplete.js', __FILE__ ), array( 'jquery-ui-widget', 'jquery-ui-position' ), '1.10.4' );

		}



		if (($hook == 'post.php' && $_GET['action'] == 'edit') || $hook == 'post-new.php' && $_GET['post_type'] == 'i9-idx-listings-page') {

			wp_enqueue_style('i9idxpress_admin_options_style', plugins_url('css/admin-options.css?v1.0', __FILE__ ), array(), i9_PLUGIN_VERSION);

		}

	}



	static function SetPluginUri(){

		$pluginUrl = i9_PLUGIN_URL;

		echo <<<HTML

			<script type="text/javascript">

				var i9idxPluginUri = "$pluginUrl";

			</script>

HTML;

	}

	static function LoadHeader() {

		if (self::$HeaderLoaded)

			return;



		$pluginUrl = i9_PLUGIN_URL;

		echo <<<HTML

			<link rel="stylesheet" href="{$pluginUrl}css/admin-options.css?v1.0" type="text/css" />

HTML;

		self::$HeaderLoaded = true;

	}

	static function DisplayAdminNotices() {

		if (!current_user_can("manage_options") || (defined('ZPRESS_API') && ZPRESS_API != ''))

			return;



		$options = get_option(i9_OPTION_NAME);

		

		if (!isset($options["PrivateApiKey"])) {

			echo <<<HTML

				<div class="error">

					<p style="line-height: 1.6;">

						In order to use the I-9 IDXpress plugin, you need to add your

						<a href="https://www.i9techus.com/pricing.html" target="_blank">activation key</a> to the

						<a href="admin.php?page=i9xpress">I-9 IDXpress activation area</a>.

					</p>

				</div>

HTML;

		} else if (isset($options["PrivateApiKey"]) && empty($options["Activated"])) {

			echo <<<HTML

				<div class="error">

					<p style="line-height: 1.6;">

						It looks like there may be a problem with the I-9 IDXpress that's installed on this blog.

						Please take a look at the <a href="admin.php?page=i9xpress">I-9 IDXpress diagnostics area</a>

						to find out more about any potential issues

					</p>

				</div>

HTML;

		} else if (isset($options["Activated"]) && empty($options["HideIntroNotification"])) {

			wp_nonce_field("i9idxpress-dismiss-notification", "i9idxpress-dismiss-notification", false);

			echo <<<HTML

				<script>

					function i9idxpressDismiss() {

						jQuery.post(ajaxurl, {

							action: 'i9idxpress-dismiss-notification',

							_ajax_nonce: jQuery('#i9idxpress-dismiss-notification').val()

						});

						jQuery('#i9idxpress-intro-notification').slideUp();

					}

				</script>

				<div id="i9idxpress-intro-notification" class="updated">

					<p style="line-height: 1.6;">Now that you have the <strong>I-9 IDXpress plugin</strong>

						activated, you'll probably want to start adding <strong>live MLS content</strong>

						to your site right away. The easiest way to get started is to use the three new IDX widgets that have

						been added to your <a href="widgets.php">widgets page</a> and the two new IDX icons

						(they look like property markers) that have been added to the visual editor for

						all of your <a href="post-new.php?post_type=page">pages</a> and <a href="post-new.php">posts</a>.

						You'll probably also want to check out our <a href="#"

							target="_blank">I-9 IDXpress virtual page link structure guide</a> so that you

						can start linking to the property listings and property details pages throughout

						your blog. Finally, you may also want to hop over to our

						<a href="#" target="_blank">help desk</a> or our

						<a href="#" target="_blank">forum</a>.

					</p>

					<p style="line-height: 1.6; text-align: center; font-weight: bold;">Take a look at the

						<a href="#" target="_blank">I-9 IDXpress getting

						started guide</a> for more info.

					</p>

					<p style="text-align: right;">(<a href="javascript:void(0)" onclick="i9idxpressDismiss()">dismiss this message</a>)</p>

				</div>

HTML;

		}

	}

	static function DismissNotification() {

		$action = $_POST["action"];

		check_ajax_referer($action);



		$options = get_option(i9_OPTION_NAME);

		$options["HideIntroNotification"] = true;

		update_option(i9_OPTION_NAME, $options);

		die();

	}

	

	static function EditOptions() {

		$options = get_option(i9_OPTION_NAME);



		$apiHttpResponse = i9_ApiRequest::FetchData("AccountOptions", array(), false, 0);

		if (!empty($apiHttpResponse["errors"]) || $apiHttpResponse["response"]["code"] != "200")

			wp_die("We're sorry, but we ran into a temporary problem while trying to load the account data. Please check back soon.", "Account data load error");

		else

			$account_options = json_decode($apiHttpResponse["body"]);



		$urlBase = get_home_url();

		if (substr($urlBase, strlen($urlBase), 1) != "/") $urlBase .= "/";

		$urlBase .= dsSearchAgent_Rewrite::GetUrlSlug();

?>

	<div class="wrap metabox-holder">

		<h1>General Options</h1>

		<?php if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true') : ?>

		<div class="updated"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>

		<?php endif; ?>

		<form method="post" action="options.php" onsubmit="return i9idxpressOptions.FilterViews();">

		<?php settings_fields("i9idxpress_options"); ?>

		<h2>Display Settings</h2>

			<table class="form-table">

				<?php if(!defined('ZPRESS_API') || ZPRESS_API == '') : ?>

				<tr>

					<th>

						<label for="i9idxpress-DetailsTemplate">Template for details pages:</label>

					</th>

					<td>

						<select id="i9idxpress-DetailsTemplate" name="<?php echo i9_OPTION_NAME ; ?>[DetailsTemplate]">

							<option value="">- Default -</option>

							<?php

								$details_template = (isset($options["DetailsTemplate"])) ? $options["DetailsTemplate"] : '';

								page_template_dropdown($details_template);

							?>

						</select><br />

						<span class="description">Some themes have custom templates that can change how a particular page is displayed. If your theme does have multiple templates, you'll be able to select which one you want to use in the drop-down above.</span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-ResultsTemplate">Template for results pages:</label>

					</th>

					<td>

						<select id="i9idxpress-ResultsTemplate" name="<?php echo i9_OPTION_NAME ; ?>[ResultsTemplate]">

							<option value="">- Default -</option>

							<?php

								$results_template = (isset($options["ResultsTemplate"])) ? $options["ResultsTemplate"] : '';

								page_template_dropdown($results_template);

							?>

						</select><br />

						<span class="description">See above.</span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-AdvancedTemplate">Template for dsSearchAgent:</label>

					</th>

					<td>

						<select id="i9idxpress-AdvancedTemplate" name="<?php echo i9_OPTION_NAME ; ?>[AdvancedTemplate]">

							<option value="">- Default -</option>

							<?php

								$advanced_template = (isset($options["AdvancedTemplate"])) ? $options["AdvancedTemplate"] : '';

								page_template_dropdown($advanced_template);

							?>

						</select><br />

						<span class="description">See above.</span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-IDXTemplate">Template for IDX pages:</label>

					</th>

					<td>

						<select id="i9idxpress-IDXTemplate" name="<?php echo i9_OPTION_NAME ; ?>[IDXTemplate]">

							<option value="">- Default -</option>

							<?php

								$idx_template = (isset($options["IDXTemplate"])) ? $options["IDXTemplate"] : '';

								page_template_dropdown($idx_template);

							?>

						</select><br />

						<span class="description">See above.</span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-404Template">Template for error pages:</label>

					</th>

					<td>

						<select id="i9idxpress-404Template" name="<?php echo i9_OPTION_NAME ; ?>[404Template]">

							<option value="">- Default -</option>

							<optgroup label="Template">

							<?php

								$error_template = (isset($options["404Template"])) ? $options["404Template"] : '';

								$error_404 = locate_template('404.php');

								if(!empty($error_404)){

							?>

							<option value="404.php"<?php echo ($error_template == '404.php' ? ' selected' : ''); ?>>404.php</option>

							<?php

								}

							?>

							<?php

								$error_template = (isset($options["404Template"])) ? $options["404Template"] : '';

								page_template_dropdown($error_template);

							?>

							</optgroup>

							<optgroup label="Page">

							<?php

								$pages = get_posts(

									array(

										'post_type' => 'page',

										'posts_per_page' => -1

									)

								);

								foreach( $pages as $page ){

									echo '<option value="' . $page->ID . '"' . ( $error_template == $page->ID ? ' selected' : '' ) . '>' . $page->post_title . '</option>';

								}

								wp_reset_postdata();

							?>

							</optgroup>

						</select><br />

						<span class="description">See above.</span>

					</td>

				</tr>

				<?php endif; ?>

				<tr>

					<th>

						<label for="i9idxpress-CustomTitleText">Title for results pages:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-CustomTitleText" maxlength="49" name="<?php echo i9_API_OPTIONS_NAME; ?>[CustomTitleText]" value="<?php echo $account_options->CustomTitleText; ?>" /><br />

						<span class="description">By default, the titles are auto-generated based on the type of area searched. You can override this above; use <code>%title%</code> to designate where you want the location title. For example, you could use <code>Real estate in the %title%</code>.</span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-ResultsMapDefaultState">Default view for Results pages:</label>

					</th>

					<td>

						<input type="radio" id="i9idxpress-ResultsDefaultState-List" name="<?php echo i9_OPTION_NAME; ?>[ResultsDefaultState]" value="list" <?php echo @$options["ResultsDefaultState"] == "list" || !isset($options["ResultsDefaultState"]) ? "checked=\"checked\"" : "" ?>/> <label for="i9idxpress-ResultsDefaultState-List">List</label><br />

						<input type="radio" id="i9idxpress-ResultsDefaultState-ListMap" name="<?php echo i9_OPTION_NAME; ?>[ResultsDefaultState]" value="listmap" <?php echo @$options["ResultsDefaultState"] == "listmap" ? "checked=\"checked\"" : "" ?> /> <label for="i9idxpress-ResultsDefaultState-ListMap">List + Map</label>

						<?php if (defined('ZPRESS_API') || isset($options["i9idxPressPackage"]) && $options["i9idxPressPackage"] == "pro"): ?>

						<br /><input type="radio" id="i9idxpress-ResultsDefaultState-Grid" name="<?php echo i9_OPTION_NAME; ?>[ResultsDefaultState]" value="grid" <?php echo @$options["ResultsDefaultState"] == "grid" ? "checked=\"checked\"" : "" ?>/> <label for="i9idxpress-ResultsDefaultState-Grid">Grid</label>

						<?php endif ?>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-ResultsMapDefaultState">Property Details Image Display:</label>

					</th>

					<td>

						<input type="radio" id="i9idxpress-ImageDisplay-Slideshow" name="<?php echo i9_OPTION_NAME; ?>[ImageDisplay]" value="slideshow" <?php echo @$options["ImageDisplay"] == "slideshow" || !isset($options["ImageDisplay"]) ? "checked=\"checked\"" : "" ?>/> <label for="i9idxpress-ImageDisplay-Slideshow">Rotating Slideshow</label><br />

						<input type="radio" id="i9idxpress-ImageDisplay-Thumbnail" name="<?php echo i9_OPTION_NAME; ?>[ImageDisplay]" value="thumbnail" <?php echo @$options["ImageDisplay"] == "thumbnail" ? "checked=\"checked\"" : "" ?> /> <label for="i9idxpress-ImageDisplay-Thumbnail">Thumbnail Display</label>

					</td>

				</tr>

			</table>

						<?php if (defined('ZPRESS_API') || isset($options["i9idxPressPackage"]) && $options["i9idxPressPackage"] == "pro"): ?>

			<h2>Registration Options</h2>

			<table class="form-table">

				<tr>

					<th>

						<label for="i9idxpress-RequiredPhone-check">Require phone numbers for visitor registration and contact forms</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequiredPhone" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequiredPhone]" value="<?php echo $account_options->{'RequiredPhone'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequiredPhone-check" <?php checked('true', strtolower($account_options->{'RequiredPhone'})); ?> />

					</td>

				</tr>

			</table>

			<h2>Forced Registration Settings</h2>

			<table class="form-table">

				<tr>

					<th>

						<label for="i9idxpress-NumofDetailsViews">Number of detail views before required registration</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-NumOfDetailsViews" name="<?php echo i9_API_OPTIONS_NAME; ?>[AllowedDetailViewsBeforeRegistration]" value="<?php echo $account_options->AllowedDetailViewsBeforeRegistration; ?>" />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-NumofResultsViews">Number of result views before required registration</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-NumOfResultViews" name="<?php echo i9_API_OPTIONS_NAME;?>[AllowedSearchesBeforeRegistration]" value="<?php echo $account_options->AllowedSearchesBeforeRegistration; ?>" />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Details-Description-check">Require login to view description</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Details-Description" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Details-Description]" value="<?php echo $account_options->{'RequireAuth-Details-Description'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Details-Description-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Details-Description'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Property-Community">Require login to view the community</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Property-Community" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Property-Community]" value="<?php echo $account_options->{'RequireAuth-Property-Community'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Property-Community-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Property-Community'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Property-Tract-check">Require login to view the tract</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Property-Tract" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Property-Tract]" value="<?php echo $account_options->{'RequireAuth-Property-Tract'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Property-Tract-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Property-Tract'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Details-Schools-check">Require login to view schools</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Details-Schools" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Details-Schools]" value="<?php echo $account_options->{'RequireAuth-Details-Schools'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Details-Schools-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Details-Schools'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Details-AdditionalInfo-check">Require login to view additional info</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Details-AdditionalInfo" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Details-AdditionalInfo]" value="<?php echo $account_options->{'RequireAuth-Details-AdditionalInfo'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Details-AdditionalInfo-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Details-AdditionalInfo'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Details-PriceChanges-check">Require login to view price changes</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Details-PriceChanges" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Details-PriceChanges]" value="<?php echo $account_options->{'RequireAuth-Details-PriceChanges'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Details-PriceChanges-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Details-PriceChanges'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Details-Features-check">Require login to view features</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Details-Features" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Details-Features]" value="<?php echo $account_options->{'RequireAuth-Details-Features'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Details-Features-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Details-Features'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Property-DaysOnMarket-check">Require login to view days on market</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Property-DaysOnMarket" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Property-DaysOnMarket]" value="<?php echo $account_options->{'RequireAuth-Property-DaysOnMarket'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Property-DaysOnMarket-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Property-DaysOnMarket'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Property-LastUpdated-check">Require login to view last update date</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Property-LastUpdated" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Property-LastUpdated]" value="<?php echo $account_options->{'RequireAuth-Property-LastUpdated'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Property-LastUpdated-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Property-LastUpdated'})); ?> />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-RequireAuth-Property-YearBuilt-check">Require login to view year built</label>

					</th>

					<td>

						<input type="hidden" id="i9idxpress-RequireAuth-Property-YearBuilt" name="<?php echo i9_API_OPTIONS_NAME; ?>[RequireAuth-Property-YearBuilt]" value="<?php echo $account_options->{'RequireAuth-Property-YearBuilt'}; ?>" />

						<input type="checkbox" class="i9idxpress-api-checkbox" id="i9idxpress-RequireAuth-Property-YearBuilt-check" <?php checked('true', strtolower($account_options->{'RequireAuth-Property-YearBuilt'})); ?> />

					</td>

				</tr>

			</table>

			<?php endif ?>

			<?php if(!defined('ZPRESS_API') || ZPRESS_API == '') : ?>

			<h2>Contact Information</h2>

			<span class="description">This information is used in identifying you to the website visitor. For example: Listing PDF Printouts, Contact Forms, and Dwellicious</span>

			<table class="form-table">

				<tr>

					<th>

						<label for="i9idxpress-FirstName">First Name:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-FirstName" maxlength="49" name="<?php echo i9_API_OPTIONS_NAME; ?>[FirstName]" value="<?php echo $account_options->FirstName; ?>" /><br />

						<span class="description"></span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-LastName">Last Name:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-LastName" maxlength="49" name="<?php echo i9_API_OPTIONS_NAME; ?>[LastName]" value="<?php echo $account_options->LastName; ?>" /><br />

						<span class="description"></span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-Email">Email:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-Email" maxlength="49" name="<?php echo i9_API_OPTIONS_NAME; ?>[Email]" value="<?php echo $account_options->Email; ?>" /><br />

						<span class="description"></span>

					</td>

				</tr>

			</table>

			

			<h2>Copyright Settings</h2>

			<span class="description">This setting allows you to remove links to <a href="https://www.i9techus.com">Diverse Solutions</a> that are included in the IDX disclaimer.</span>

			<table class="form-table">

				<tr>

					<th>

						<label for="i9idxpress-RemoveDsDisclaimerLinks">Remove Diverse Solutions links</label>

					</th>

					<td>

						<input type="checkbox" id="i9idxpress-RemoveDsDisclaimerLinks" name="<?php echo i9_OPTION_NAME; ?>[RemoveDsDisclaimerLinks]" value="Y"<?php if (isset($options['RemoveDsDisclaimerLinks']) && $options['RemoveDsDisclaimerLinks'] == 'Y'): ?> checked="checked"<?php endif ?> />

					</td>

				</tr>

			</table>

			<?php endif; ?>

			<h2>Mobile Settings</h2>

			<span class="description">To set up a custom mobile domain you must configure your DNS to point a domain, or subdomain, at app.dsmobileidx.com. Then enter the custom domain's full url here. Example: http://mobile.myrealestatesite.com</span>

			<table class="form-table">

				<tr>

					<th>

						<label for="i9idxpress-MobileSiteUrl">Custom Mobile Domain:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-MobileSiteUrl" maxlength="100" name="<?php echo i9_API_OPTIONS_NAME; ?>[MobileSiteUrl]" value="<?php echo $account_options->MobileSiteUrl; ?>" />

					</td>

				</tr>

			</table>

			<h2>My Listings</h2>

			<span class="description">When filled in, these settings will make pages for "My Listings" and "My Office Listings" available in your navigation menus page list.</span>

			<table class="form-table">

				<tr>

					<th>

						<label for="i9idxpress-AgentID">Agent ID:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-AgentID" maxlength="35" name="<?php echo i9_OPTION_NAME; ?>[AgentID]" value="<?php echo (!empty($options['AgentID']) ? $options['AgentID'] : $account_options->AgentID); ?>" /><br />

						<span class="description">This is the Agent ID as assigned to you by the MLS you are using to provide data to this site.</span>

						<input type="hidden" id="i9idxpress-API-AgentID" maxlength="35" name="<?php echo i9_API_OPTIONS_NAME; ?>[AgentID]" value="<?php echo (!empty($options['AgentID']) ? $options['AgentID'] : $account_options->AgentID); ?>" /><br />

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-OfficeID">Office ID:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-OfficeID" maxlength="35" name="<?php echo i9_OPTION_NAME; ?>[OfficeID]" value="<?php echo (!empty($options['OfficeID']) ? $options['OfficeID'] : $account_options->OfficeID); ?>" /><br />

						<span class="description">This is the Office ID as assigned to your office by the MLS you are using to provide data to this site.</span>

						<input type="hidden" id="i9idxpress-API-OfficeID" maxlength="35" name="<?php echo i9_API_OPTIONS_NAME; ?>[OfficeID]" value="<?php echo (!empty($options['OfficeID']) ? $options['OfficeID'] : $account_options->OfficeID); ?>" /><br />

					</td>

				</tr>

			</table>

			<?php if((!defined('ZPRESS_API') || ZPRESS_API == '') && isset($account_options->EnableMemcacheIni9idxPress) && strtolower($account_options->EnableMemcacheIni9idxPress) == "true") {?>

			<h2>Memcache Options</h2>

			<?php if(!class_exists('Memcache') && !class_exists('Memcached')) {?>

			<span class="description">Warning PHP is not configured with a Memcache module. See <a href="http://www.php.net/manual/en/book.memcache.php" target="_blank">here</a> or <a href="http://www.php.net/manual/en/book.memcached.php" target="_blank">here</a> to implement one.</span>

			<?php }?>

			<table class="form-table">

				<tr>

					<th>

						<label for="i9idxpress-MemcacheHost">Host:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-MemcacheHost" maxlength="49" name="<?php echo i9_OPTION_NAME; ?>[MemcacheHost]" value="<?php echo @$options["MemcacheHost"]; ?>" /><br />

						<span class="description"></span>

					</td>

				</tr>

				<tr>

					<th>

						<label for="i9idxpress-MemcachePort">Port:</label>

					</th>

					<td>

						<input type="text" id="i9idxpress-MemcachePort" maxlength="49" name="<?php echo i9_OPTION_NAME; ?>[MemcachePort]" value="<?php echo @$options["MemcachePort"]; ?>" /><br />

						<span class="description"></span>

					</td>

				</tr>

			</table>

			<?php }?>

			<p class="submit">

				<input type="submit" class="button-primary" name="Submit" value="Save Options" />

			</p>

		</form>

	</div><?php

	}



	static function Activation() {

		$options = get_option(i9_OPTION_NAME);

		//print_r($options);die;

		if (@$options["PrivateApiKey"]) {

			$diagnostics = self::RunDiagnostics($options);

			//print_r($diagnostics);die;

			$previous_options  = (isset($options["Activated"])) ? $options["Activated"] : '';

			$previous_options .= (isset($options["HasSearchAgentPro"])) ? '|'.$options["HasSearchAgentPro"] : '';

			$previous_options .= (isset($options["DetailsRequiresRegistration"])) ? '|'.$options["DetailsRequiresRegistration"] : '';

			$new_options = $diagnostics["DiagnosticsSuccessful"].'|'.$diagnostics["HasSearchAgentPro"].'|'.$diagnostics["DetailsRequiresRegistration"];


			$options["Activated"] = $diagnostics["DiagnosticsSuccessful"];

			$options["HasSearchAgentPro"] = $diagnostics["HasSearchAgentPro"];

			$options["DetailsRequiresRegistration"] = $diagnostics["DetailsRequiresRegistration"];

			if ($previous_options != $new_options)

				update_option(i9_OPTION_NAME, $options);

			//$formattedApiKey = $options["AccountID"] . "/" . $options["SearchSetupID"] . "/" . $options["PrivateApiKey"];
			$formattedApiKey = $options["AccountID"]."/".$options["PrivateApiKey"];

		}

?>



	<div class="wrap metabox-holder">

		<h1>IDX Activation</h1>

		<form method="post" action="options.php">

			<?php settings_fields("i9xpress_activation"); ?>

			<h2>Plugin activation</h2>

			<p>

				In order to use <i><a href="https://i9techus.com/blog.php#wtisIdx" target="_blank">I-9 IDXpress</a></i>

				to display real estate listings from the MLS on your blog, you must have an activation key from

				<a href="https://www.i9techus.com/" target="_blank">I-9 Technologies</a>. Without it, the plugin itself

				will be useless, widgets won't appear, and all "shortcodes" specific to this plugin in your post and page

				content will be hidden when that content is displayed on your blog. If you already have this activation key, enter it

				below and you can be on your way.

			</p>

			<p>

				If you <b>don't</b> yet have an activation key, you can purchase one from us

				(<a href="https://www.i9techus.com/pricing.html" target="_blank">I-9 Technologies</a>) for a monthly price that

				varies depending on the MLS you belong to. Furthermore, in order for us to authorize the data to be transferred

				from us to your blog, you <b>must</b> be a member of the MLS you would like the data for. In some cases, you

				even have to be a real estate broker (or have your broker sign off on your request for this data). If you're 1)

				a real estate agent, and 2) a member of an MLS, and you're interested in finding out more, please

				<a href="https://i9techus.com/contact.php" target="_blank">contact us</a>.

			</p>

			<div id="i9idx-activation-notice">

				<p>

					By default, <strong>your activation key will only work on one blog at a time</strong>. If you'd like to make it

					work on more than one blog, you need to <a href="https://i9techus.com/contact.php" target="_blank">contact our sales department</a>.

				</p>

				<p>

					<strong>If you activate I-9 IDXpress on this blog, I-9 IDXpress will immediately stop working on any other blogs you use

					this plugin on!</strong>

				</p>

			</div>

			<table class="form-table">

				<tr>

					<th style="width: 110px;">

						<label for="option-FullApiKey">Activation key:</label>

					</th>

					<td>

						<input type="text" id="option-FullApiKey" maxlength="100" name="<?php echo i9_OPTION_NAME; ?>[FullApiKey]" value="<?php echo @$formattedApiKey ?>" />

						</td>

						</tr>

						<tr>

						<th style="width: 110px;">Current status:</th>

						<td class="i9idx-status i9idx-<?php echo @$diagnostics["DiagnosticsSuccessful"] ? "success" : "failure" ?>">

						** <?php echo @$diagnostics && @$diagnostics["DiagnosticsSuccessful"] ? "ACTIVE" : "INACTIVE" ?> **

						</td>

						</tr>

						</table>

						<p class="submit">

						<input type="submit" class="button-primary" name="Submit" value="Activate Plugin For This Blog / Server" />

						</p>

						<?php

						if (@$diagnostics) {

						?>

			<h2>Diagnostics</h2>

<?php

if (isset($diagnostics["error"])) {

?>

			<p class="error">

				It seems that there was an issue while trying to load the diagnostics from Diverse Solutions' servers. It's possible that our servers

				are temporarily down, so please check back in just a minute. If this problem persists, please

				<a href="https://www.i9techus.com" target="_blank">contact us</a>.

			</p>

<?php

} else {

?>

			<table class="form-table" style="margin-bottom: 15px;">

				<tr>

					<th style="width: 230px;">

						<a>Account active?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["IsAccountValid"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["IsAccountValid"] ? "Yes" : "No" ?>

					</td>

					<th style="width: 290px;">

					<a>Activation key active?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["IsApiKeyValid"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["IsApiKeyValid"] ? "Yes" : "No" ?>

					</td>

					</tr>

					<tr>

					<th>

					<a>Account authorized for this MLS?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["IsAccountAuthorizedToMLS"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["IsAccountAuthorizedToMLS"] ? "Yes" : "No" ?>

					</td>

					<th>

					<a>Activation key authorized for this blog?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["IsApiKeyAuthorizedToUri"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["IsApiKeyAuthorizedToUri"] ? "Yes" : "No" ?>

					</td>

					</tr>

					<tr>

					<th>

					<a>Clock accurate on this server?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["ClockIsAccurate"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["ClockIsAccurate"] ? "Yes" : "No" ?>

					</td>



					<th>

					<a>Activation key authorized for this server?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["IsApiKeyAuthorizedToIP"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["IsApiKeyAuthorizedToIP"] ? "Yes" : "No" ?>

					</td>

					</tr>

					<tr>

					<th>

					<a>WordPress link structure ok?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["UrlInterceptSet"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["UrlInterceptSet"] ? "Yes" : "No" ?>

					</td>



					<th>

					<a>Under monthly API call limit?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["UnderMonthlyCallLimit"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["UnderMonthlyCallLimit"] ? "Yes" : "No" ?>

					</td>

					</tr>

					<tr>

					<th>

					<a>Server PHP version at least 5.2?</a>

					</th>

					<td class="i9idx-status i9idx-<?php echo $diagnostics["PhpVersionAcceptable"] ? "success" : "failure" ?>">

					<?php echo $diagnostics["PhpVersionAcceptable"] ? "Yes" : "No" ?>

					</td>



					<th>

					<a>Would you like fries with that?</a>

					</th>

					<td class="i9idx-status i9idx-success">

					Yes <!-- you kidding? we ALWAYS want fries. mmmm, friessssss -->

					</td>

					</tr>

					</table>

					<?php

				}

			}

			?>





		</form>

	</div>

	

	

	

	

<?php

	}



	static function FilterOptions() {

		$apiHttpResponse = i9_ApiRequest::FetchData("AccountOptions", array(), false, 0);

		if (!empty($apiHttpResponse["errors"]) || $apiHttpResponse["response"]["code"] != "200")

			wp_die("We're sorry, but we ran into a temporary problem while trying to load the account data. Please check back soon.", "Account data load error");

		else

			$account_options = json_decode($apiHttpResponse["body"]);

		$urlBase = get_home_url();



		$wp_options = get_option(i9_OPTION_NAME);



		$property_types = i9_ApiRequest::FetchData('AccountSearchSetupPropertyTypes', array(), false, 0);

		$default_types = i9_ApiRequest::FetchData('DefaultPropertyTypesNoCache', array(), false, 0);



		$property_types = json_decode($property_types["body"]);

		$default_types = json_decode($default_types["body"]);



		if (substr($urlBase, strlen($urlBase), 1) != "/") $urlBase .= "/";

			$urlBase .= dsSearchAgent_Rewrite::GetUrlSlug(); ?>

		<div class="wrap metabox-holder">

			<h1>Filters</h1>

			<?php if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true') : ?>

			<div class="updated"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>

			<?php endif; ?>

			<form method="post" action="options.php">

				<?php settings_fields("i9idxpress_api_options"); ?>

				<span class="description">These settings will filter results.</span>

				<table class="form-table">

					<tr>

						<th>

							<label for="i9idxpress-FirstName">Restrict Results to a Zipcode:</label>

						</th>

						<td>

							<textarea class="linkInputTextArea" id="i9idxpress-RestrictResultsToZipcode" name="<?php echo i9_API_OPTIONS_NAME; ?>[RestrictResultsToZipcode]"><?php echo preg_replace("/,/", "\n", $account_options->RestrictResultsToZipcode); ?></textarea><br />

							<span class="description">If you need/want to restrict i9idxpress to a specific zipcode, put the zipcode in this field. Separate a list of values by hitting the 'Enter' key after each entry.</span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-FirstName">Restrict Results to a City:</label>

						</th>

						<td>

							<textarea class="linkInputTextArea" id="i9idxpress-RestrictResultsToCity" name="<?php echo i9_API_OPTIONS_NAME; ?>[RestrictResultsToCity]"><?php echo preg_replace('/,/', "\n", $account_options->RestrictResultsToCity); ?></textarea><br />

							<span class="description">If you need/want to restrict i9idxpress to a specific city, put the name in this field. Separate a list of values by hitting the 'Enter' key after each entry. </span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-FirstName">Restrict Results to a County:</label>

						</th>

						<td>

							<textarea class="linkInputTextArea" id="i9idxpress-RestrictResultsToCounty" name="<?php echo i9_API_OPTIONS_NAME; ?>[RestrictResultsToCounty]"><?php echo preg_replace("/,/", "\n", $account_options->RestrictResultsToCounty); ?></textarea><br />

							<span class="description">If you need/want to restrict i9idxpress to a specific county, put the name in this field. Separate a list of values by hitting the 'Enter' key after each entry. </span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-FirstName">Restrict Results to a State:</label>

						</th>

						<td>

							<input type="hidden" class="linkInputTextArea" id="i9idxpress-RestrictResultsToState" name="<?php echo i9_API_OPTIONS_NAME; ?>[RestrictResultsToState]" value="<?php echo preg_replace("/,/", "\n", $account_options->RestrictResultsToState); ?>"></input>

							<select size="4" style="width:140px;" multiple="yes" class="linkInputTextArea"  id="i9idxpress-states" name="i9idxpress-states">

							<?php

							

							$states = array(

								"None"=>'',

								"Alabama"=>'AL',

								"Alaska"=>'AK',  

								"Arizona"=>'AZ',  

								"Arkansas"=>'AR',  

								"California"=>'CA',  

								"Colorado"=>'CO',  

								"Connecticut"=>'CT',  

								"Delaware"=>'DE',  

								"District of Columbia"=>'DC',  

								"Florida"=>'FL',  

								"Georgia"=>'GA',  

								"Hawaii"=>'HI',  

								"Idaho"=>'ID',  

								"Illinois"=>'IL',  

								"Indiana"=>'IN',  

								"Iowa"=>'IA',  

								"Kansas"=>'KS',  

								"Kentucky"=>'KY',  

								"Louisiana"=>'LA',  

								"Maine"=>'ME',  

								"Maryland"=>'MD',  

								"Massachusetts"=>'MA',  

								"Michigan"=>'MI',  

								"Minnesota"=>'MN',  

								"Mississippi"=>'MS',  

								"Missouri"=>'MO',  

								"Montana"=>'MT',

								"Nebraska"=>'NE',

								"Nevada"=>'NV',

								"New Hampshire"=>'NH',

								"New Jersey"=>'NJ',

								"New Mexico"=>'NM',

								"New York"=>'NY',

								"North Carolina"=>'NC',

								"North Dakota"=>'ND',

								"Ohio"=>'OH',  

								"Oklahoma"=>'OK',  

								"Oregon"=>'OR',  

								"Pennsylvania"=>'PA',  

								"Rhode Island"=>'RI',  

								"South Carolina"=>'SC',  

								"South Dakota"=>'SD',

								"Tennessee"=>'TN',  

								"Texas"=>'TX',  

								"Utah"=>'UT',  

								"Vermont"=>'VT',  

								"Virginia"=>'VA',  

								"Washington"=>'WA',  

								"West Virginia"=>'WV',  

								"Wisconsin"=>'WI',  

								"Wyoming"=>'WY');

							

							if(isset($account_options->RestrictResultsToState)) $selected_states = explode(',', $account_options->RestrictResultsToState);

							foreach ($states as $key => $value) {

								$opt_checked = "";

								if (isset($selected_states)) {

									foreach ($selected_states as $selected_state) {

										if (!empty($value) && $selected_state == $value) {

											$opt_checked = "selected='selected'";

											break;

										}

									}

								}

								echo '<option class="i9idxpress-states-filter" '.$opt_checked.' value="' . $value . '">' . $key . '</option>';

							}

							?>

							</select><br/>

						



							<span class="description">If you need/want to restrict i9idxpress to a specific state, put the abbreviation in this field. Separate a list of values by hitting the 'Enter' key after each entry. <a href="http://en.wikipedia.org/wiki/List_of_U.S._state_abbreviations" target="_blank">List of U.S. State Abbreviations</a></span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-FirstName">Restrict Results to a Property Type:</label>

						</th>

						<?php

							$default_values = array();

							foreach ($default_types as $default_type) {

								array_push($default_values, $default_type->SearchSetupPropertyTypeID);

							}

						?>

						<td>

							<input type="hidden" class="linkInputTextArea" id="i9idxpress-RestrictResultsToPropertyType" name="<?php echo i9_API_OPTIONS_NAME; ?>[RestrictResultsToPropertyType]" value="<?php echo $account_options->RestrictResultsToPropertyType; ?>"></input>

							<input type="hidden" class="linkInputTextArea" id="i9idxpress-DefaultPropertyType" name="<?php echo i9_API_OPTIONS_NAME; ?>[DefaultPropertyType]" value="<?php echo (count($default_values) > 0) ? implode(",", $default_values) : ""; ?>" />

							<table id="i9idxpress-property-types" name="i9idxpress-property-types">

									<tr>

										<td></td>

										<td>Filter</td>

										<td>Default</td>

									</tr>

									<?php

									$filter_types = explode(',', $account_options->RestrictResultsToPropertyType);

									foreach ($property_types as $property_type) {

										$name = htmlentities($property_type->DisplayName);

										$id = $property_type->SearchSetupPropertyTypeID;

										$filter_checked = "";

										$default_checked = "";

										foreach ($filter_types as $filter_type) {

											if ($filter_type == (string)$id) {

												$filter_checked = "checked";

												break;

											}

										}

										foreach ($default_types as $default_type) {

											if(htmlentities($default_type->SearchSetupPropertyTypeID) == (string)$id){

												$default_checked = "checked";

												break;

											}

										}

										?>

										<tr>

											<td><?php echo $name; ?></td>

											<td><input class="i9idxpress-proptype-filter" <?php echo $filter_checked; ?> type="checkbox" value="<?php echo $id; ?>"/></td>

											<td><input class="i9idxpress-proptype-default" <?php echo $default_checked; ?> type="checkbox" value="<?php echo $id; ?>"/></td>

										</tr>

										<?php

									}

								?>

							</table>

							<span class="description">If you need/want to restrict i9idxpress to specific property types, select the types you would like to have return results.  This setting will also restrict the property types shown in search form options.  You may also choose which types are included in the default property type selection.</span>

						</td>

					</tr>

					<?php if ($account_options->{'i9idxPress-Package'} == 'pro') : ?>

					<tr>

						<th>

							<label>Default Results by Status:</label>

						</th>

						<td>

							<input type="hidden" id="i9idxpress-DefaultListingStatusTypeIDs" name="<?php echo i9_API_OPTIONS_NAME; ?>[DefaultListingStatusTypeIDs]" value="<?php echo $account_options->DefaultListingStatusTypeIDs; ?>" />

							<table class="i9idxpress-status-types">

								<?php

								$listing_status_types = array('Active' => 1, 'Conditional' => 2, 'Pending' => 4, 'Sold' => 8);

								if (empty(self::$capabilities['HasSoldData'])) {

									unset($listing_status_types['Sold']);

								}

								if (empty(self::$capabilities['HasConditionalData'])) {

									unset($listing_status_types['Conditional']);

								}

								if (empty(self::$capabilities['HasPendingData'])) {

									unset($listing_status_types['Pending']);

								}

								foreach ($listing_status_types as $label => $value) :

									$status_checked = '';

									if (strpos($account_options->DefaultListingStatusTypeIDs, (string)$value) !== false) 

										$status_checked = 'checked';

									?>

									<tr>

										<td><?php echo $label.' '; ?></td>

										<td><input class="i9idxpress-statustype-filter" <?php echo $status_checked; ?> type="checkbox" value="<?php echo $value; ?>" /></td>

									</tr>

								<?php endforeach; ?>

							</table>

							<span class="description">If you need / want to restrict the properties shown on your website by property status, check the statuses you would like visitors to see by default in search results here</span>

						</td>

					</tr>

					<?php endif; ?>

				</table>

				<br />

				<p class="submit">

					<input type="submit" class="button-primary" name="Submit" value="Save Options" />

				</p>

			</form>

		</div><?php

	}

	

	static function SEOSettings() {

		$options = get_option(i9_OPTION_NAME);

		

		$apiHttpResponse = i9_ApiRequest::FetchData("AccountOptions", array(), false, 0);

		if (!empty($apiHttpResponse["errors"]) || $apiHttpResponse["response"]["code"] != "200")

			wp_die("We're sorry, but we ran into a temporary problem while trying to load the account data. Please check back soon.", "Account data load error");

		else

			$account_options = json_decode($apiHttpResponse["body"]);

		$urlBase = get_home_url();

		if (substr($urlBase, strlen($urlBase), 1) != "/") $urlBase .= "/";

			$urlBase .= dsSearchAgent_Rewrite::GetUrlSlug(); ?>

		<div class="wrap metabox-holder">

			<h1>SEO Settings</h1>

			<?php if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true') : ?>

			<div class="updated"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>

			<?php endif; ?>

			<form method="post" action="options.php">

			<?php settings_fields("i9idxpress_api_options"); ?>

			<span class="description">These settings are used to improve the accuracy of how search engines find and list this site.<br/>When using a replacement field please include it using lowercase characters.</span>

			<div style="padding-left: 30px;">

			<h2>Details Page Settings</h2>

			<span class="description">These settings apply to any page holding details for a specific property. <br /><br />

				You may use %city%, %state%, %zip%, %county%, %tract%, and/or %community% in any of the fields below and <br />

				they will display as the relevant value. For example: Homes for sale in %zip%. will appear as Homes for sale in 92681.

			</span>

			<br />

			<table class="form-table">

				<tr>

					<th><label for="i9idxpress-DescMetaTag">Description Meta Tag:</th>

					<td>

						<input type="text" id="i9idxpress-DescMetaTag" size="50" name="<?php echo i9_API_OPTIONS_NAME; ?>[i9idxPressSEODescription]"  value="<?php echo $account_options->i9idxPressSEODescription; ?>" /><br />

						<span class="description">This text will be used as the summary displayed in search results.</span>

					</td>

				</tr>

				<tr>

					<th><label for="i9idxpress-KeywordMetaTag">Keyword Meta Tag:</th>

					<td>

						<input type="text" id="i9idxpress-KeywordMetaTag" size="50" name="<?php echo i9_API_OPTIONS_NAME; ?>[i9idxPressSEOKeywords]" value="<?php echo $account_options->i9idxPressSEOKeywords; ?>" /><br />

						<span class="description">This value aids search engines in categorizing property pages.</span>

					</td>

				</tr>

				<tr>

					<th><label for="i9idxpress-DetailsTitle">Page Title:</th>

					<td>

						<input type="text" id="i9idxpress-DetailsTitle" size="50" name="<?php echo i9_API_OPTIONS_NAME; ?>[i9idxPressSEODetailsTitle]" value="<?php echo $account_options->i9idxPressSEODetailsTitle; ?>" /><br />

						<span class="description">This option will override the default page title.</span>

					</td>

				</tr>

			</table>

			<h2>Results Page Settings</h2>

			<span class="description">

				These settings apply to any page holding a list of properties queried through a url. You may use %location% in the fields and the relevant value will display.

			</span>

			<br />

			<table class="form-table">

				<tr>

					<th><label for="i9idxpress-DescMetaTag">Description Meta Tag:</th>

					<td>

						<input type="text" id="i9idxpress-DescMetaTag" size="50" name="<?php echo i9_API_OPTIONS_NAME; ?>[i9idxPressSEOResultsDescription]"  value="<?php echo $account_options->i9idxPressSEOResultsDescription; ?>" /><br />

						<span class="description">This text will be used as the summary displayed in search results </span>

					</td>

				</tr>

				<tr>

					<th><label for="i9idxpress-KeywordMetaTag">Keyword Meta Tag:</th>

					<td>

						<input type="text" id="i9idxpress-KeywordMetaTag" size="50" name="<?php echo i9_API_OPTIONS_NAME; ?>[i9idxPressSEOResultsKeywords]" value="<?php echo $account_options->i9idxPressSEOResultsKeywords; ?>" /><br />

						<span class="description">This value aids search engines in categorizing property result pages.</span>

					</td>

				</tr>

				<tr>

					<th><label for="i9idxpress-ResultsTitle" >Page Title:</th>

					<td>

						<input type="text" id="i9idxpress-ResultsTitle" size="50" name="<?php echo i9_API_OPTIONS_NAME; ?>[i9idxPressSEOResultsTitle]" value="<?php echo $account_options->i9idxPressSEOResultsTitle; ?>" /><br />

						<span class="description">This option will override the default page title.</span>

					</td>

				</tr>

			</table>

			

			</div>

			<br />

			<p class="submit">

				<input type="submit" class="button-primary" name="Submit" value="Save Options" />

			</p>

		</form>

	</div><?php

	}

	

	static function XMLSitemaps() {

		$options = get_option(i9_OPTION_NAME);

		$urlBase = get_home_url();

		if (substr($urlBase, strlen($urlBase), 1) != "/") $urlBase .= "/";

		$urlBase .= dsSearchAgent_Rewrite::GetUrlSlug();

	?>

		<div class="wrap metabox-holder">

			<h1>XML Sitemaps</h1>

			<?php if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true') : ?>

			<div class="updated"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>

			<?php endif; ?>

			<span class="description">Here you can manage the MLS/IDX items you would like to feature in your XML Sitemap. XML Sitemaps help Google, and other search engines, index your site. Go <a href="http://en.wikipedia.org/wiki/Sitemaps" target="_blank">here</a> to learn more about XML Sitemaps</span>



			<form method="post" action="options.php">

			<?php settings_fields("i9idxpress_xml_sitemap"); ?>

			<h2>XML Sitemaps Locations</h2>

		<?php if ( is_plugin_active('google-sitemap-generator/sitemap.php') || is_plugin_active('bwp-google-xml-sitemaps/bwp-simple-gxs.php') || class_exists('zpress\admin\Theme')) {?>

			<span class="description">Add the Locations (City, Community, Tract, or Zip) to your XML Sitemap by adding them via the dialogs below.</span>

			<?php if (is_plugin_active('bwp-google-xml-sitemaps/bwp-simple-gxs.php')): ?>

				<p><span class="description">REQUIRED: In the BWP GXS Sitemap Generator settings page, ensure that the option to include external pages is checked</span></p>

			<?php endif; ?>

			<div class="i9idxpress-SitemapLocations stuffbox">

				<script type="text/javascript">jQuery(function() { xmlsitemap_page = true; i9idxpressOptions.UrlBase = '<?php echo $urlBase; ?>'; i9idxpressOptions.OptionPrefix = '<?php echo i9_OPTION_NAME; ?>';});</script>

				<div class="inside">

					<ul id="i9idxpress-SitemapLocations">

					<?php

					if (isset($options["SitemapLocations"]) && is_array($options["SitemapLocations"])) {

						$location_index = 0;



						usort($options["SitemapLocations"], array("i9SearchAgent_Admin", "CompareListObjects"));



						foreach ($options["SitemapLocations"] as $key => $value) {

							$location_sanitized = urlencode(strtolower(str_replace(array("-", " "), array("_", "-"), $value["value"])));

					?>

								<li class="ui-state-default i9idxpress-SitemapLocation">

									<div class="action"><input type="button" value="Remove" class="button" onclick="i9idxpressOptions.RemoveSitemapLocation(this)" /></div>

									<div class="priority">

										Priority: <select name="<?php echo i9_OPTION_NAME ; ?>[SitemapLocations][<?php echo $location_index; ?>][priority]">

											<option value="0.0"<?php echo ($value["priority"] == "0.0" ? ' selected="selected"' : '') ?>>0.0</option>

											<option value="0.1"<?php echo ($value["priority"] == "0.1" ? ' selected="selected"' : '') ?>>0.1</option>

											<option value="0.2"<?php echo ($value["priority"] == "0.2" ? ' selected="selected"' : '') ?>>0.2</option>

											<option value="0.3"<?php echo ($value["priority"] == "0.3" ? ' selected="selected"' : '') ?>>0.3</option>

											<option value="0.4"<?php echo ($value["priority"] == "0.4" ? ' selected="selected"' : '') ?>>0.4</option>

											<option value="0.5"<?php echo ($value["priority"] == "0.5" || !isset($value["priority"]) ? ' selected="selected"' : '') ?>>0.5</option>

											<option value="0.6"<?php echo ($value["priority"] == "0.6" ? ' selected="selected"' : '') ?>>0.6</option>

											<option value="0.7"<?php echo ($value["priority"] == "0.7" ? ' selected="selected"' : '') ?>>0.7</option>

											<option value="0.8"<?php echo ($value["priority"] == "0.8" ? ' selected="selected"' : '') ?>>0.8</option>

											<option value="0.9"<?php echo ($value["priority"] == "0.9" ? ' selected="selected"' : '') ?>>0.9</option>

											<option value="1.0"<?php echo ($value["priority"] == "1.0" ? ' selected="selected"' : '') ?>>1.0</option>

											</select>

									</div>

									<div class="type">

										<select name="<?php echo i9_OPTION_NAME ; ?>[SitemapLocations][<?php echo $location_index; ?>][type]">

											<option value="city"<?php echo ($value["type"] == "city" ? ' selected="selected"' : ''); ?>>City</option>

											<option value="community"<?php echo ($value["type"] == "community" ? ' selected="selected"' : ''); ?>>Community</option>

											<option value="tract"<?php echo ($value["type"] == "tract" ? ' selected="selected"' : ''); ?>>Tract</option>

											<option value="zip"<?php echo ($value["type"] == "zip" ? ' selected="selected"' : ''); ?>>Zip Code</option>

										</select>

									</div>

									<div class="value">

										<a href="<?php echo $urlBase . $value["type"] .'/'. $location_sanitized;?>" target="_blank"><?php echo $value["value"]; ?></a>

										<input type="hidden" name="<?php echo i9_OPTION_NAME ; ?>[SitemapLocations][<?php echo $location_index; ?>][value]" value="<?php echo $value["value"]; ?>" />

									</div>

									<div style="clear:both"></div>

								</li>

								<?php

								$location_index++;

							}

						}

						?>

					</ul>

				</div>

			</div>



			<div class="i9idxpress-SitemapLocations i9idxpress-SitemapLocationsNew stuffbox">

				<div class="inside">

					<h4>New:</h4>

					<div class="type">

						<select class="widefat ignore-changes" id="i9idxpress-NewSitemapLocationType">

							<option value="city">City</option>

							<option value="community">Community</option>

							<option value="tract">Tract</option>

							<option value="zip">Zip Code</option>

						</select>

					</div>

					<div class="value"><input type="text" id="i9idxpress-NewSitemapLocation" value="" /></div>

					<div class="action">

						<input type="button" class="button" id="i9idxpress-NewSitemapLocationAdd" value="Add" onclick="i9idxpressOptions.AddSitemapLocation()" />

					</div>

				</div>

			</div>



			<span class="description">"Priority" gives a hint to the web crawler as to what you think the importance of each page is. <code>1</code> being highest and <code>0</code> lowest.</span>



			<h2>XML Sitemaps Options</h2>

			<table class="form-table">

				<tr>

					<th>

						<label for="<?php echo i9_OPTION_NAME ; ?>[SitemapFrequency]">Frequency:</label>

					</th>

					<td>

						<select name="<?php echo i9_OPTION_NAME ; ?>[SitemapFrequency]" id="<?php echo i9_OPTION_NAME; ?>_SitemapFrequency">

							<!--<option value="always"<?php echo (@$options["SitemapFrequency"] == "always" ? ' selected="selected"' : '') ?>>Always</option> -->

							<option value="hourly"<?php echo (@$options["SitemapFrequency"] == "hourly" ? 'selected="selected"' : '') ?>>Hourly</option>

							<option value="daily"<?php echo (@$options["SitemapFrequency"] == "daily" || !isset($options["SitemapFrequency"]) ? 'selected="selected"' : '') ?>>Daily</option>

							<!--<option value="weekly"<?php echo (@$options["SitemapFrequency"] == "weekly" ? 'selected="selected"' : '') ?>>Weekly</option>

							<option value="monthly"<?php echo (@$options["SitemapFrequency"] == "monthly" ? 'selected="selected"' : '') ?>>Monthly</option>

							<option value="yearly"<?php echo (@$options["SitemapFrequency"] == "yearly" ? 'selected="selected"' : '') ?>>Yearly</option>

							<option value="never"<?php echo (@$options["SitemapFrequency"] == "never" ? 'selected="selected"' : '') ?>>Never</option> -->

						</select>

						<span class="description">The "hint" to send to the crawler. This does not guarantee frequency, crawler will do what they want.</span>

					</td>

				</tr>

			</table>

			<br />

			<p class="submit">

							<input id="xml-options-saved" type="submit" class="button-primary" name="Submit" value="Save Options" />

			</p>

			</form>

		</div>

		<?php } else { ?>

			<span class="description">To enable this functionality, install and activate one of these plugins: <br />

				<a class="thickbox onclick" title="Google XML Sitemaps" href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=google-sitemap-generator&TB_iframe=true&width=640')?>" target="_blank">Google XML Sitemaps</a><br />

				<a class="thickbox onclick" title="BWP Google XML Sitemaps" href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=bwp-google-xml-sitemaps&TB_iframe=true&width=640')?>" target="_blank">BWP Google XML Sitemaps</a> (for Multi-Site wordpress installs)

			</span>

		<?php }

	}



	static function DetailsOptions() {

		$options = get_option(i9_OPTION_NAME);

		$apiHttpResponse = i9_ApiRequest::FetchData("AccountOptions", array(), false, 0);

		if (!empty($apiHttpResponse["errors"]) || $apiHttpResponse["response"]["code"] != "200")

			wp_die("We're sorry, but we ran into a temporary problem while trying to load the account data. Please check back soon.", "Account data load error");

		else

			$account_options = json_decode($apiHttpResponse["body"]);

		$urlBase = get_home_url();

		if (substr($urlBase, strlen($urlBase), 1) != "/") $urlBase .= "/";

			$urlBase .= dsSearchAgent_Rewrite::GetUrlSlug(); ?>

		<div class="wrap metabox-holder">

			<h1>More Options</h1>

			<?php if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true') : ?>

			<div class="updated"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>

			<?php endif; ?>

			<form method="post" action="options.php">

				<?php settings_fields("i9idxpress_api_options"); ?>

				<span class="description">These settings apply to any page holding details for a specific property.</span>

				<table class="form-table">

					<?php if (isset($account_options->{'i9idxPress-Package'}) && $account_options->{'i9idxPress-Package'} == "pro"): ?>

					<tr>

						<th>

							<label for="i9idxpress-ShowPanel_ZillowCB">Show Zestimate:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowPanel_ZillowCB" size="50" <?php checked('true', strtolower($account_options->ShowPanel_Zillow)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowPanel_Zillow" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowPanel_Zillow]" value="<?php echo $account_options->ShowPanel_Zillow; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<?php endif ?>

					<tr>

						<th>

							<label for="i9idxpress-ShowWalkScoreInDetailsCB">Show Walkscore In Details:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowWalkScoreInDetailsCB" size="50" <?php checked('true', strtolower($account_options->ShowWalkScoreInDetails)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowWalkScoreInDetails" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowWalkScoreInDetails]" value="<?php echo $account_options->ShowWalkScoreInDetails; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-ShowWalkScoreInResultsCB">Show Walkscore In Results:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowWalkScoreInResultsCB" size="50" <?php checked('true', strtolower($account_options->ShowWalkScoreInResults)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowWalkScoreInResults" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowWalkScoreInResults]" value="<?php echo $account_options->ShowWalkScoreInResults; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-ShowPanel_FeaturesCB">Show Features:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowPanel_FeaturesCB" size="50" <?php checked('true', strtolower($account_options->ShowPanel_Features)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowPanel_Features" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowPanel_Features]" value="<?php echo $account_options->ShowPanel_Features; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-AllowScheduleShowingFeatureCB">Show Schedule a Showing button:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-AllowScheduleShowingFeatureCB" size="50" <?php checked('true', strtolower($account_options->AllowScheduleShowingFeature)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-AllowScheduleShowingFeature" name="<?php echo i9_API_OPTIONS_NAME; ?>[AllowScheduleShowingFeature]" value="<?php echo $account_options->AllowScheduleShowingFeature; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-ShowAskAQuestionCB">Show Ask a Question button:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowAskAQuestionCB" size="50" <?php checked('true', strtolower($account_options->ShowAskAQuestion)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowAskAQuestion" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowAskAQuestion]" value="<?php echo $account_options->ShowAskAQuestion; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<?php if (isset($account_options->{'i9idxPress-Package'}) && $account_options->{'i9idxPress-Package'} == "pro"): ?>

					<tr>

						<th>

							<label for="i9idxpress-ShowPanel_SchoolsCB">Show Schools:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowPanel_SchoolsCB" size="50" <?php checked('true', strtolower($account_options->ShowPanel_Schools)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowPanel_Schools" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowPanel_Schools]" value="<?php echo $account_options->ShowPanel_Schools; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<?php endif; ?>

					<tr>

						<th>

							<label for="i9idxpress-ShowPanel_MapCB">Show Map:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowPanel_MapCB" size="50" <?php checked('true', strtolower($account_options->ShowPanel_Map)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowPanel_Map" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowPanel_Map]" value="<?php echo $account_options->ShowPanel_Map; ?>" />

							<span class="description"></span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-ShowPanel_Contact">Show Contact Form:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowPanel_ContactCB" size="50" <?php checked('true', strtolower($account_options->ShowPanel_Contact)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowPanel_Contact" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowPanel_Contact]" value="<?php echo $account_options->ShowPanel_Contact;?>" />

							<span class="description"></span>

						</td>

					</tr>

					<tr>

						<th>

							<label for="i9idxpress-ShowOpenHousesCB">Show Open House Details:</label>

						</th>

						<td>

							<input type="checkbox" id="i9idxpress-ShowOpenHousesCB" size="50" <?php checked('true', strtolower($account_options->ShowOpenHouses)); ?> onclick="i9idxpressOptions.OptionCheckBoxClick(this);" /><br />

							<input type="hidden" id="i9idxpress-ShowOpenHouses" name="<?php echo i9_API_OPTIONS_NAME; ?>[ShowOpenHouses]" value="<?php echo $account_options->ShowOpenHouses;?>" />

							<span class="description"></span>

						</td>

					</tr>

				</table>

				<br />

				<p class="submit">

					<input type="submit" class="button-primary" name="Submit" value="Save Options" />

				</p>

			</form>

		</div><?php

	}



	static function RunDiagnostics($options) {

		// it's possible for a malicious script to trick a blog owner's browser into running the Diagnostics which passes the PrivateApiKey which

		// could allow a bug on the wire to pick up the key, but 1) we have IP and URL restrictions, and 2) there are much bigger issues than the

		// key going over the wire in the clear if the traffic is being spied on in the first place

		global $wp_rewrite;

		//print_r($options);//die;

		$diagnostics = i9_ApiRequest::FetchData("Diagnostics", array("apiKey" => $options["PrivateApiKey"]), false, 0, $options);

		//print_r($diagnostics);die;

		if (empty($diagnostics["body"]) || $diagnostics["response"]["code"] != "200")

			return array("error" => true);



		$diagnostics = (array)json_decode($diagnostics["body"]);

		$setDiagnostics = array();

		$timeDiff = time() - strtotime($diagnostics["CurrentServerTimeUtc"]);

		$secondsIn2Hrs = 60 * 60 * 2;

		$permalinkStructure = get_option("permalink_structure");



		$setDiagnostics["IsApiKeyValid"] = $diagnostics["IsApiKeyValid"];

		$setDiagnostics["IsAccountAuthorizedToMLS"] = $diagnostics["IsAccountAuthorizedToMLS"];

		$setDiagnostics["IsAccountValid"] = $diagnostics["IsAccountValid"];

		$setDiagnostics["IsApiKeyAuthorizedToUri"] = $diagnostics["IsApiKeyAuthorizedToUri"];

		$setDiagnostics["IsApiKeyAuthorizedToIP"] = $diagnostics["IsApiKeyAuthorizedToIP"];



		$setDiagnostics["PhpVersionAcceptable"] = version_compare(phpversion(), i9idxPRESS_MIN_VERSION_PHP) != -1;

		$setDiagnostics["UrlInterceptSet"] = get_option("permalink_structure") != "" && !preg_match("/index\.php/", $permalinkStructure);

		$setDiagnostics["ClockIsAccurate"] = $timeDiff < $secondsIn2Hrs && $timeDiff > -1 * $secondsIn2Hrs;

		$setDiagnostics["UnderMonthlyCallLimit"] = $diagnostics["AllowedApiRequestCount"] === 0 || $diagnostics["AllowedApiRequestCount"] > $diagnostics["CurrentApiRequestCount"];



		$setDiagnostics["HasSearchAgentPro"] = $diagnostics["HasSearchAgentPro"];

		$setDiagnostics["i9idxPressPackage"] = $diagnostics["i9idxPressPackage"];

		$setDiagnostics["DetailsRequiresRegistration"] = $diagnostics["DetailsRequiresRegistration"];


		$setDiagnostics["DiagnosticsSuccessful"] = true;

		
		/*foreach ($setDiagnostics as $key => $value) {

			if (!$value && $key != "HasSearchAgentPro" && $key != "DetailsRequiresRegistration")

				$setDiagnostics["DiagnosticsSuccessful"] = false;

		}
*/
		$wp_rewrite->flush_rules();



		return $setDiagnostics;

	}

	static function SanitizeOptions($options) {

		if(!isset($options) || !$options) $options = array();

		

		if (!empty($options["FullApiKey"])) {

			$options["FullApiKey"] = trim($options["FullApiKey"]);

			$apiKeyParts = explode("/", $options["FullApiKey"]);

			unset($options["FullApiKey"]);

			

			//if (sizeof($apiKeyParts) == 3) {

				$options["AccountID"] = $apiKeyParts[0];

				$options["SearchSetupID"] = $apiKeyParts[1];

				$options["PrivateApiKey"] = $apiKeyParts[1];

				

				i9_ApiRequest::FetchData("BindToRequester", array(), false, 0, $options);

				$diagnostics = self::RunDiagnostics($options);

				//print_r($diagnostics);die;

				$options["HasSearchAgentPro"] = $diagnostics["HasSearchAgentPro"];

				$options["i9idxPressPackage"] = $diagnostics["i9idxPressPackage"];

				$options["Activated"] = $diagnostics["DiagnosticsSuccessful"];

				
				if (!$options["Activated"] && isset($options["HideIntroNotification"]))

					unset($options["HideIntroNotification"]);

			

			//}

		}

		

		// different option pages fill in different parts of this options array, so we simply merge what's already there with our new data

		if ($full_options = get_option(i9_OPTION_NAME)) {

			// clear out old ResultsMapDefaultState if its replacement, ResultsDefaultState is set

			if (isset($options['ResultsDefaultState']) && isset($full_options['ResultsMapDefaultState'])) {

				unset($full_options['ResultsMapDefaultState']);

			}

			

			// make sure the option to remove diverse solutions links is removed if unchecked

			if (isset($options['ResultsDefaultState']) && isset($full_options['RemoveDsDisclaimerLinks'])) {

				unset($full_options['RemoveDsDisclaimerLinks']);

			}

			

			// merge existing data with new data

			$options = array_merge($full_options, $options);

		}



		// call the sitemap rebuild action since they may have changed their sitemap locations. the documentation says that the sitemap

		// may not be rebuilt immediately but instead scheduled into a cron job for performance reasons.

		do_action("sm_rebuild");



		return $options;

	}



	/*

	 * We're using the sanitize to capture the POST for these options so we can send them back to the diverse API

	 * since we save and consume -most- options there.

	 */

	static function SanitizeApiOptions($options) {

		if (is_array($options)) {

			$options_text = "";


			foreach ($options as $key => $value) {

				if ($options_text != "") $options_text .= ",";

				if ($key == 'RestrictResultsToZipcode' || $key == 'RestrictResultsToCity' || $key == 'RestrictResultsToCounty' || $key == 'RestrictResultsToState') {

				$value = preg_replace("/\r\n|\r|\n/", ",", $value);//replace these values with new commas in api db

				}

				$options_text .= $key.'|'.urlencode($value);

				unset($options[$key]);

			}

			$result = i9_ApiRequest::FetchData("SaveAccountOptions", array("options" => $options_text), false, 0);

			

			// this serves to flush the cache

			i9_ApiRequest::FetchData("AccountOptions", array(), false, 0);

		}

		return $options;

	}



	static function CompareListObjects($a, $b)

	{

		$al = strtolower($a["value"]);

		$bl = strtolower($b["value"]);

		if ($al == $bl) {

			return 0;

		}

		return ($al > $bl) ? +1 : -1;

	}

	public static function NavMenus($posts) {

		$options = get_option(i9_OPTION_NAME);

		

		// offset the time to ensure we have a unique post id

		$post_id = time() + sizeof($posts);

		

		if (isset($options['AgentID']) && $options['AgentID'] != '') {

			$posts[] = (object) array(

				'ID'           => $post_id,

				'object_id'    => $post_id,

				'post_content' => '',

				'post_excerpt' => '',

				'post_parent'  => 0,

				'post_title'   => 'My Listings',

				'post_type'    => 'nav_menu_item',

				'type'         => 'custom',

				'url'          => get_home_url().'/idx/?'.urlencode('idx-q-ListingAgentID<0>') . '=' . $options['AgentID'],

				'zpress_page'  => true

			);

			$post_id++;

			

			$posts[] = (object) array(

				'ID'           => $post_id,

				'object_id'    => $post_id,

				'post_content' => '',

				'post_excerpt' => '',

				'post_parent'  => 0,

				'post_title'   => 'My Sold Properties',

				'post_type'    => 'nav_menu_item',

				'type'         => 'custom',

				'url'          => get_home_url().'/idx/?'.urlencode('idx-q-ListingAgentID<0>') . '=' . $options['AgentID'] .'&idx-q-ListingStatuses=8',

				'zpress_page'  => true

			);

			$post_id++;

		}

		

		if (isset($options['OfficeID']) && $options['OfficeID'] != '') {

			$posts[] = (object) array(

				'ID'           => $post_id,

				'object_id'    => $post_id,

				'post_content' => '',

				'post_excerpt' => '',

				'post_parent'  => 0,

				'post_title'   => 'My Office Listings',

				'post_type'    => 'nav_menu_item',

				'type'         => 'custom',

				'url'          => get_home_url().'/idx/?'.urlencode('idx-q-ListingOfficeID<0>') . '=' . $options['OfficeID'],

				'zpress_page'  => true

			);

			$post_id++;

		}

		

		$posts[] = (object) array(

			'ID'           => $post_id,

			'object_id'    => $post_id,

			'post_content' => '',

			'post_excerpt' => '',

			'post_parent'  => 0,

			'post_title'   => 'Real Estate Search',

			'post_type'    => 'nav_menu_item',

			'type'         => 'custom',

			'url'          => get_home_url().'/idx/search/',

			'zpress_page'  => true

		);

		

		return $posts;

	}

	static function CreateLinkBuilderMenuWidget()

	{

		add_meta_box( 'add-link-builder', __('Listings Page Builder'), array('i9SearchAgent_Admin', 'CreateLinkBuilder'), 'nav-menus', 'side', 'default' );

	}

	/**

	 * Displays a metabox for the link builder menu item.

	 */

	static function CreateLinkBuilder() {

		global $_nav_menu_placeholder, $nav_menu_selected_id;

		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;



		$current_tab = 'create';

		if ( isset( $_REQUEST['customlink-tab'] ) && in_array( $_REQUEST['customlink-tab'], array('create', 'all') ) ) {

			$current_tab = $_REQUEST['customlink-tab'];

		}



		$removed_args = array(

			'action',

			'customlink-tab',

			'edit-menu-item',

			'menu-item',

			'page-tab',

			'_wpnonce',

		);

		

		i9SearchAgent_Admin::LinkBuilderHtml(false, $_nav_menu_placeholder, $nav_menu_selected_id);

	}

		

	public static function LinkBuilderHtml($in_post_dialog = false, $_nav_menu_placeholder = -1, $nav_menu_selected_id = 1, $in_idx_page_options=false, $preset_url='') {

		$label_class = (!$in_post_dialog) ? ' input-with-default-title' : '';

		$label_value = ($in_post_dialog && isset($_GET['selected_text'])) ? ' value="'.esc_attr(strip_tags($_GET['selected_text'])).'"' : '';

		$url_value   = ($in_post_dialog && isset($_GET['selected_url'])) ? htmlspecialchars($_GET['selected_url']) : 'http://';

		$link_mode   = (isset($_GET['idxlinkmode'])) ? $_GET['idxlinkmode'] : '';

		if(!empty($preset_url)){

			$url_value = $preset_url;

		}



		$property_types_html = "";

		$property_types = i9_ApiRequest::FetchData('AccountSearchSetupPropertyTypes', array(), false, 60 * 60 * 24);

		if(!empty($property_types) && is_array($property_types)){

		    $property_types = json_decode($property_types["body"]);

		    foreach ($property_types as $property_type) {

		        $checked_html = '';

		        $name = htmlentities($property_type->DisplayName);

				$id = $property_type->SearchSetupPropertyTypeID;

		        $property_types_html .= <<<HTML

{$id}: {$name},

HTML;

		    }

		}

		$property_types_html = substr($property_types_html, 0, strlen($property_types_html)-1); 

?>

	<script> zpress_home_url = '<?php echo get_home_url() ?>';</script>

	<div id="i9idxpress-link-builder" class="customlinkdiv">

	    <input type="hidden" id="linkBuilderPropertyTypes" value="<?php echo $property_types_html ?>" />

		<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />

		<input type="hidden" value="<?php esc_attr_e($link_mode) ?>" id="i9idx-linkbuilder-mode" ?>

		<?php if(!$in_idx_page_options): ?>

		<p class="i9idxpress-item-wrap">

			<label class="howto" for="i9idxpress-menu-item-label">

				<span><?php _e('Label'); ?></span>

				<input id="i9idxpress-menu-item-label" name="menu-item-label" type="text" class="regular-text menu-item-textbox<?php echo $label_class ?>" title="<?php esc_attr_e('Menu Item'); ?>"<?php echo $label_value ?> />

			</label>

		</p>

		<?php endif; ?>

		<p class="i9idxpress-item-wrap">

			<label class="howto" for="i9idxpress-filter-menu">

				<span><?php _e('Add Filter'); ?></span>

				<select class="regular-text" id="i9idxpress-filter-menu" ></select>

			</label>

		</p>

		

		<div id="i9idxpress-editor-wrap" class="i9idxpress-item-wrap hidden">

			<div class="i9idxpress-filter-editor">

				<div class="i9idxpress-editor-header">

					<h4>Filter results by <b>Beds</b></h4>

					<span class="i9idx-editor-cancel"><a href="javascript:void(0)"></a></span>

				</div>

				<div class="i9idxpress-editor-main"></div>

				<div class="buttons">

					<input type="button" value="Update this Filter" class="button-primary" />

					<input type="button" value="Cancel" class="button-secondary i9idx-editor-cancel" />

				</div>

			</div>

		</div>

		

		<div id="i9idxpress-filters-wrap" class="i9idxpress-item-wrap hidden">

			<span><?php _e('Filters'); ?></span>

			<ul id="i9idxpress-filter-list"></ul>

		</div>



		<?php if(!$in_idx_page_options): ?>	

		<p class="i9idxpress-item-wrap">

			<label class="howto i9idxpress-checkbox">

				<input id="i9idxpress-show-url" type="checkbox" />

				<span><?php _e('Display Generated URL'); ?></span>

			</label>

		</p>

		<?php endif; ?>

		

		<?php

		$inputName = 'menu-item['.$_nav_menu_placeholder.'][menu-item-url]';

		

		if($in_idx_page_options):

			$inputName = 'i9idxpress-assembled-url';

		endif; ?>



		<p id="i9idxpress-assembled-url-wrap" class="i9idxpress-item-wrap hidden">

			<label class="howto" for="i9idxpress-assembled-url">

				<span><?php _e('URL'); ?></span>

				<textarea id="i9idxpress-assembled-url" name="<?php echo $inputName; ?>" type="text" rows="4" class="code menu-item-textbox"><?php echo $url_value; ?></textarea>

			</label>

		</p>

		

		<?php if(!$in_idx_page_options): ?>

		<p class="button-controls">

			<span class="add-to-menu">

				<?php if (!$in_post_dialog): ?>

				<img id="img-link-builder-waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />

				<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-menu-item" id="submit-linkbuilderdiv" />

				<?php else: ?>

				<input type="button" id="i9idxpress-lb-cancel" name="cancel" value="Cancel" class="button-secondary" onclick="tinyMCEPopup.close();" />

				<input type="button" id="i9idxpress-lb-insert" name="insert" value="<?php esc_attr_e($link_mode); ?> Link" class="button-primary" style="text-transform: capitalize;" onclick="i9idxLinkBuilder.insert();" />

				<?php endif ?>

			</span>

		</p>

		<?php endif; ?>

	</div><!-- /#i9idxpress-link-builder -->

	<?php

	}

}

?>