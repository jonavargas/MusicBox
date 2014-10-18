 <?php

//  ****************  Rabbit-MQ Conection  ***************

    require_once __DIR__ . '/vendor/autoload.php';
    use PhpAmqpLib\Connection\AMQPConnection;

    $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
    $channel = $connection->channel();


    $channel->queue_declare('split_file', false, false, false, false);

    echo ' ** Waiting for messages. To exit press CTRL+C', "\n";

    $callback = function($msg) {

//           ******************* Read Json *********************
        $json = json_decode($msg->body,true);
        $id=$json["id"];    
        $file=$json["file"];  
        $parts=$json["parts"];
        $time_per_chunk=$json["time_per_chunk"];    
    
        list($none, $none, $none,  $none, $audio_file) = split("[/]", $file); // Obtiene el nombre del archivo de audio

    //  ******************* Convertir minutos a formato reconocido por ffmpeg ********************* 

        list($time, $minutes) = split("[ ]", $time_per_chunk);

        if($time > 0){
            $min_duration = $time * 10000;
            $min_duration = str_pad($min_duration, '8', '0', STR_PAD_LEFT);
            $get_time = $min_duration;

            if($min_duration > 595999){//*********************************revisar
                $min_duration = $min_duration - 595999;
                $min_duration = str_pad($min_duration, '8', '0', STR_PAD_LEFT);
            }

            for($i=7; $i<strlen($get_time); $i++){ // Convierte los minutos en el formato requerido por el ffmpeg y valida los min y sg

                $hr = $get_time[0] . $get_time[1];
                $mn = $get_time[2] . $get_time[3];
                $sg = $get_time[4] . $get_time[5];
                $ms = $get_time[6] . $get_time[7];

                if($sg > 59 && $mn < 60){  //////////// revisar si es necesario!!!!!!!!! *************
                    $sg = $sg - 59;
                    $mn = $mn + 1; 
                }
                if($mn > 59){
                    $mn = $mn - 59;
                    $hr = $hr + 1; 
                }
                $min_duration = $hr . ':' . $mn . ':' . $sg . '.' . $ms;             
            }
        } 

        list($file_name, $ext) = split("[.]", $audio_file);
        $start_cut = '00:00:00.00';
        $path_audio_file = "../laravel/public/uploads/";
        $audio_duration = shell_exec('ffmpeg -i ' . $path_audio_file . $audio_file . ' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');

        list($hr, $mn, $sg, $ms) = split("[:.]", $audio_duration);
        $audio_dur = $hr . $mn . $sg . $ms;

        list($hr, $mn, $sg, $ms) = split("[:.]", $min_duration);
        $min_dur = $hr . $mn . $sg . $ms;

        list($hr, $mn, $sg, $ms) = split("[:.]", $start_cut);
        $startCut = $hr . $mn . $sg . $ms;        
        
        if($audio_dur > $min_dur){


            if($parts > 0){

                $parts = $audio_dur / $parts;
                $int=round($parts * 1) / 1; //Redondeo decimales
                $int = number_format($parts, 0, '', '');  //Elimina los decimales
                $parts = str_pad($int, '8', '0', STR_PAD_LEFT); //poner ceros al inicio
                $parts_to_duration = $parts; // une el tiempo con separadores : hasta desde donde se empieza a cortar 
                
                for($i=7; $i<strlen($parts_to_duration); $i++){
                    $hr = $parts_to_duration[0].$parts_to_duration[1];
                    $mn = $parts_to_duration[2].$parts_to_duration[3];
                    $sg = $parts_to_duration[4].$parts_to_duration[5];
                    $ms = $parts_to_duration[6].$parts_to_duration[7];

                    if($sg > 59 && $mn < 60){
                        $sg = $sg - 59;
                        $mn = $mn + 1; 
                    }
                    if($mn > 59){
                        $mn = $mn - 59;
                        $hr = $hr + 1; 
                    }
                    $min_duration = $hr . ':' . $mn . ':' . $sg . '.' . $ms;                    
                    $temp = $min_duration;
                }   
                
                $min_duration = $temp;
                $startCut = '00:00:00.00';
                list($hr, $mn, $sg, $ms) = split("[:.]", $min_duration);
                $min_dur = $hr . $mn . $sg . $ms;
            }
            
            if($min_duration != '00:00:00.00' || $parts != '0'){
                $cont = 0;    
                do {
                      
                    $cont ++;     
                    
                    if($cont > 1){
                        for($i=7; $i<strlen($startCut); $i++){
                            $hr = $startCut[0].$startCut[1];
                            $mn = $startCut[2].$startCut[3]; 
                            $sg = $startCut[4].$startCut[5];
                            $ms = $startCut[6].$startCut[7];

                            if($sg > 59 && $mn < 60){
                                $sg = $sg - 59;
                                $mn = $mn + 1; 
                            }
                            if($mn > 59){
                                $mn = $mn - 59;
                                $hr = $hr + 1; 
                            }
                        }   

                        $startCut = $hr . ':' . $mn . ':' . $sg . '.' . $ms;   
                    }

                    if($parts < 0){

                        $partTime = $startCut; // une el tiempo con separadores : desde donde se empieza a cortar 
                            
                            for($i=7; $i<strlen($partTime); $i++){
                                $startCut = $partTime[0].$partTime[1].':'.$partTime[2].$partTime[3].':'.$partTime[4].$partTime[5].'.'.$partTime[6].$partTime[7];            
                            }            
                    }

                    $audio_dur = $audio_dur - $min_dur; 
                    $audio_dur = str_pad($audio_dur, '8', '0', STR_PAD_LEFT); //poner ceros al inicio
                    $audio_dur = str_pad($audio_dur, '8', '0', STR_PAD_LEFT); //poner ceros al inicio 

                    $cmd = shell_exec("ffmpeg -i " . $path_audio_file . $audio_file . " -acodec copy -t " 
                    . $min_duration . " -ss  " . $startCut . ' ' . $path_audio_file . $file_name . '_part' 
                    .  $cont . '.' .  $ext);// Linea que divide el archivo de audio
                    
                    $name = $file_name . '_part' . $cont . "." . $ext;

                    $insert .= "INSERT INTO worker(file_path_split, audio_file_id)VALUES (" . "'" . $name . "'" . ', ' . $id . " ); ";
                    $startCut = $min_dur * $cont;        
                    $startCut = str_pad($startCut, '8', '0', STR_PAD_LEFT); //poner ceros al inicio
                    
                } while ($audio_dur > 1099); 
            }
        }else{
            echo " ** Error!!! - You must type a quantity of minutes lesser than the duration of the file." . "\n";
        }

        //if($audio_dur > $min_dur){

            $host = 'localhost';
            $port = '5432';
            $dbname = 'music_box';
            $user = 'postgres';
            $password = '12345';

            $connection_Pg = pg_connect( 'host=' . $host . ' port=' . $port . ' dbname=' . $dbname . ' user=' . $user . ' password=' . $password) or die("Error: Connection to database not found.!!!");    
            $state_connection = pg_connection_status($connection_Pg);
            
            if ($state_connection === PGSQL_CONNECTION_OK) {
                echo ' ** Connection to database established successfully.' . "\n";
            }

            $query = pg_query($connection_Pg, $insert) or die("Error in query.!!!");
            pg_close($connection_Pg);  
            echo ' ** Connexion successfully completed.' . "\n";
       // }

    };

    $channel->basic_consume('split_file', '', false, true, false, false, $callback);   
    while(count($channel->callbacks)) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
?>
