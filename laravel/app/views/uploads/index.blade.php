{{ Form::open(array('url' => 'uploads' , 'files'=>true)) }}

	{{ Form::label('file','File',array('id'=>'','class'=>'')) }}
  	{{ Form::file('file','',array('id'=>'file','class'=>'file')) }}
  	<br>

	{{ Form::label('parts','Parts',array('id'=>'lblparts','class'=>'lblparts')) }}
	{{ Form::text('parts', '') }}
	
	<br>	   
  
  	{{ Form::label('minutes', 'Minutes') }}
	{{ Form::text('minutes', '') }}
	
	<br>

	<!-- 	Codigo de Validar los campos, aunn no esta implementado -->
	@if ($errors->has())
                    <div class="alert-danger text-center" role="alert">
                        <small>{{ $errors->first('file') }}</small>
                        <small>{{ $errors->first('parts') }}</small>
                        <small>{{ $errors->first('minutes') }}</small>
                    </div>
	@endif


	{{Form::submit('Split', array())}}

{{ Form::close() }}

