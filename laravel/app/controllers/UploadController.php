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
			
		
	}


	public function validate(){

		$data = Input::all();

		$rules = array(
			'file' => 'required',
			'PARTS'=> 'Integer',
			'MINUTES'=> 'Integer'

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
		//
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
