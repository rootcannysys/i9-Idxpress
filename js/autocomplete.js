jQuery().ready(function($) {

	var cache = {};

	var omnibox = $('.i9idx-search-omnibox-autocomplete');

	google.maps.event.addDomListener(window, 'load', initialize);
	
	if(omnibox.length > 0){

		$('.i9idx-search-omnibox-autocomplete').each(function() { 
		
		
		$(this).autocomplete({
			
			source: function(request, response) {

				var term = request.term;

			
				// since we no longer know what the correct search type is, revert to the default

				$(this.element).attr('name', 'location');

			

				// check if we've cached this autocomplete locally

				//if (term in cache) {

				//	response(cache[term]);

				//	return;

				//}

			

				// load autocomplete data

				var pluginUrl = locali9idx.pluginUrl;

				$.getJSON(pluginUrl + 'client-assist.php?action=AutoComplete', request, function(data) {

					if ($.isEmptyObject(data)) {

						data = [{'Name': 'No locations, addresses found', 'Type': 'Error'}]

					}

					//cache[term] = data;

					response(data);

				});
				
			},
			
			select: function(event, ui) {

				if (ui.item.Type != 'Error') {

					/*if (ui.item.Type == 'Listing' && ui.item.SupportingInfo.indexOf('MLS Number;') != -1) {

						// redirect MLS selection to the details page

						var idx_pos = window.location.pathname.indexOf('/canny');

						if (idx_pos > -1) {

							var path = window.location.pathname.slice(0, idx_pos + 5);

							var url  = path + 'mls-' + ui.item.Name + '-';

						} else {

							var url = locali9idx.homeUrl + '/canny/mls-' + ui.item.Name + '-';

						}

					

						window.location = url;

					} else if (ui.item.Type == 'Listing' && ui.item.SupportingInfo.indexOf('Address;') != -1) {

						$(this).attr('name', 'idx-q-AddressMasks<0>');

						$(this).after('<input type="hidden" id="i9idxpress-auto-listing-status" name="idx-q-ListingStatuses" value="15" />');// add listing status = all

					} else if (ui.item.Type == 'County') {

						$(this).attr('name', 'idx-q-Counties<0>');

						$('#i9idxpress-auto-listing-status').remove();

					} else if (ui.item.Type == 'Zip') {

						$(this).attr('name', 'canny-q-ZipCodes<0>');

						$('#i9idxpress-auto-listing-status').remove();

					} else {

						$(this).attr('name', 'location');

						$('#i9idxpress-auto-listing-status').remove();

					}*/


					$(this).val(ui.item.Name);

				}

			

				return false;

			},

			selectFirst: true,

		}).data("ui-autocomplete")._renderItem = function(ul, item) {

			//console.log(item);

			var name = (item.Type == 'County') ? item.Name + ' (County)' : item.Name;

			return $('<li>').data('ui-autocomplete-item', item).append('<a>' + name + '</a>').appendTo(ul);

		}

		});

		$('ul.ui-autocomplete').addClass('i9idx-ui-widget');

	}
	
	// listing widget links click
	$('.i9_panels').click(function(e) {
		//alert($(this).attr('data-panel'));
		if($(this).attr('data-panel')=="expanded"){
			$('.featured-listing').show();
				$("#pan-det").css("background-color", "#000");
				$("#pan-det a").css("color", "#fff");
		
			$('.small-listing').hide();
				$("#pan-li").css("background-color", "transparent");
				$("#pan-li a").css("color", "#000");
			$('.slide-listing').hide();
				$("#pan-sli").css("background-color", "transparent");
				$("#pan-sli a").css("color", "#000");
			$('#listingMap2').hide();
				$("#pan-map").css("background-color", "transparent");
				$("#pan-map a").css("color", "#000");
		}
		if($(this).attr('data-panel')=="list"){
			$('.featured-listing').hide();
				$("#pan-det").css("background-color", "transparent");
				$("#pan-det a").css("color", "#000");
		
			$('.small-listing').show();
				$("#pan-li").css("background-color", "#000");
				$("#pan-li a").css("color", "#fff");
			
			$('.slide-listing').hide();
				$("#pan-sli").css("background-color", "transparent");
				$("#pan-sli a").css("color", "#000");
			$('#listingMap2').hide();
				$("#pan-map").css("background-color", "transparent");
				$("#pan-map a").css("color", "#000");
		}
		if($(this).attr('data-panel')=="slideshow"){
			$('.featured-listing').hide();
				$("#pan-det").css("background-color", "transparent");
				$("#pan-det a").css("color", "#000");
			$('.small-listing').hide();
				$("#pan-li").css("background-color", "transparent");
				$("#pan-li a").css("color", "#000");
			
			$('.slide-listing').show();
				$("#pan-sli").css("background-color", "#000");
				$("#pan-sli a").css("color", "#fff");
			
			$('#listingMap2').hide();
				$("#pan-map").css("background-color", "transparent");
				$("#pan-map a").css("color", "#000");
		}
		if($(this).attr('data-panel')=="map"){
			$('.featured-listing').hide();
				$("#pan-det").css("background-color", "transparent");
				$("#pan-det a").css("color", "#000");
			$('.small-listing').hide();
				$("#pan-li").css("background-color", "transparent");
				$("#pan-li a").css("color", "#000");
			$('.slide-listing').hide();
				$("#pan-sli").css("background-color", "transparent");
				$("#pan-sli a").css("color", "#000");
			
			$('#listingMap2').show();
				$("#pan-map").css("background-color", "#000");
				$("#pan-map a").css("color", "#fff");
			
			$('#listingMap2').css('height','290px');
			$('#listingMap2').css('margin-bottom','15px');
			listingmap(2);
		}
		
    });
	
	// sort by function
	$('#sort').change(function(e) {
		
		var srt = $('#sort').val();
		//alert(locali9idx.homeUrl+"/canny/page-1?"+locali9idx.qrystr+"&sort="+srt);
        window.location = locali9idx.homeUrl+"/canny/page-1?"+locali9idx.qrystr+"&sort="+srt;
		
    });
	$('#sort2').change(function(e) {
		
		var srt = $('#sort2').val();
		var tag = $('#tag').val();
		var data = $('#datas').val();
		
		var url = locali9idx.homeUrl+'/canny/'+tag+'/'+data+'/page-1?sort='+srt;
		//alert(url);
        window.location = url;
		
    });
	
	$('.mapdiv').click(function(e) {
		
		$('.mapdesc').toggle();
    
	});
	
	$('#emailfrnd').click(function(e) {
		
		$('#cannysys-share').hide();
		$('#cannysys-share-email').show();
		$('#cannysys-calc').hide();
    
	});
	$('#mapb').click(function(e) {
		
		$('.owl-listitem').hide();
		$("#groupb").css("background-color", "#FFFFFF");
		
		//alert("mapyyy");
		$("#mapb").css("background-color", "#BDBDBD");
		
		$('#maping').show();
		
		listingmap(1);
    
	});
	
	$('#groupb').click(function(e) {
		
		$("#mapb").css("background-color", "#FFFFFF");		
		$("#groupb").css("background-color", "#BDBDBD");
		$('#maping').hide();
		$('.owl-listitem').show();
		
	});
	
	$('.email-cancel').click(function(e) {
        $('#cannysys-share-email').hide();
    });
	
	$('#share').click(function(e) {
		$('#cannysys-share').toggle();
		$('#cannysys-share-email').hide();
		$('#cannysys-calc').hide();
    });	
	
	// calculator function
	$('#calc').click(function(e) {
		$('#cannysys-calc').toggle();
		$('#cannysys-share-email').hide();
		$('#cannysys-share').hide();
		calc();
	});
	$('#eclose').click(function(e) {
		$('#cannysys-calc').hide();
	});
	function calc(){
		
		if(!$.isNumeric($('#intrate').val()) || $('#intrate').val()>100){ alert('enter correct Interest Rate'); return false;}
		if(!$.isNumeric($('#cprice').val())){  alert('enter correct Price'); return false;}
		if(!$.isNumeric($('#downpay').val()) || $('#downpay').val()>100){  alert('enter correct DownPay'); return false;}
		if(!$.isNumeric($('#esttax').val()) || $('#esttax').val()>100){  alert('enter correct Est.Tax Rate'); return false;}
		
		var down=parseFloat($('#downpay').val());
		var loanprincipal=parseFloat($('#cprice').val());
		var e=document.getElementById("cyears");
		var months= parseFloat(e.options[e.selectedIndex].value)*12;
		var interest=parseFloat($('#intrate').val())/1200;
		var tx=loanprincipal*parseFloat($('#esttax').val())/100;
		var val=loanprincipal*(100-down)/100;
		var amt=parseFloat((val*interest/(1-(Math.pow(1/(1+interest),months)))).toFixed(2))+parseFloat((tx/12));
		
		$('.emi').html(Math.round(amt));
		$('#loanamount').html(Math.round(val));
		$('#esttaxspan').html("( Est.Tax - $"+Math.round(tx/12)+" )");

	}
	$('#estcal').submit(function(e){
		e.preventDefault();
		calc();
	});

	//contact validation
	$('#cont_formsubmit').on('click',function(e){
		//var phoneno = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;  
		var cont = $('#cont_phone').val(); 
		
		if( cont.length != 10 || isNaN(cont)){
			alert('enter correct phone no.');
			return false;
		}
		
	});
	
	// shedule checkbox code
	if($('#cont_schedule').is(":checked")){
		$('#shedulerow').show();
	}
	else{
		$('#shedulerow').hide();
	}
	$('#cont_schedule').on('click',function(e) {

		if($('#cont_schedule').is(":checked")){
			$('#shedulerow').show();
		}
		else{
			$('#shedulerow').hide();
		}
        
    });	
	
	listingmap(2);
	
	function listingmap(x){
		
		var map;
		var bounds = new google.maps.LatLngBounds();
		var mapOptions = {
			mapTypeId: 'roadmap',
		};
						
		// Display a map on the page
		if(x=='1'){
			
			map = new google.maps.Map(document.getElementById("listingMap"), mapOptions);
			var lats = document.getElementById('arrlat').value;
			var lans = document.getElementById('arrlan').value;
			var citys = document.getElementById('arrcity').value;
			var infos = document.getElementById('arrinfo').value;
			var lkeys = document.getElementById('arrlkey').value;
			
			var zipbound = document.getElementById('zipbounds').value;
			
			// draw polygon code
			var zbound = zipbound.split(",");
			//console.log(zbound);
			
			var coordArray = [];
			var x1,x2;
			for(var j = 0; j < zbound.length-1; j++){ //use length-1 because of ending comma in data string
			    
				var googleLatLng = new google.maps.LatLng(zbound[j+1],zbound[j]);
				bounds.extend(googleLatLng);
				coordArray.push(googleLatLng);
				x1 = zbound[j+1];x2 = zbound[j];
				j=j+1;
			}
			
			//console.log(coordArray);
			var mapOptions = {
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				
				map = new google.maps.Map(document.getElementById("listingMap"), mapOptions);
				bermudaTriangle = new google.maps.Polygon({
					paths: coordArray,
					strokeColor: "#FF0000",
					strokeOpacity: 1,
					strokeWeight: 3,
					fillColor: "transparent",
					fillOpacity: 0.35,
					editable: false
				});
		
			  bermudaTriangle.setMap(map); // end polygon-code 
			
		}
		else{
			map = new google.maps.Map(document.getElementById("listingMap2"), mapOptions);
			var lats = document.getElementById('arrlat1').value;
			var lans = document.getElementById('arrlan1').value;
			var citys = document.getElementById('arrcity1').value;
			var infos = document.getElementById('arrinfo1').value;
			var lkeys = document.getElementById('arrlkey1').value;
			
		}
		map.setTilt(45);
			
		// Multiple Markers
		//var markers = [
//			['London Eye, London', 51.503454,-0.119562],
//			['Palace of Westminster, London', 51.499633,-0.124755]
//		];
	
		// Info Window Content
		//var infoWindowContent = [
//			['<div class="info_content">' +
//			'<h3>London Eye</h3>' +
//			'<p>The London Eye is a giant Ferris wheel situated on the banks of the River Thames. The entire structure is 135 metres (443 ft) tall and the wheel has a diameter of 120 metres (394 ft).</p>' +        '</div>'],
//			['<div class="info_content">' +
//			'<h3>Palace of Westminster</h3>' +
//			'<p>The Palace of Westminster is the meeting place of the House of Commons and the House of Lords, the two houses of the Parliament of the United Kingdom. Commonly known as the Houses of Parliament after its tenants.</p>' +
//			'</div>']
//		];

		var lat = lats.split(",");
		var lan = lans.split(",");
		var city = citys.split(",");
		var info = infos.split(";");					
		var lkey = lkeys.split(",");
		
		// Display multiple markers on a map
		var infoWindow = new google.maps.InfoWindow(), marker, i;
		
		// Loop through our array of markers & place each one on the map  
		for( i = 0; i < lat.length; i++ ) {
			
			//var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
			var position = new google.maps.LatLng(lat[i], lan[i]);
			bounds.extend(position);
			marker = new google.maps.Marker({
				position: position,
				map: map,
				title: city[i]
			});
			
			// Allow each marker to have an info window    
			google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
				return function() {
					infoWindow.setContent(info[i]);
					infoWindow.open(map, marker);
				}
			})(marker, i));
			google.maps.event.addListener(marker, 'mouseout', (function(marker, i) {
				return function() {
					infoWindow.close();
				}
			})(marker, i));
			
			//click function on marker
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					//alert(locali9idx.homeUrl+'/canny/lte-'+lkey[i]);
					window.location = locali9idx.homeUrl+'/canny/lte-'+lkey[i];
				}
			})(marker, i));
	
			// Automatically center the map fitting all markers on the screen
			map.fitBounds(bounds);
		}
	
		// Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
		var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
			//this.setZoom(10);
			google.maps.event.removeListener(boundsListener);
		});
		
	}
	
	function initialize() {
		
		var lat = document.getElementById("latt").value; //'51.508742';
		var lan = document.getElementById("lann").value; //'-0.120850';
		var city = document.getElementById("cityy").value; 
		
	  var mapProp = {
		center:new google.maps.LatLng(lat,lan),
		zoom:18,
		mapTypeId:google.maps.MapTypeId.HYBRID
	  };
		  
	  var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
	  
	  var marker=new google.maps.Marker({position:new google.maps.LatLng(lat,lan)});
	  marker.setMap(map);
	  
	  //neighbor photos panaromio
	  
	 	var swlat = lat-0.006;
		var swlng = lan-0.018;
		var nelat = lat+0.006;
		var nelng = lan+0.018;
		var myRequest = {
				  	//'tag': city,	
				  	'rect' : {'sw': {'lat': swlat, 'lng': swlng}, 'ne': {'lat': nelat, 'lng': nelng}},
					//  'tag': 'water',
					//  'rect': {'sw': {'lat': 39.289236, 'lng': -76.589383}, 'ne': {'lat': 39.299236, 'lng': -76.569383}}
				};
				var myOptions = {
				  'width': 600,
				  'height': 500
				};
		//var widget = new panoramio.PhotoWidget('neighborphotos', myRequest, myOptions);
		//widget.setPosition(0);
		
		  
	}

	
});

