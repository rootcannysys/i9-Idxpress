<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: I-9 IDXpress
Plugin URI: https://i9techus.com/
Description: An I-9 technologies word-press plugin to embed live real estate data from an MLS directly into a blog. You must have a i9 IDXpress account to use this plugin.
Author: I-9 Technologies
Version: 1.0
Author URI: http://i9techus.com/
*/

global $wp_version;

require_once(ABSPATH . "wp-admin/includes/plugin.php");
$require_prefix = dirname( __FILE__ ) . "/";
$pluginData = get_plugin_data(__FILE__);

define("i9_OPTION_NAME", "i9-idx-key");
define("i9_API_OPTIONS_NAME", "i9-api-options");
define("i9_PLUGIN_VERSION", $pluginData["Version"]);
define("i9_PLUGIN_URL", plugins_url()."/i9-idxpress/");

wp_enqueue_style('i9_admin_options_style', plugins_url('css/admin-options.css?v1.0', __FILE__ ), array(), i9_PLUGIN_VERSION);

require_once($require_prefix . "comments-template.php");
require_once($require_prefix . "autocomplete.php");
require_once($require_prefix . "footer.php");
require_once($require_prefix . "i9-listing-areas.php");
require_once($require_prefix . "i9quick_search.php");
require_once($require_prefix . "i9Listings.php");
require_once($require_prefix . "i9guided-search.php");
require_once($require_prefix . "i9-open-house.php");
require_once($require_prefix . "i9-budget-widget.php");
require_once($require_prefix . "i9-calc-widget.php");
require_once($require_prefix . "rewrite.php");
require_once($require_prefix . "api-request.php");
require_once($require_prefix . "i9-idx-seo.php");
require_once($require_prefix . "client.php");
require_once($require_prefix . "client-assist.php");

if (is_admin()) {

	// this is needed specifically for development as PHP seems to choke when 1) loading this in admin, 2) using windows, 3) using directory junctions

	include_once(str_replace("\\", "/", WP_PLUGIN_DIR) . "/i9-idxpress/admin.php");

}
if (defined('DS_API')) {
	i9_ApiRequest::$ApiEndPoint = DS_API;
} else {
	define('DS_API', 'http://localhost/wordpress/canny');
	i9_ApiRequest::$ApiEndPoint = DS_API;
}

add_action("widgets_init", "i9_InitWidgets");

function i9_InitWidgets() {

		register_widget("i9quick_search");
		register_widget("i9_ListingsWidget");
		register_widget("i9_ListingAreasWidget");
		register_widget("i9_IdxGuidedSearchWidget");
		register_widget("i9_OpenHouseWidget");
		//register_widget("i9_IdxBudgetWidget");
		register_widget("i9_IdxCalcWidget");
		flush_rewrite_rules();
	
}

////////////////////////////////////////////
if (is_admin()) {
	
        add_action( 'add_meta_boxes', array( 'i9ListingsPages', 'addIdxOptions' ) );
        add_action( 'save_post', array( 'i9ListingsPages', 'saveIdxOptions' ) );  

} else {
	
        add_action('admin_bar_menu', array('i9ListingsPages', 'AdminBar'), 500);
        add_filter('the_posts', array('i9ListingsPages', 'DisplayPage'), 100);
        add_filter('body_class', array('i9ListingsPages', 'AddPostClass'));
        add_filter('post_class', array('i9ListingsPages', 'AddPostClass'));
        add_action('init', array('i9ListingsPages', 'EnsureBaseUri'));
        add_filter('template_include', array('i9ListingsPages', 'SetTemplate'));
}

//add_action('init', array('i9ListingsPages', 'Setup'));

class i9ListingsPages {
    
    const LANG = 'some_textdomain';

    public static function Setup(){
        
        register_post_type( 'i9-idx-pages',
                array(
                        'labels' => array(
                                'name' => __( 'IDX Pages' ),
                                'menu_name' => __('IDX Pages'),
                                'singular_name' => __( 'IDX Page' ),
                                'add_new_item' => __( 'Add New IDX Page' ),
                                'new_item' => __( 'New IDX Page' ),
                                'edit_item' => __( 'Edit IDX  Page' ),
                                'view_item' => __( 'View IDX Page' ),
                                'all_items' => __( 'All IDX Pages' ),
                                'search_items' => __( 'Search IDX Pages' ),
                        ),
                'public' => true,
                'has_archive' => false,
                'show_in_menu' => true,
                'show_ui' => true,
                'menu_position' => 15,
                'menu_icon' => 'dashicons-admin-home',
                'supports' => array('title', 'thumbnail'),
                'public' => true,
                'hierarchical' => true,
                'taxonomies' => array(),
                'capability_type'     => 'page',
                'rewrite' => array('slug'=>'canny/listings', 'with_base'=>true)
                )
        );
    }

    public static function EnsureBaseUri(){
		
        if (preg_match('/canny\/listings/', $_SERVER['REQUEST_URI'])){
			$parts = explode('?', $_SERVER['REQUEST_URI']);
            if(substr($parts[0], -1) == '/'){
                return;
            }
            if(count($parts) == 1){
                $redirect = $parts[0].'/';
                header("Location: $redirect", true, 301);
                exit();
            }
            return;
        }
    }
	

    public static function RewriteRules() {
		
            add_rewrite_tag('%i9-idx-pages%', '([^&]+)');
            add_rewrite_rule('[Ii][Dd][Xx]/[Ll][Ii][Ss][Tt][Ii][Nn][Gg][Ss]/([^/]+)(?:/page\-(\\d+))?', 'index.php?i9-idx-pages=$matches[1]&idx-d-ResultPage=$matches[2]', 'top');

            add_rewrite_tag('%i9-idx-archives-listings-page%', '([^&]+)');
            add_rewrite_rule('archives/[Ii][Dd][Xx]/[Ll][Ii][Ss][Tt][Ii][Nn][Gg][Ss]/([^/]+)(?:/page\-(\\d+))?', 'index.php?i9-idx-pages=$matches[1]&idx-d-ResultPage=$matches[2]', 'top');

            $rules = get_option('rewrite_rules');
            if (!isset($rules["[Ii][Dd][Xx]/[Ll][Ii][Ss][Tt][Ii][Nn][Gg][Ss]/([^/]+)(?:/page\-(\\d+))?"]))
                add_action('wp_loaded', array('i9ListingsPages', 'FlushRewriteRules'));

            if (!isset($rules["archives/[Ii][Dd][Xx]/[Ll][Ii][Ss][Tt][Ii][Nn][Gg][Ss]/([^/]+)(?:/page\-(\\d+))?"]))
                add_action('wp_loaded', array('i9ListingsPages', 'FlushRewriteRules'));
				
    }
    
    public static function FlushRewriteRules(){
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    public static function AdminBar() {
            global $wp_query;

            if (is_array($wp_query->query) && isset($wp_query->query['i9-idx-pages']) && current_user_can('manage_options') && is_admin_bar_showing()) {
                    global $wp_admin_bar;
                    $wp_admin_bar->remove_menu('edit');
            }
    }

    public static function DisplayPage($posts) {
		
            global $wp_query;

            if (is_array($wp_query->query) && (isset($wp_query->query['i9-idx-pages']))) {
                remove_filter("the_posts", array("i9_Client", "Activate"));
                if(!isset($posts[0])){
                    return $posts;
                }
                $pageData = $posts[0];
                $pageContent = trim($pageData->post_content);
                if(!empty($pageContent)){
                    $pageContent = wpautop(wpautop($pageContent)).'<div class="i9idx-clear;"></div><hr class="i9idx-separator" />';
                }
				
                $wp_query->query['i9-action'] = 'results';
                $wp_query->is_page = 1;
                $wp_query->is_singular = 1;
                $wp_query->is_single = 0;
                if(!isset($_GET)){
                    $_GET = array();
                }
                $linkUrl = get_post_meta($pageData->ID, 'i9idxpress-assembled-url', true);
                $parts = parse_url($linkUrl);
                $filters = array();
                if(isset($parts['query'])){
                    parse_str($parts['query'], $filters);
                }
                $filters = array_map(array('i9ListingsPages','CleanIdxPageFilters'), $filters);
                $newPosts = i9_Client::Activate($posts, $filters, $pageData->ID);
                $newPosts[0]->post_content = $pageContent . $newPosts[0]->post_content;
                $newPosts[0]->post_name = $pageData->post_name;
                $newPosts[0]->ID = $pageData->ID;
                $newPosts[0]->post_title = $pageData->post_title;
                $newPosts[0]->post_type = 'i9-idx-pages';
                return $newPosts;
            }
            return $posts;
    }

    public static function CleanIdxPageFilters($item){
        return stripslashes($item);
    }

    public static function SetTemplate($template) {
        if (get_query_var('post_type') == 'i9-idx-pages') {
            $options = get_option(i9_OPTION_NAME);
            if (!empty($options['IDXTemplate'])) {
                $newTemplate = locate_template(array($options['IDXTemplate']));
                if (!empty($newTemplate)) $template = $newTemplate;
            }
            else if (!empty($options['ResultsTemplate'])) {
                $newTemplate = locate_template(array($options['ResultsTemplate']));
                if (!empty($newTemplate)) $template = $newTemplate;
            }
        }
		
        return $template;
    }


    public static function AddPostClass($class) {
            global $wp_query;
            if (get_query_var('post_type') == 'i9-idx-pages') {
                    $class[] = 'page';
            }
            return $class;
    }

    static function header() {
            global $thesis;

            // let thesis handle the canonical
            if (!$thesis)
                echo "<link rel=\"canonical\" href=\"" . get_permalink() . "\" />\n";
    }

    public static function addIdxOptions($post){
        add_meta_box( 
            'idx_filters_box'
            ,__( 'IDX Data Filters', self::LANG )
            ,array( 'i9ListingsPages', 'renderIdxOptions' )
            ,'i9-idx-pages' 
            ,'normal'
            ,'high'
            );
    }

    public static function saveIdxOptions($post_id){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
       
        if (empty($_POST['i9-idx-page_nonce'])) return;
            
        if (!wp_verify_nonce( $_POST['i9-idx-page_nonce'], plugin_basename( __FILE__ ) ) ) die('no nonce');

        if ( 'i9-idx-pages' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) ) return;
        }
        else {
            if ( !current_user_can( 'edit_post', $post_id ) ) die('uhh');
        }
        $url = esc_url($_POST['i9idxpress-assembled-url']);
        
        update_post_meta($post_id, 'i9idxpress-assembled-url', $url);
    }

    public static function renderIdxOptions($post){
        $url_value = null;
        $url_value = get_post_meta($post->ID, 'i9idxpress-assembled-url', true);
        $adminUri = get_admin_url();
        $property_types_html = "";

        wp_nonce_field( plugin_basename( __FILE__ ), 'i9-idx-page_nonce' );

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

        echo '
        <div class="postbox">
            <div class="inside">
                <input type="hidden" id="linkBuilderPropertyTypes" value="'.$property_types_html.'" />';
                //dsSearchAgent_Admin::LinkBuilderHtml(false, -1, 1, true, $url_value);
        echo '
            </div>
        </div>
        <div><span class="description">You must Publish/Update your page after modifying the IDX data filters.</span></div>
        ';
    }
}

?>
