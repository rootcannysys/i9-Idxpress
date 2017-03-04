<?php
class i9quick_search extends WP_Widget {

    var $widgetsCdn;

    public function __construct() {

        parent::__construct("i9-quicksearch", "I-9 IDX Quick Search", array(
		
            "classname" => "i9-widget-quick-search",
			
            "description" => "Choose either horizontal or vertical format. A simple responsive search form. Allow users to type any location, select from available property types and filter by price range."
			
            ));

       // $this->widgetsCdn = dsWidgets_Service_Base::$widgets_cdn;
    }
 
    public static function shortcodeWidget($values){

        self::renderWidget(array(), $values);

    }

    function widget($args, $instance) { // public so we can use this on our shortcode as well

        self::renderWidget($args, $instance);

    }

    public static function renderWidget($args, $instance){

        extract($args);
        extract($instance);

        $title = apply_filters("widget_title", $title);

        $options = get_option(i9_OPTION_NAME);
		
		if (!isset($options["Activated"]) || !$options["Activated"])
            return;

        $formAction = get_home_url() . "/canny/page-1";

     //   $defaultSearchPanels = \i9_ApiRequest::FetchData("AccountSearchPanelsDefault", array(), false, 60 * 60 * 24);

     //   $defaultSearchPanels = $defaultSearchPanels["response"]["code"] == "200" ? json_decode($defaultSearchPanels["body"]) : null;

     //   $propertyTypes = \i9_ApiRequest::FetchData("AccountSearchSetupFilteredPropertyTypes", array(), false, 60 * 60 * 24);

     //   $propertyTypes = $propertyTypes["response"]["code"] == "200" ? json_decode($propertyTypes["body"]) : null;

     //   $account_options = \i9_ApiRequest::FetchData("AccountOptions");

     //   $account_options = $account_options["response"]["code"] == "200" ? json_decode($account_options["body"]) : null;

        $widgetType = htmlspecialchars($instance["widgetType"]);
		
        $values =array();

        $values['location'] = isset($_GET['location']) ? stripslashes($_GET['location']) : null;

        $values['canny-q-PropertyTypes'] = isset($_GET['canny-q-PropertyTypes']) ? $_GET['canny-q-PropertyTypes'] : null;

        $values['canny-q-PriceMin'] = isset($_GET['canny-q-PriceMin']) ? $_GET['canny-q-PriceMin'] : null;

        $values['canny-q-PriceMax'] = isset($_GET['canny-q-PriceMax']) ? $_GET['canny-q-PriceMax'] : null;

        $specialSlugs = array(
            'city'      => 'canny-q-Cities',
            'community' => 'canny-q-Communities',
            'tract'     => 'canny-q-TractIdentifiers',
            'zip'       => 'canny-q-ZipCodes'
        );
		
        $urlParts = explode('/', $_SERVER['REQUEST_URI']);

        $count = 0;

        foreach($urlParts as $p){

            if(array_key_exists($p, $specialSlugs) && isset($urlParts[$count + 1])){

                $values['location'] = ucwords($urlParts[$count + 1]);

            }

            $count++;

        }

        echo $before_widget;

        if ($title)

            echo $before_title . $title . $after_title;

        $widgetClass = ($widgetType == 1 || $widgetType == 'vertical')?'i9idx-resp-vertical':'i9idx-resp-horizontal';
        
        if(isset($instance['class'])){ //Allows us to add custim class for shortcode etc.

            $widgetClass .= ' '.$instance['class'];

        }

        echo <<<HTML

            <div class="i9idx-resp-search-box {$widgetClass}">

                <form class="i9idx-resp-search-form" action="{$formAction}" method="GET">

                    <fieldset>

                        <div class="i9idx-resp-area i9idx-resp-location-area">

                            <label for="i9idx-resp-location" class="i9idx-resp-location">Location</label>

                            <input placeholder="Search Term" name="location" type="text" class="text i9idx-search-omnibox-autocomplete form-control" id="location" value="" />

                        </div>
<div class="col-xs-12 col-sm-12 col-md-12 searchedarea">
                        <div class="i9idx-resp-area i9idx-resp-type-area types">

                            <label for="i9idx-resp-area-type" class="i9idx-resp-type">Type</label>                      

                            <select id="search_prop_type" class="i9idx-resp-select form-control" name="search_prop_type">

                                <option value="">Any</option>
								<option value="Commercial">Commercial</option>
								<option value="Lot-Land">Lot-Land</option>
								<option value="single family">Single-Family</option>
								<option value="Multi-Family">Multi-Family</option>
								<option value="condos">Condos</option>
								<option value="Townhouse">Townhouse</option>
								<option value="MANUFACTURED HOME">Manufactured Home</option>
								<option value="farm">Farm</option>
								<option value="CL">Commercial-Lease</option>
HTML;

                                
                                echo <<<HTML

                            </select>

                        </div>
						 </div>
						 <div class="searchedarea">

                        <div class="i9idx-resp-area i9idx-quick-resp-min-baths-area i9idx-resp-area-half i9idx-resp-area-left">

                            <label for="search_bedrooms">Beds</label>

                            <select id="search_bedrooms" name="search_bedrooms" class="i9idx-beds form-control">

                                <option value="">Any</option>
HTML;

                                for($i=1; $i<=5; $i++){

                                    $selected = $i == $values['idx-q-BedsMin']?' selected="selected"':'';

                                    echo '<option value="'.$i.'"'.$selected.'>'.$i.'+</option>';

                                }

                            echo <<<HTML

                            </select>

                        </div>

                        <div class="i9idx-resp-area i9idx-quick-resp-min-baths-area i9idx-resp-area-half i9idx-resp-area-right">

                            <label for="search_bathrooms">Baths</label>

                            <select id="search_bathrooms" name="search_bathrooms" class="i9idx-baths form-control">

                                <option value="">Any</option>
HTML;

                                for($i=1; $i<=5; $i++){

                                    $selected = $i == $values['idx-q-BathsMin']?' selected="selected"':'';

                                    echo '<option value="'.$i.'"'.$selected.'>'.$i.'+</option>';

                                }

                            echo <<<HTML

                            </select>

                        </div>
</div>
<div class="searchedarea">
                        <div class="i9idx-resp-area i9idx-quick-resp-price-area i9idx-resp-price-area-min i9idx-resp-area-half i9idx-resp-area-left">

                            <label for="search_minprice" class="i9idx-resp-price">PriceMin</label>

                            <select id="search_minprice" name="search_minprice" class="i9idx-baths form-control">

                                <option value="">Any</option>
								<option value="50000">50000</option>
								<option value="60000">60000</option>
								<option value="70000">70000</option>
								<option value="80000">80000</option>
								<option value="90000">90000</option>
HTML;

                               for($i=100000;$i<=1000000;$i=$i+25000){

                                    $selected = $i == $values['idx-q-BathsMin']?' selected="selected"':'';

                                    echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
									
									if($i>=550000 && $i<=3000000)
									$i=$i+25000;
									else if($i>3000000)
									$i=$i+475000;

                                }

                            echo <<<HTML

                            </select>

                        </div>

                        <div class="i9idx-resp-area i9idx-quick-resp-price-area i9idx-resp-price-area-max i9idx-resp-area-half i9idx-resp-area-right">

                            <label for="search_maxprice" class="i9idx-resp-price">PriceMax</label>

                            <select id="search_maxprice" name="search_maxprice" class="i9idx-baths form-control">

                                <option value="">Any</option>
								<option value="50000">50000</option>
								<option value="60000">60000</option>
								<option value="70000">70000</option>
								<option value="80000">80000</option>
								<option value="90000">90000</option>
HTML;

                                for($i=100000;$i<=1000000;$i=$i+25000){

                                    $selected = $i == $values['idx-q-BathsMin']?' selected="selected"':'';

                                    echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
									
									if($i>=550000 && $i<=3000000)
									$i=$i+25000;
									else if($i>3000000)
									$i=$i+475000;

                                }

                            echo <<<HTML

                            </select>

                        </div>
<div class="searchedarea">
                        <div class="i9idx-resp-area i9idx-resp-area-submit">
							
                            <label for="i9idx-resp-submit" class="i9idx-resp-submit">&nbsp;</label>

                            <input type="submit" class="i9idx-resp-submit" value="Search" />

                        </div>

                    </fieldset>

                </form>

            </div>
HTML;

        //\i9_footer::ensure_disclaimer_exists("search");

        echo $after_widget;

    }

    function update($new_instance, $old_instance) {

        $new_instance["quicksearchOptions"]["title"] = strip_tags($new_instance["title"]);

        $new_instance["quicksearchOptions"]["eDomain"] = $new_instance["eDomain"];

        $new_instance["quicksearchOptions"]["widgetType"] = $new_instance["widgetType"];

        $new_instance = $new_instance["quicksearchOptions"];

        return $new_instance;

    }

    function form($instance) {

     //   wp_enqueue_script('i9idxwidgets_widget_service_admin', plugins_url('js/widget-service-admin.js', __FILE__ ), array('jquery'), false, true);

        $instance = wp_parse_args($instance, array(
            "title" => "Real Estate Search",
            "eDomain" =>   "",
            "widgetType" => 1
            ));

        $title = htmlspecialchars($instance["title"]);

        $widgetType = htmlspecialchars($instance["widgetType"]);

        $widgetTypeFieldId = $this->get_field_id("widgetType");

        $widgetTypeFieldName = $this->get_field_name("widgetType");

        $titleFieldId = $this->get_field_id("title");

        $titleFieldName = $this->get_field_name("title");

        $baseFieldId = $this->get_field_id("quicksearchOptions");

        $baseFieldName = $this->get_field_name("quicksearchOptions");

      //  $apiStub = dsWidgets_Service_Base::$widgets_admin_api_stub;

        echo <<<HTML

        <p>

            <label for="{$titleFieldId}">Widget title</label>

            <input id="{$titleFieldId}" name="{$titleFieldName}" value="{$title}" class="widefat" type="text" />

        </p>

        <p>

            <label>Widget Aspect Ratio</label><br/><br/>

            <input type="radio" name="{$widgetTypeFieldName}" id="{$widgetTypeFieldId}" 
HTML;

        if ($widgetType == '1') echo ' checked'; 

        echo <<<HTML

            value="1"/> Vertical - <i>Recommended for side columns</i><br/>

            <input type="radio" name="{$widgetTypeFieldName}" 
HTML;

        if ($widgetType == '0') echo 'checked'; 

        echo <<<HTML

            value="0"/> Horizontal - <i>Recommended for wider areas</i><br/>

        </p> 
HTML;

}}

?>