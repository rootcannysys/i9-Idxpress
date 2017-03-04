

i9calc = {

	getcalc : function(){

		var $ = jQuery;
		
		var irate = $('#i9idx_interestrate').val();
		var iprice = $('#i9idx_price').val();
		var idwn = $('#i9idx_downpayment').val();
		var itype = $('#i9idx_loan_type').val();
		var itax = $('#i9idx_monthlydebts').val();
		if(!$.isNumeric(irate) || irate>100){ alert('enter correct Interest Rate'); return false;}
		if(!$.isNumeric(iprice)){  alert('enter correct Price'); return false;}
		if(!$.isNumeric(idwn) || idwn>100){  alert('enter correct DownPay'); return false;}
		if(!$.isNumeric(itax) || itax>100){  alert('enter correct Est.Tax Rate'); return false;}
		
		
		var down = parseFloat(idwn);
		var loanprincipal = parseFloat(iprice);
		var months = parseFloat(itype)*12;
		var interest = parseFloat(irate)/1200;
		var tx = loanprincipal*(parseFloat(itax)/100);
		var val = loanprincipal*(100-down)/100;
		var amt = parseFloat((val*interest/(1-(Math.pow(1/(1+interest),months)))).toFixed(2))+parseFloat((tx/12));
		
		//alert('EMI :$'+Math.round(amt)+'  |  Remaining Amount : $'+Math.round(val)+'  |  Estimated Tax : $'+Math.round(tx/12));
		$('#hidecont').html('EMI : '+Math.round(amt)+' $<br>  Remaining Amount : '+Math.round(val)+' $ <br>  Estimated Tax : '+Math.round(tx/12)+' $');
		$('#hidecont').show(1000);
	},

	validateNum : function(evt){
		evt = (evt) ? evt : window.event;
	    var charCode = (evt.which) ? evt.which : evt.keyCode;
	    if (charCode == 37 || charCode == 38 || charCode == 39 || charCode == 40 || charCode == 46) {
	    	return true;
	    }
	    else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
	        return false;
	    }
	    return true;
	},

}