<?php
class i9_OpenHouseWidget extends WP_Widget {

	public function __construct() {

		parent::__construct("i9-Open-house", "I-9 IDX Open-House", array(

			"classname" => "openhouse-widget-listings",

			"description" => "Show a list of Open-House listings"

		));

			

	}

	function widget($args, $instance) {

		extract($args);

		extract($instance);

		$title = apply_filters("widget_title", $title);

		$options = get_option(i9_OPTION_NAME);
		
		if (!isset($options["Activated"]) || !$options["Activated"])

			return;

		if (!isset($options["HasSearchAgentPro"]) || !$options["HasSearchAgentPro"])

			return;
				
		wp_enqueue_script('jquery', false, array(), false, true);

		echo $before_widget;

		if ($title)

			echo $before_title . $title . $after_title;


		$apiRequestParams = array();

		if($listingsToShow>50){
			$listingsToShow = 50;
		}
		
		$apiRequestParams["directive.ResultsPerPage"] = $listingsToShow;

		$apiHttpResponse = i9_ApiRequest::FetchData("opnehouses", $apiRequestParams);

		if (empty($apiHttpResponse["errors"]) && $apiHttpResponse["response"]["code"] == "200") {

			$data = $apiHttpResponse["body"];

		} else {

			switch ($apiHttpResponse["response"]["code"]) {

				case 403:

					$data = '<p class="i9idx-error">'.i9idxPRESS_INACTIVE_ACCOUNT_MESSAGE.'</p>';

				break;

				default:

					$data = '<p class="i9idx-error">'.i9idxPRESS_IDX_ERROR_MESSAGE.'</p>';

			}

		}

		//$data = str_replace('{$pluginUrlPath}', i9_ApiRequest::MakePluginsUrlRelative(plugin_dir_url(__FILE__)), $data);

		echo $data;

		echo $after_widget;

		//i9idx_footer::ensure_disclaimer_exists();

	}

	function update($new_instance, $old_instance) {

		// we need to do this first-line awkwardness so that the title comes through in the sidebar display thing

		$new_instance["listingsOptions"]["title"] = $new_instance["title"];

		$new_instance = $new_instance["listingsOptions"];

		return $new_instance;

	}

	function form($instance) {

		wp_enqueue_script('i9idxpress_widget_listings', plugins_url('js/widget-listings.js', __FILE__ ), array('jquery'), i9_PLUGIN_VERSION, true);

		$options = get_option(i9_OPTION_NAME);

		$instance = wp_parse_args($instance, array(

			"title"				=> "Open Houses",

			"listingsToShow"	=> "20",

		));

		$titleFieldId = $this->get_field_id("title");

		$titleFieldName = $this->get_field_name("title");

		$baseFieldId = $this->get_field_id("listingsOptions");

		$baseFieldName = $this->get_field_name("listingsOptions");

		
		$agentListingsNote = null;

		$officeListingsNote = null;

		if ($options['AgentID'] == null) {

			$agentListingsNote = "There are no listings to show with your current settings.  Please make sure you have provided your Agent ID on the IDX > General page of your site dashboard, or change this widget's settings to show other listings.";

		}

		if ($options['OfficeID'] == null) {

			$officeListingsNote = "There are no listings to show with your current settings.  Please make sure you have provided your Office ID on the IDX > General page of your site dashboard, or change this widget's settings to show other listings.";

		}


		echo <<<HTML

			<p>

				<label for="{$titleFieldId}">Widget title</label>

				<input id="{$titleFieldId}" name="{$titleFieldName}" value="{$instance[title]}" class="widefat" type="text" />

			</p>

			<p>

				<label for="{$baseFieldId}[listingsToShow]"># of listings to show (max 50)</label>

				<input id="{$baseFieldId}[listingsToShow]" name="{$baseFieldName}[listingsToShow]" value="{$instance[listingsToShow]}" class="widefat no_of_listingss" type="text" />
				<span class="max_listings" style="color:red;"></span>
			</p>

			<div class="widefat" style="border-width: 0 0 1px; margin: 20px 0;"></div>

			<script> jQuery(document).ready(function() { 
				jQuery('.no_of_listingss').change(function(e){
					e.preventDefault();
					var a = jQuery(this).val();
					if(isNaN(a) || parseInt(a)>50){
						jQuery('.max_listings').html('Max Listings Exceeded [Max Listings is 50 by default]');
						jQuery('.no_of_listingss').val(50);
					} else {
						jQuery('.max_listings').html('');
					}
				}) 
			}); </script>

HTML;

	}

}

?>