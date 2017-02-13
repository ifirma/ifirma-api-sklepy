$(document).ready(function(){
	$('#ifirma_api_ryczaltowiec').click(function(e){
		$("#ifirma_ryczalt_rate_div").toggle(this.checked);
		$("#ifirma_ryczalt_wpis_do_ewidencji_div").toggle(this.checked);
	});
	
	$('#ifirma_api_vatowiec').click(function(e){
		$("#ifirma_podstawa_prawna_div").toggle(!this.checked);
	});
});