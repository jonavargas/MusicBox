<?php

class UploadController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	protected $layout = 'layouts.default';
	public function index()	{

		$this->layout->content = View::make('uploads.index');
		$uploads = Upload::all();
		$this->layout->titulo = 'Music Box';
		$this->layout->nest(
			'content',
			'uploads.index',
			array(
				'uploads' => $uploads
			)
		);

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

	
	public function upload_file(){

		$file = Input::file('file');
		$destinationPath = __DIR__.'/../../uploads/';		
		$filename = $file->getClientOriginalName();
		$extension =$file->getClientOriginalExtension(); 
		$new_name = $this->replace_white_spaces($filename);
		$uploadSuccess = Input::file('file')->move($destinationPath, $new_name);
		$file_upload_type = $file->getClientMimeType();//Obtiene el formato del archivo seleccionado
		$ext_supported = array('audio/m4a', 'audio/mp2', 'audio/mp3', 'audio/wav');//Valida los formatos soportados

		if( $uploadSuccess) {// validar extension con las declaradas en el array
   			
   			return $destinationPath . $new_name;  

		} else if(!in_array($file_upload_type, $ext_supported)){
   			echo "<p class='error_message'>File format not supported, try another file</p>";
	        return View::make('uploads.index');
		}

	}
	

	public function replace_white_spaces($file){
		
		$file = strtolower($file);//COnvierte el nombre del archivo a minuscula
		$file = preg_replace("/[^.a-z0-9_\s-]/", "", $file);//Indica los caracteres posiblesque mantiene el nombre
		$file = preg_replace("/[\s-]+/", " ", $file);//Elimina espacios en blanco multiples y barras inclinadas
		$file = preg_replace("/[\s_]/", "-", $file);//Combierte los espacios en blanco en guiones
		return $file;
	}
	
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$file = $this->upload_file();
		$parts = Input::get('parts');
		$minutes = Input::get('minutes');
		$upload = new Upload();
		$upload->file = $file;
		$upload->parts = $parts;
		$upload->time_per_chunk = $minutes . ' minutes';
		$upload->save();
		return Redirect::to('uploads');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
