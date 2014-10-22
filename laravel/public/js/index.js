	{{HTML::style('bootstrap/css/bootstrap.min.css')}}
	{{HTML::style('bootstrap/css/style.css')}}

'use strict';

$(document).ready(function() {
    $('#fileForm').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            split: {
                validators: {
                    file: {
                        extension: 'mp3,m4a',
                        type: 'audio/mpeg,audio/x-m4a',
                        message: 'The selected file is not valid'
                    }
                }
            }
        }
    });
});




/**$(document).ready(function() {
	disabled_texts();
	solonumeros(e);

	$('#submit').click(function(event) {
		validateTypeRadioButton();
		//print_values();
		//hide_elements();
		/* Act on the event */
	//});
//});
	
/**public function disabled_texts() {
		document.getElementById("#parts").disabled = true;
    	document.getElementById("minutes").disabled = true;
	}


		
	public function solonumeros(e){
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
	**/


	



