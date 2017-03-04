// JavaScript Document
var $ = jQuery;
jQuery().ready(function($) {

	// login or register show/hide code
	$('.regshow').click(function(e) {
        $('#regdiv').show();
		$('#logindiv').hide();
		$('#forgotdiv').hide();
    });
	
	$('.loginshow').click(function(e) {
        $('#regdiv').hide();
		$('#logindiv').show();
		$('#forgotdiv').hide();
    });

    $('#forgot_pwd').click(function(){
    	$('#regdiv').hide();
		$('#logindiv').hide();
		$('#forgotdiv').show();
    })
	
	// contact form
	$('#contactForm').submit(function(e) {
		e.preventDefault(); //STOP default action
		var patAlf=/^[A-Za-z\s]+$/;
		var fname = $('#cont_firstName').val().trim();
		if(!fname.match(patAlf)){
        	alert('Enter correct FirstName');
        	$('#cont_firstName').val('');
            $('#cont_firstName').focus();
            return false;
        }
        var lname = $('#cont_lastName').val().trim();
        if(!lname.match(patAlf)){
        	alert('Enter correct LastName');
        	$('#cont_lastName').val('');
            $('#cont_lastName').focus();
            return false;
        }
        var pat=/^[a-zA-Z]+[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$/;
        var email = $('#cont_email').val().trim();
        if(!email.match(pat)){
        	alert('Enter correct Email');
        	$('#cont_email').val('');
            $('#cont_email').focus();
            return false;
        }
		var mno = $('#cont_phone').val().trim();
		if(mno.length != 10 || isNaN(mno) || Number(mno) == 0  || mno.charAt(0)==0){
			alert('Enter correct MobileNo');
			$('#cont_phone').val('');$('#cont_phone').focus();
			return false;
		}
		
		var postData = $(this).serializeArray();
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=Contact';
		//console.log(postData);return false;
	
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			success:function(data, textStatus, jqXHR){
				if(data == 0){
					alert('Entered Email Not Registered with us');
					$('#loginModal').modal('show');
				} else {
					alert('Request send successfully');
					//window.location.reload();
				}
				$("#contactForm")[0].reset();
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});
		$("#contactForm")[0].reset();
	});
	
	// listing email 
	$('#emaillistingForm').submit(function(e) {
		e.preventDefault(); //STOP default action

		var pat=/^[a-zA-Z]+[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$/;
        var email = $('#email_from').val().trim();
        if(!email.match(pat)){
        	alert('Enter correct Email');
        	$('#email_from').val('');
            $('#email_from').focus();
            return false;
        }
        var email2 = $('#email_to').val().trim();
        if(!email2.match(pat)){
        	alert('Enter correct Email');
        	$('#email_to').val('');
            $('#email_to').focus();
            return false;
        }

		var postData = $(this).serializeArray();
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=EmailListing';
		
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			success:function(data, textStatus, jqXHR){
				if(data == 0){
					alert('Entered Email Not Registered with us');
				} else {
					alert('Mail send successfully.');
					window.location.reload();
				}
				$("#emaillistingForm")[0].reset();
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});
		$("#emaillistingForm")[0].reset();
		
    });
	
	// submit register form 
	$('#regfrm').submit(function(e) {

		var patAlf=/^[A-Za-z\s]+$/;
		var fname = $('#fname').val().trim();
		if(!fname.match(patAlf)){
        	alert('Enter correct FirstName');
        	$('#fname').val('');
            $('#fname').focus();
            return false;
        }
        var lname = $('#lname').val().trim();
        if(!lname.match(patAlf)){
        	alert('Enter correct LastName');
        	$('#lname').val('');
            $('#lname').focus();
            return false;
        }
        var pat=/^[a-zA-Z]+[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}$/;
        var email = $('#emailid').val().trim();
        if(!email.match(pat)){
        	alert('Enter correct Email');
        	$('#emailid').val('');
            $('#emailid').focus();
            return false;
        }
		
		var mno = $('#mno').val().trim();
		if(mno.length != '10' || isNaN(mno) || Number(mno) == 0  || mno.charAt(0)==0){
			alert('Enter correct MobileNo');
			$('#mno').val('');$('#mno').focus();
			return false;
		}
		if($('#agree').prop('checked')!= true){
			alert('Please Agree Our Terms');
			return false;
		}
		
		var postData = $(this).serializeArray();
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=Register';
		$('#btnre2').attr("disabled",true);
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			success:function(data, textStatus, jqXHR){
				$('#btnre2').attr("disabled",false);
				if(data>0){
					localStorage.setLoginFlag = 0;
					alert("Registered Successfullly, Please Check Your Email");
					window.location.reload();
				} else {
					alert(data);
				}
				$('#loginModal').modal('hide');
				$("#regfrm")[0].reset();
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});
		e.preventDefault(); //STOP default action
        
    });
	
	// submit login form 
	$('#loginfrm').submit(function(e) {
		
		var postData = $(this).serializeArray();
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=Login';
		//	alert(formURL);
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			success:function(data, textStatus, jqXHR){
				
				if(data>0){
					localStorage.setLoginFlag = 1;
					alert("login success");
					window.location.reload();
				}
				else{
					alert(data);
				}
				
				$('#loginModal').modal('hide');
				$("#loginfrm")[0].reset();
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});
		e.preventDefault(); //STOP default action
    });

    // forgot password form
    $('#forgotfrm').submit(function(e){
    	e.preventDefault();
    	
    	var mno = $('#uphone').val().trim();
		if(mno.length != '10' || isNaN(mno) || Number(mno) == 0  || mno.charAt(0)==0){
			$('#uphone').val('');$('#uphone').focus();
			return false;
		}
		var postData = $(this).serializeArray();
    	
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=ForgotPwd';
		$('.forgot_send_btn').prop('disabled',true);
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			success:function(res, textStatus, jqXHR){
				if(res==1){
					alert("Please check your registered email");
					//window.location.reload();
				} else if (res == 2) {
					alert("Request Not Submitted");
				} else {
					alert("Entered Wrong Credentials");
				}
				$('.forgot_send_btn').prop('disabled',false);
				$('#loginModal').modal('hide');
				$("#forgotfrm")[0].reset();
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});
    })
	
	
	/// log out
	$('#logoutbtn').click(function(e) {

		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=Logout';
		$.post(formURL,function(data){
			if(data == '1'){
				localStorage.setLoginFlag = 0;
				localStorage.setIdxFlag = 0;
				alert('logout successfullly');
				window.location.reload();
			}
		});
		e.preventDefault();

    });
	
	
	// favorites code
	checkfav();
	function checkfav(){

		var pluginUrl = locali9idx.pluginUrl;
		var keys = $('#arrlkey').val();
		$.ajax({
			url : pluginUrl+"client-assist.php?action=Checkfav",
			type: "POST",
			dataType: "JSON",
			data : { key : keys },
			success:function(data, textStatus, jqXHR){
				
				if(data != '2'){
					$('#loginbtn').hide();
					$('.logspan').show();
					$('#logoutbtn').show();
					$('#savesearchbtn').show();
					localStorage.setLoginFlag = 1;
					
				} else {
					$('#loginbtn').show();
					$('.logspan').hide();
					$('#logoutbtn').hide();
					$('#savesearchbtn').hide();
					localStorage.setLoginFlag = 0;
					setcacheFlag(function(d){});
				}
				if(typeof data !=='undefined'){
					$.each(data,function(k,val){
						if(val["status"]==1){
							$('#fav-'+val["key"]).css('color','#007acc');	
						}else{
							$('#fav-'+val["key"]).css('color','#6f6f6f');	
						}
					});
				}
				
			}
		});
		var keys1 = $('#arrlkey1').val();
		$.ajax({
			url : pluginUrl+"client-assist.php?action=Checkfav",
			type: "POST",
			dataType: "JSON",
			data : { key : keys1 },
			success:function(data, textStatus, jqXHR){
				
				$.each(data,function(k,val){
					if(val["status"]==1){
						$('#fav1-'+val["key"]).css('color','#007acc');	
					}else{
						$('#fav1-'+val["key"]).css('color','#6f6f6f');	
					}
				});
				
			}
		});

	}
	
	$('.favclass').click(function(e) {
		var key = $(this).data('id');
		
		changefav(key);
		//$(this).children('span').css('color','#007acc');
        e.preventDefault();
    });
	
	function changefav(key){
		
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=Changefav';
		
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : { key : key },
			success:function(data, textStatus, jqXHR){
				
				if(data == '1'){
					//$('#favorite').children('span').css('color','#007acc');
					$('#fav-'+key).css('color','#007acc');
					$('#fav1-'+key).css('color','#007acc');	
					window.location.reload();
				}
				else if(data == '0'){
					//$('#favorite').children('span').css('color','#fff');
					$('#fav-'+key).css('color','#6f6f6f');
					$('#fav1-'+key).css('color','#6f6f6f');	
					window.location.reload();
				}
				else
				alert('login to add favorite');
				
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});	
	}

	$('.fav').click(function(e) {
		var key = $(this).data('id');
		changefav(key);
        e.preventDefault();
    });
	
	$('#favbtn').click(function(e) {
		
		//alert(locali9idx.homeUrl+'/canny/favorite');
		window.location = locali9idx.homeUrl+'/canny/favorite';
		
    });
	
	
	// save search functions
	$('#savesearchfrm').submit(function(e) {
		
		var postData = $(this).serializeArray();
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=Savesearch';
		
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : postData,
			success:function(data, textStatus, jqXHR){
				//alert(data);
				if(data=='1'){
					alert("save search successfully");
					window.location.reload();
				}
				else if(data=='0'){
					alert("change search name.");
				}
				else{
					alert('login to save search.');
				}
				$('#savesearchModal').modal('hide');
				$("#savesearchfrm")[0].reset();
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});
        e.preventDefault();
    });
	
	$('#managebtn').click(function(e) {
       
		var pluginUrl = locali9idx.pluginUrl;
		var formURL = pluginUrl + 'client-assist.php?action=Managesearch';
		
		$.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : { managesrch : 1 },
			success:function(data, textStatus, jqXHR){
				if(data == '2')
				alert('login to manage save search');
				else{
					var mes = '<thead><tr><th>Name</th><th>CreateDate</th><th>Delete</th></tr></thead><tbody >';
					
					$.each(data, function(i, item) {
						mes+='<tr><td><a href="'+item.searchLink+'">'+item.searchName+'</a></td><td>'+item.createDate+'</td><td><a onclick="return delsearch('+item.saveSearchId+')"><i class="fa fa-times"></i></a></td></tr>';	
					});
					mes+='</tbody>';
					$('#searchtable').html(mes);
				}
				
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails      
			}
		});
    });
	
	
	$(".I9_item").mouseover(function(){
			
		$(this).find(".button1").css("display","block");
		$(this).find(".fav").css("display","block");
		
	});
	
	$(".I9_item").mouseout(function(){
	
		$(this).find(".button1").css("display","none");
		$(this).find(".fav").css("display","none");
		
	});
	
	$(".flimg").mouseover(function(){
			
		$(this).find(".button1").css("display","block");
		$(this).find(".fav").css("display","block");
		
	});
	
	$(".flimg").mouseout(function(){
	
		$(this).find(".button1").css("display","none");
		$(this).find(".fav").css("display","none");
		
	});
	
	// property detail function
	$('.button1').on('click',function(e) {
		//alert($(this).data('id'));
		var key = $(this).data('id');

		setcacheFlag(function(d){
			if(d!=0){
				
				//var win = window.open(locali9idx.homeUrl+"/canny/lte-"+key, '_blank');
		  		//win.focus();
				window.location = locali9idx.homeUrl+"/canny/lte-"+key;	
			}
		}); // set flag to storage for login modal display
		
	});

	$('.chk_flag').click(function(){
		
		var link = $(this).data('id');

		setcacheFlag(function(d){
			if(d!=0){
				window.location = link;		
			}
		})
		
	})

	var dt = new Date();
	for(var j=0;j<dt.getMonth();j++){
		$("#cont_month option[value="+(j+1)+"]").hide();
	}
	$("#cont_month option[value="+(dt.getMonth()+1)+"]").attr('selected',true);

	$('#cont_month').on('change', function() {
	  	var val= this.value;
	 	var thirty="30";
	 	var thirtyone="31";

	 	for(var z=1;z<32;z++){
	 		$("#cont_day option[value="+z+"]").show();
	 	}

	 	if(val==4 || val==6 || val== 9 || val==11){
	  		$("#cont_day option[value="+thirty+"]").show();
	  		$("#cont_day option[value="+thirtyone+"]").hide();
	 	}
	 	if(val==1 || val==3 || val== 5 || val==7 || val==8 || val==10 || val==12){
	  		$("#cont_day option[value="+thirty+"]").show();
	  		$("#cont_day option[value="+thirtyone+"]").show();
	 	}
	 	if(val==2){
	  		$("#cont_day option[value="+thirty+"]").hide();
	  		$("#cont_day option[value="+thirtyone+"]").hide();
	  		
	  		if((dt.getFullYear()%4) == 0){
	  			$("#cont_day option[value=29]").show();
	  		} else {
	  			$("#cont_day option[value=29]").hide();
	  		}
	 	}

	 	checkDate();
	})	
	
	checkDate();
});

function checkDate(){
	var $ = jQuery;
	var dt = new Date();
	var mn = $('#cont_month').val();
	
	if(mn == (dt.getMonth()+1)){
		for(var j=1;j<dt.getDate();j++){
			$("#cont_day option[value="+j+"]").hide();
		}
		$("#cont_day option[value="+dt.getDate()+"]").attr('selected',true);
	}

	 	if(mn==4 || mn==6 || mn== 9 || mn==11){
	  		$("#cont_day option[value=30]").show();
	  		$("#cont_day option[value=31]").hide();
	 	}
	 	if(mn==1 || mn==3 || mn== 5 || mn==7 || mn==8 || mn==10 || mn==12){
	  		$("#cont_day option[value=30]").show();
	  		$("#cont_day option[value=31]").show();
	 	}
	 	if(mn==2){
	  		$("#cont_day option[value=30]").hide();
	  		$("#cont_day option[value=31]").hide();
	  		
	  		if((dt.getFullYear()%4) == 0){
	  			$("#cont_day option[value=29]").show();
	  		} else {
	  			$("#cont_day option[value=29]").hide();
	  		}
	 	}
}

function delsearch(id){
	//alert(id);
	var pluginUrl = locali9idx.pluginUrl;
	var formURL = pluginUrl + 'client-assist.php?action=Delsearch';
	jQuery.ajax({
			url : formURL,
			type: "POST",
			dataType: "JSON",
			data : { searchId : id },
			success:function(data, textStatus, jqXHR){
				if(data == '1'){
					alert('deleted successfully.');
					window.location.reload();
				}
				else
				alert("failed");
			}
	});
}

function setcacheFlag(cb){
	
	if (typeof(Storage) !== "undefined") {
		// Code for localStorage/sessionStorage.
		
		if(!localStorage.setIdxFlag){
			localStorage.setIdxFlag = 0;
		}
		if(localStorage.setLoginFlag!=1){
			if(parseInt(localStorage.setIdxFlag)>5){
				$('#loginModal').modal({backdrop: 'static', keyboard: false});
				$('.modal_login_close').hide();
				cb(0);
			} else if(parseInt(localStorage.setIdxFlag)>3){
				localStorage.setIdxFlag = parseInt(localStorage.setIdxFlag)+1;
				$('#loginModal').modal({backdrop: 'static', keyboard: false});
				cb(0);
			} else {
				localStorage.setIdxFlag = parseInt(localStorage.setIdxFlag)+1;
				cb(1);
			}
		} else {
			cb(1);
		}
	} else {
		// Sorry! No Web Storage support..
		console.log("Sorry! No Web Storage support..");
		cb(1);
	}
}
