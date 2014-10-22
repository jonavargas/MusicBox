<?php

require_once __DIR__ . '/../../rabbit_connection/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;  
use PhpAmqpLib\Message\AMQPMessage;

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
	
	public function upload_file(){		

		$file = Input::file('file');				
		
		if( $file== null){
			return Redirect::to('/');
		}

		$extension=$file->getClientOriginalExtension();
		$ext = $extension;

		if(($ext !="wma")&&($ext !="mp3")&& ($ext !="mp2"))
		{
			return Redirect::to('/');
			
		}
		$filename = $file->getClientOriginalName();
		try
			{
		    	$query = DB::Select("SELECT id FROM audio_file ORDER BY id DESC LIMIT 1");
				$id = $query[0]->id;
				$id = $id + 1;
			}
			catch(Exception $ex)
			{
		   		$id = 1;
			}

			$get_name = explode( ".", $filename );
			$name = $get_name[0];
			$name_folder = $this->replace_white_spaces($name);		
			$name_folder = $name_folder . $id;

			$destinationPath = 'uploads/' . $name_folder . '/';
			$extension =$file->getClientOriginalExtension(); 
			$new_name = $this->replace_white_spaces($filename);

			$uploadSuccess = Input::file('file')->move($destinationPath, $new_name);
		
			if( $uploadSuccess) {// validar extension con las declaradas en el array
   			
   				return $destinationPath . $new_name;  

			} else if(!in_array($file_upload_type, $ext_supported)){
   				echo "<p class='error_message'>File format not supported, try another file</p>";
	        	return View::make('uploads.index');
			}
	}		

	public function replace_white_spaces($file){
		
		$file = strtolower($file);//Convierte el nombre del archivo a minuscula
		$file = preg_replace("/[^.a-z0-9_\s-]/", "", $file);//Indica los caracteres posiblesque mantiene el nombre
		$file = preg_replace("/[\s-]+/", " ", $file);//Elimina espacios en blanco multiples y barras inclinadas
		$file = preg_replace("/[\s_]/", "", $file);//Combierte los espacios en blanco en guiones
		return $file;
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

		$file_path = $file;
		$id = $upload->id;
		$send_message = $this->send_Json($id, $file_path, $parts, $minutes);
		return Redirect::to('uploads');
	}

	public function send_Json($id, $file_path, $parts, $minutes)
	{
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();
		$channel->queue_declare('split_file', false, false, false, false);

		$msg = array("id" => "$id","file" => "$file_path","parts" => "$parts","time_per_chunk" => "$minutes . ' minutes'");
		$push = json_encode($msg);
		$push = new AMQPMessage($push);

		$channel->basic_publish($push, '', 'split_file');
		$channel->close();
		$connection->close();
	}


}
