 <?php
//  ****************  Rabbit-MQ Conection  ***************
    require_once __DIR__ . '/vendor/autoload.php';
    use PhpAmqpLib\Connection\AMQPConnection;

    $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');//Se establecen los pparametros para conectar con el servidor Rabbit-Mq
    $channel = $connection->channel();//se establece la conexion con el canal

    $channel->queue_declare('split_file', false, false, false, false);//se le indica a que canal debe conectarse

    echo ' ** Waiting for messages. To exit press CTRL+C', "\n";

    $callback = function($msg) {//Realiza constantemente los mensajes que ingresan al canal del servidor rabbit, en caso de encontrar un mnsaje en cola lo procesa

//           ******************* Read Json *********************
        $json = json_decode($msg->body,true);// Recibe el mensaje en formato Json y lo decodifica para asignarselo a sus variables respectivas
        $id=$json["id"];    
        $file=$json["file"];  
        $parts=$json["parts"];
        $time_per_chunk=$json["time_per_chunk"];    

        list($none, $folder_name, $audio_file) = split("[/]", $file); //Obtiene el nombre del archivo de audio
    
    //  ******************* Convertir minutos a formato reconocido por ffmpeg ********************* 
        
        list($time, $minutes) = split("[ ]", $time_per_chunk);//Obtiene los minutos digitados por el usuario
        $min = $time;//Obtiene los minutos digitados por el usuario
        $min_duration = '00:00:00.00'; //inicializa los minutos en 0
        if($min > 0){// Valida que los minutos sea mayor que 0 pararealizar los procesos paa dividir el archivo por minutos
            if($min > 59){
                $hrs = $min / 60;

                if(is_float ($hrs)){//Valida si el resultado contiene decimales
                    $get_minutes = explode( ".", $hrs );
                    $hrs = $get_minutes[0];
                    $mn = '0.'.$get_minutes[1];    
                    $min_decimals = $mn * 60;//hace la convercion a minutos

                    $min = round($min_decimals, 0, PHP_ROUND_HALF_UP);// Redondea y elimina decimales de los minutos

                }else{
                    $min = '00';
                    $seg = '00';
                    $mcs = '00';

                    if($hrs > 0 && $hrs < 10){
                        $min_duration = '0' . $hrs . ':' . $min . ':' . $seg . '.' . $mcs;
                    }

                    $min_duration = $hrs . ':' . $min . ':' . $seg . '.' . $mcs;
                } 

                $seg = '00';
                $mcs = '00';

                if($mn > 59){
                    $mn = $mn - 60;
                    if($mn > 0 && $mn < 10){
                                $mn = '0' . $mn;
                            }
                    $hrs = $hrs + 1; 
                }

                if($min < 10 && $min > 0){
                    $min_duration = $hrs . ':' . '0' . $min . ':' . $seg . '.' . $mcs;
                }else if($hrs > 0 && $hrs < 10){
                    $min_duration = '0' . $hrs . ':' . $min . ':' . $seg . '.' . $mcs;
                }else{
                    $min_duration = $hrs . ':' . $min . ':' . $seg . '.' . $mcs;
                }

            }else if($min < 60){//Si los minutos son menores a 60 realiza los calculos necesario para procesar el archivo

                $min_duration = $min * 10000;//Calcula la cantidad de minutos y los hubica en la posicion correspondiente a minutos
                $min_duration = str_pad($min_duration, '8', '0', STR_PAD_LEFT);//Rellena los espacios a la derecha con ceros
                $get_time = $min_duration;

                for($i=7; $i<strlen($get_time); $i++){ // Convierte los minutos en el formato requerido por el ffmpeg y valida los min y sg

                    $hr = $get_time[0] . $get_time[1];
                    $mn = $get_time[2] . $get_time[3];
                    $sg = $get_time[4] . $get_time[5];
                    $ms = $get_time[6] . $get_time[7];

                    if($mn > 59){//si los minutos son mayor que 60 se aumenta en 1 las horas
                        $mn = $mn - 60;
                        if($mn > 0 && $mn < 10){
                                    $mn = '0' . $mn;
                                }
                        $hrs = $hrs + 1;
                        if($hr > 0 && $hr < 10){
                                    $hr = '0' . $hr;
                                } 
                    }
 
                    $min_duration = $hr . ':' . $mn . ':' . $sg . '.' . $ms;             
                }
            }
        } 

        list($file_name, $ext) = split("[.]", $audio_file);//Extrae el nombre y la extension del archivo de audio
        $start_cut = '00:00:00.00';//Establece desde donde se va a iniciar a dividir los archivos
        $path_audio_file = "../laravel/uploads/" . $folder_name . '/';//Define la ubicacion en donde se van a almacenar los archivos divididos                                                 
        $audio_duration = shell_exec('ffmpeg -i ' . $path_audio_file . $audio_file . ' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');//Obtiene la duracion del archivo de audio

        list($hr, $mn, $sg, $ms) = split("[:.]", $audio_duration);//Elimina los puntos que separan las variables numericas
        $audio_dur = $hr . $mn . $sg . $ms;

        list($hr, $mn, $sg, $ms) = split("[:.]", $min_duration);//Elimina los puntos que separan las variables numericas
        $min_dur = $hr . $mn . $sg . $ms;

        list($hr, $mn, $sg, $ms) = split("[:.]", $start_cut);//Elimina los puntos que separan las variables numericas
        $startCut = $hr . $mn . $sg . $ms;        

            $db = true;
            if($parts > 0){//Si las partes son mayor que 0 realiza los calculos necesarios para realizar la division de los archivos

                $parts = $audio_dur / $parts;//Divide las partes entre la duracion del archivo
                $int=round($parts * 1) / 1; //Redondeo decimales
                $int = number_format($parts, 0, '', '');  //Elimina los decimales
                $parts = str_pad($int, '8', '0', STR_PAD_LEFT); //Agrega ceros al inicio
                $parts_to_duration = $parts; //Une el tiempo con separadores : hasta desde donde se empieza a cortar 

                for($i=7; $i<strlen($parts_to_duration); $i++){
                    $hr = $parts_to_duration[0].$parts_to_duration[1];
                    $mn = $parts_to_duration[2].$parts_to_duration[3];
                    $sg = $parts_to_duration[4].$parts_to_duration[5];
                    $ms = $parts_to_duration[6].$parts_to_duration[7];

                    if($sg > 59){
                        $sg = $sg - 60;
                        $sg = str_pad($sg, '2', '0', STR_PAD_LEFT);
                        $mn = $mn + 1; 
                        $mn = str_pad($mn, '2', '0', STR_PAD_LEFT);
                    }
                    if($mn > 59){
                        $mn = $mn - 60;
                        $mn = str_pad($mn, '2', '0', STR_PAD_LEFT); 
                        $hr = $hr + 1; 
                    }

                    $min_duration = $hr . ':' . $mn . ':' . $sg . '.' . $ms;                    
                }   
                
                $startCut = '00:00:00.00';//Establece el tiempo en donde se va a iniciar el primer corte
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

                            if($sg > 59){//Valida los segundos si exceden los 60 segundos aumenta en 1 los minutos
                                $sg = $sg - 60;
                                $sg = str_pad($sg, '2', '0', STR_PAD_LEFT);
                                $mn = $mn + 1; 
                                $mn = str_pad($mn, '2', '0', STR_PAD_LEFT); 
                            }

                            if($mn > 59){//Valida los minutos si exceden los 60 minutos aumenta en 1 las horas
                                $mn = $mn - 60;
                                $mn = str_pad($mn, '2', '0', STR_PAD_LEFT); 

                                $hr = $hr + 1; 
                            }
                        }   
                        $startCut = $hr . ':' . $mn . ':' . $sg . '.' . $ms;   

                        //echo ' **** **** **** Inicio de corte: ' . $startCut . "\n";
                    }
                    $audio_dur = $audio_dur - $min_dur; //Le resta al audio total la duracion de cada parte, para luego validar el audio que queda sin dividir
                    $audio_dur = str_pad($audio_dur, '8', '0', STR_PAD_LEFT); //Pone ceros al inicio de la variable, para conservar el tamaño total de 8 digitos

                    if($cont < 10 && $cont > 0){//Asigna un cero a el número de parte
                        $num_part = '0' . $cont;
                    }else{
                        $num_part = $cont;
                    }
                    $name = $path_audio_file . $file_name . '_part_' . $num_part . "." . $ext;//Arma el nombre del archivo de salida

                    $cmd = shell_exec("ffmpeg -i " . $path_audio_file . $audio_file . " -acodec copy -t " 
                    . $min_duration . " -ss  " . $startCut . ' ' . $name);//Comando que divide el archivo de audio                   
                    $insert .= "INSERT INTO download_links(file_path_split, audio_file_id)VALUES (" . "'" . $name . "'" . ', ' . $id . " ); ";
                    $startCut = $min_dur * $cont;//Se multiplica la duracion por el contador, para indicar desde donde se incia el siguiente corte     
                    $startCut = str_pad($startCut, '8', '0', STR_PAD_LEFT); //Pone ceros al inicio de la variable, para conservar el tamaño total de 8 digitos
                  
                } while ($audio_dur > 99); // El ciclo se mantiene mientras que la duración del audio se mayoa a 00:00:10.99
            }

        if($db = true){ //Se establece la conexión a la base de datos
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
        }        
    };

    $channel->basic_consume('split_file', '', false, true, false, false, $callback);//Indica el canal del cual se van a recibir los mensajes
    while(count($channel->callbacks)) {//El ciclo finaliza hasta que el usuario precione la tecla Ctrl + C
        $channel->wait();
    }

    $channel->close();//Se cierra la conexion con el canal
    $connection->close();//Se cierra la conexion con el rabbit-mq
?>