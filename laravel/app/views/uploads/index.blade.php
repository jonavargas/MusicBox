<!DOCTYPE html>
<head>	
	{{HTML::script('js/jquery-2.1.1.min.js')}}
	{{HTML::style('bootstrap/css/bootstrap.min.css')}}
	{{HTML::style('bootstrap/css/style.css')}}

	<div>
		<title >Music Box</title>   		
	</div>
</head>
<body>	
	<div class="title">
		<h1>Music Box</h1>
    </div>
    <div class="form">
    	{{ Form::open(array('url' => 'uploads' , 'files'=>true )) }}
    	<div class="lblFile">
    		{{ Form::label('file','Select your audio file:',array('id'=>'','class'=>'')) }}
    	
    	</div>
    	<div class ="file">
 			{{ Form::file('file','',array('id'=>'file','class'=>'file')) }}		
		</div>
		<div class= "parts">
			{{Form::radio('radioBtn', 'Parts', array('id'=>'parts'))}}	
			{{ Form::label('lblparts','Parts',array('id'=>'lblparts','class'=>'lblparts')) }}
			<input type="text" name="parts" onkeypress="return solonumeros(e)">				
		</div>
		<div>
					
		</div>
		<div>
			
			
		</div>
		<div>
			
		</div>
  	
  	

  	
	
	
	
	
	<br>	   
  
  	{{Form::radio('radioBtn', 'Minutes')}}
  	{{ Form::label('lblminutes', 'Minutes') }}
	{{ Form::text('minutes', '') }}	
	<br>
	
	<input type="submit" id="submit" value="Slipt">
{{ Form::close() }}

    	
    </div>
    

	
</body>
</html>




{{HTML::script('js/index.js')}}

