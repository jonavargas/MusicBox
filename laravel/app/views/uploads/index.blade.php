<!DOCTYPE html>
<head>	
	{{HTML::script('js/index.js')}}
	{{HTML::script('js/jquery-2.1.1.min.js')}}
	{{HTML::style('bootstrap/css/bootstrap.min.css')}}
	{{HTML::style('bootstrap/css/style.css')}}
	{{HTML::script('js/jquery.validate.min.js')}}
	{{HTML::script('js/additional-methods.min.js')}}


	<div>
		<title >Music Box</title>   		
	</div>
</head>
<body>	
	<div class="title">
		<h1>Music Box</h1>
    </div>
    <div class="form">


    	{{ Form::open(array('url' => 'uploads' , 'files'=>true)) }}
    	<div class="lblFile">
    		{{ Form::label('file','Select your audio file:',array('id'=>'','class'=>'')) }}
    	
    	</div>
    	<div class ="file">
 			{{ Form::file('file','',array('id'=>'file','class'=>'file')) }}		
		</div>
		<div class= "parts">			
			
			{{ Form::label('lblparts','Parts',array('id'=>'lblparts','class'=>'lblparts')) }}
			<input  class="form-control textparts" type="text" name="parts" onkeypress="return solonumeros(event)">				
		</div>
		<div class="minutes">
  			{{ Form::label('lblminutes', 'Minutes',array('id'=>'lblminutes','class'=>'lblminutes')) }}
  			<input  class="form-control textminutes" type="text" name="minutes" onkeypress="return solonumeros(e)">						
		</div>
		<div>
			<input  class= "form-control submit btn btn-danger" type="submit"  name="splipt" id="submit" value="Slipt">
			
			
		</div>		
			{{ Form::close() }}    	
    </div>   
  
</body>

	
</body>
</html>




