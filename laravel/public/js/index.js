
'use strict';

$(document).ready(function() {
	disabled_texts();
	solonumeros(e);

	$('#submit').click(function(event) {
		validateTypeRadioButton();
		//print_values();
		//hide_elements();
		/* Act on the event */
	});
});
	

	function disabled_texts() {
		document.getElementById("#parts").disabled = true;
    	document.getElementById("minutes").disabled = true;
	}
	
	function solonumeros(e){
		var key=e.keyCode || e.which;
		var teclado=String.fromCharCode(key).toLowerCase();
		var numeros="0123456789";
		var especiales="8-37-38-46";
		var teclado_especial=false;


		for (var i in especiales) {
			if(key==especiales[i]){
				teclado_especial=true;
			}
		}
		if(numeros.indexOf(teclado)==-1 && !teclado_especial)){
		return false;
	}



	public function validate(){

	$data = Input::all();
	$rules = array(
		'file' => 'required',
		'parts'=> 'Integer',
		'time_per_chunk'=> 'Integer'
		);

	$validate = Validator::make($data, $rules);

	if ($validate->fails()) {
        return Redirect::to('uploads')
            ->withErrors($validate);
	}
	return dd($data);


		/**if($radio_btn == 'parts'){
			$validator = Validator::make(
    		array('radio_btn' => 'required|min:1')
    		);
		}
		else if($radio_btn == 'minutes'){
			$validator = Validator::make(
    		array('radio_btn' => 'required|min:1')
    		);
		}*/
	}


