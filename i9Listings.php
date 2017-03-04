<?php
class i9_ListingsWidget extends WP_Widget {

	public function __construct() {

		parent::__construct("cannysys-listings", "I-9 IDX Listings", array(

			"classname" => "cannysys-widget-listings",

			"description" => "Show a list of real estate listings"

		));

			

	}

	function widget($args, $instance) {

		extract($args);

		extract($instance);

		$title = apply_filters("widget_title", $title);

		//$sort = isset($instance['areaSourceConfig']['sort'])?$instance['areaSourceConfig']['sort']:'';

		$options = get_option(i9_OPTION_NAME);



		if (!isset($options["Activated"]) || !$options["Activated"])

			return;

			

		wp_enqueue_script('jquery', false, array(), false, true);



		echo $before_widget;

		if ($title)

			echo $before_title . $title . $after_title;

		if($listingsToShow>50){
			$listingsToShow = 50;
		}

		$apiRequestParams = array();

		$apiRequestParams["directive.ResultsPerPage"] = $listingsToShow;

		$apiRequestParams["responseDirective.ViewNameSuffix"] = "widget";

		$apiRequestParams["responseDirective.DefaultDisplayType"] = $defaultDisplay;

		$apiRequestParams['responseDirective.IncludeDisclaimer'] = 'true';

		$apiRequestParams["directive.SortOrdersColumn"] = $sort;

		//$apiRequestParams["directive.SortDirection"] = sort;


		if ($querySource == "area") {

			switch ($areaSourceConfig["type"]) {

				case "city":

					$typeKey = "query.Cities";

					break;

				case "community":

					$typeKey = "query.Communities";

					break;

				case "tract":

					$typeKey = "query.TractIdentifiers";

					break;

				case "zip":

					$typeKey = "query.ZipCodes";

					break;

			}

			$apiRequestParams[$typeKey] = $areaSourceConfig["name"];

		} else if ($querySource == "link") {

			$apiRequestParams["query.ForceUsePropertySearchConstraints"] = "true";

			$apiRequestParams["query.LinkID"] = $linkSourceConfig["linkId"];

		} else if ($querySource == "agentlistings") {

			if (isset($options['AgentID']) && !empty($options['AgentID'])) $apiRequestParams["query.ListingAgentID"] = $options['AgentID'];

		} else if ($querySource == "officelistings") {

			if (isset($options['OfficeID']) && !empty($options['OfficeID'])) $apiRequestParams["query.ListingOfficeID"] = $options['OfficeID'];

		}

		

		$apiHttpResponse = i9_ApiRequest::FetchData("listings", $apiRequestParams);

		if (empty($apiHttpResponse["errors"]) && $apiHttpResponse["response"]["code"] == "200") {

			$data = $apiHttpResponse["body"];

		} else {

			switch ($apiHttpResponse["response"]["code"]) {

				case 403:

					$data = '<p class="i9idx-error">'.I9-IDXPRESS_INACTIVE_ACCOUNT_MESSAGE.'</p>';

				break;

				default:

					$data = '<p class="i9idx-error">'.I9-IDXPRESS_IDX_ERROR_MESSAGE.'</p>';

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

			"title"				=> "Latest Real Estate",

			"listingsToShow"	=> "20",

			"defaultDisplay"	=> "listed",

			"sort"				=> "listprice",

			"querySource"		=> "area",

			"areaSourceConfig"	=> array(

				"type"			=> "city",

				"name"			=> ""

			),

			"linkSourceConfig"	=> array(

				"linkId"		=> ""

			)

		));

		$titleFieldId = $this->get_field_id("title");

		$titleFieldName = $this->get_field_name("title");


		$baseFieldId = $this->get_field_id("listingsOptions");

		$baseFieldName = $this->get_field_name("listingsOptions");



		$checkedDefaultDisplay = array($instance["defaultDisplay"] => "checked=\"checked\"");

		$checkedQuerySource = array($instance["querySource"] => "checked=\"checked\"");

		$selectedAreaType = array($instance["areaSourceConfig"]["type"] => "selected=\"selected\"");

		$selectedAreaTypeNormalized = ucwords($instance["areaSourceConfig"]["type"]);

		$selectedSortOrder = array($instance["sort"] => "selected=\"selected\"");

		

		//$selectedLink = array($instance["linkSourceConfig"]["linkId"] => "selected=\"selected\"");



		//$availableLinks = i9_ApiRequest::FetchData("AccountAvailableLinks", array(), true, 0);

		//$availableLinks = json_decode($availableLinks["body"]);

		//$pluginUrl = i9_PLUGIN_URL;



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

				<input id="{$baseFieldId}[listingsToShow]" name="{$baseFieldName}[listingsToShow]" value="{$instance[listingsToShow]}" class="widefat no_of_listings" type="text" maxlength="2"/>
				<span class="max_listing" style="color:red;"></span>
			</p>

			<p>

				<label for="{$baseFieldId}[sort]">Sort order</label>

				<select id="{$baseFieldId}[sort]" name="{$baseFieldName}[sort]" class="widefat">

					<option value="listprice" {$selectedSortOrder[listprice]}>Price High to Low</option>

					<option value="listprice_asc" {$selectedSortOrder[listprice_asc]}>Price Low to High</option>

					<option value="popularity" {$selectedSortOrder[popularity]}>Most Popular</option>

					<option value="bedrooms" {$selectedSortOrder[bedrooms]}>Bedrooms High to Low</option>
                    
					<option value="fullbaths" {$selectedSortOrder[fullbaths]}>Bathrooms High to Low</option>
					
                    <option value="acreage" {$selectedSortOrder[acreage]}>Acreage High to Low</option>
					
                    <option value="acreage_asc" {$selectedSortOrder[acreage_asc]}>Acreage Low to High</option>
					
                    <option value="yearbuilt" {$selectedSortOrder[yearbuilt]}>Year Built (Newest)</option>
					
                    <option value="yearbuilt_asc" {$selectedSortOrder[yearbuilt_asc]}>Year Built (Oldest)</option>
					
                    <option value="importdate" {$selectedSortOrder[importdate]}>Days on Site (Newest)</option>
					
                    <option value="importdate_asc" {$selectedSortOrder[importdate_asc]}>Days on Site (Oldest)</option>

				</select>

			</p>

			<p>

				<input type="radio" name="{$baseFieldName}[defaultDisplay]" id="{$baseFieldId}[defaultDisplay-listed]" value="listed" {$checkedDefaultDisplay[listed]}/>

				<label for="{$baseFieldId}[defaultDisplay-listed]">Show in list by default</label>

				<br />

				<input type="radio" name="{$baseFieldName}[defaultDisplay]" id="{$baseFieldId}[defaultDisplay-slideshow]" value="slideshow" {$checkedDefaultDisplay[slideshow]}/>

				<label for="{$baseFieldId}[defaultDisplay-slideshow]">Show slideshow details by default</label>

				<br />

				<input type="radio" name="{$baseFieldName}[defaultDisplay]" id="{$baseFieldId}[defaultDisplay-expanded]" value="expanded" onclick="document.getElementById('{$baseFieldId}[listingsToShow]').value = 4;" {$checkedDefaultDisplay[expanded]}/>

				<label for="{$baseFieldId}[defaultDisplay-expanded]">Show expanded details by default</label>

				<br />

				<input type="radio" name="{$baseFieldName}[defaultDisplay]" id="{$baseFieldId}[defaultDisplay-map]" value="map" {$checkedDefaultDisplay[map]}/>

				<label for="{$baseFieldId}[defaultDisplay-map]">Show on map by default</label>

			</p>



			<div class="widefat" style="border-width: 0 0 1px; margin: 20px 0;"></div>



			<!--<table>

				<tr>

					<td style="width: 20px;"><p><input type="radio" name="{$baseFieldName}[querySource]" id="{$baseFieldId}[querySource-area]" value="area" {$checkedQuerySource[area]}/></p></td>

					<td><p><label for="{$baseFieldId}[querySource-area]">Pick an area</label></p></td>

				</tr>

				<tr>

					<td></td>

					<td>

						<p>

							<label for="{$baseFieldId}[areaSourceConfig][type]">Area type</label>

							<select id="{$baseFieldId}_areaSourceConfig_type" name="{$baseFieldName}[areaSourceConfig][type]" class="widefat" onchange="dsWidgetListings.SwitchType(this, '{$baseFieldId}_areaSourceConfig_title')">

								<option value="city" {$selectedAreaType[city]}>City</option>

								<option value="community" {$selectedAreaType[community]}>Community</option>

								<option value="zip" {$selectedAreaType[zip]}>Zip Code</option>

							</select>

						</p>



						<p>

							<label for="{$baseFieldId}[areaSourceConfig][name]">Area name</label>

							<input id="{$baseFieldId}[areaSourceConfig][name]" name="{$baseFieldName}[areaSourceConfig][name]" class="widefat" type="text" value="{$instance[areaSourceConfig][name]}" />

						</p>



						<p>

							<span class="description">See all <span id="{$baseFieldId}_areaSourceConfig_title">{$selectedAreaTypeNormalized}</span> Names <a href="javascript:void(0);" onclick="dsWidgetListings.LaunchLookupList('{$pluginUrl}locations.php', '{$baseFieldId}_areaSourceConfig_type')">here</a></span>

						</p>

					</td>

				</tr>

				<tr>

					<th colspan="2"><p> - OR - </p></th>

				</tr>

				<tr>

					<td valign="top"><p><input type="radio" name="{$baseFieldName}[querySource]" id="{$baseFieldId}[querySource-agentlistings]" value="agentlistings" {$checkedQuerySource[agentlistings]}/></p></td>

					<td>

						<p><label for="{$baseFieldId}[querySource-agentlistings]">My own listings (via agent ID, newest listings first)</label></p>

						<p><i>{$agentListingsNote}</i></p>

					</td>

				</tr>

				<tr>

					<th colspan="2"><p> - OR - </p></th>

				</tr>

				<tr>

					<td valign="top"><p><input type="radio" name="{$baseFieldName}[querySource]" id="{$baseFieldId}[querySource-officelistings]" value="officelistings" {$checkedQuerySource[officelistings]}/></p></td>

					<td>

						<p><label for="{$baseFieldId}[querySource-officelistings]">My office listings (via office ID, newest listings first)</label></p>

						<p><i>{$officeListingsNote}</i></p>

					</td>

				</tr>

HTML;

		if (!defined('ZPRESS_API')) {

			echo <<<HTML

		

				<tr>

					<th colspan="2"><p> - OR - </p></th>

				</tr>

				<tr>

					<td><p><input type="radio" name="{$baseFieldName}[querySource]" id="{$baseFieldId}[querySource-link]" value="link" {$checkedQuerySource[link]}/></p></td>

					<td><p><label for="{$baseFieldId}[querySource-link]">Use a link you created in your website control panel</label></p></td>

				</tr>

				<tr>

					<td></td>

					<td>

						<p>

							<select name="{$baseFieldName}[linkSourceConfig][linkId]" class="widefat">

HTML;

			//foreach ($availableLinks as $link) {

			//	echo "<option value=\"{$link->LinkID}\" {$selectedLink[$link->LinkID]}>{$link->Title}</option>";

			//}

			echo <<<HTML

							</select>

						</p>

					</td>

				</tr>

HTML;

		}

		echo <<<HTML

			</table>-->

			<script> jQuery(document).ready(function() { 
				jQuery('.no_of_listings').change(function(e){
					e.preventDefault();
					var a = jQuery(this).val();
					if(isNaN(a) || parseInt(a)>50){
						jQuery('.max_listing').html('Max Listings Exceeded [Max Listings is 50 by default]');
						jQuery('.no_of_listings').val(50);
					} else {
						jQuery('.max_listing').html('');
					}
				}) 
			}); </script>

HTML;

	}

}

?>