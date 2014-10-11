
<h3>Music Box</h3>
	
	{{ Form::file('file','',array('id'=>'','class'=>'','accept'=>'.csv')) }}
	<br>

	{{ Form::label('parts', 'Parts') }}
	{{ Form::text('parts', '') }}
	<br>
	{{ Form::label('time', 'Time') }}
	{{ Form::text('TIME', '') }}
	<br>
	{{Form::submit('Split', array())}}

{{ Form::close() }}


