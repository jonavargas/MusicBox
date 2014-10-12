 <?php
    $audio_file = "file.mp3";
    $min_duration = '00:01:00.00'; ////////////// Tamaño de partes de audio !!!!!!!!!!!!!!!
    $start_cut = '00:00:00.00';
    $parts = '0';
 
    $audio_duration = shell_exec('ffmpeg -i ' . $audio_file . ' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');

    list($hr, $mn, $sg, $ms) = split("[:.]", $audio_duration);
    $audio_dur = $hr . $mn . $sg . $ms;

    list($hr, $mn, $sg, $ms) = split("[:.]", $min_duration);
    $min_dur = $hr . $mn . $sg . $ms;

    list($hr, $mn, $sg, $ms) = split("[:.]", $start_cut);
    $startCut = $hr . $mn . $sg . $ms;        
        
    if($parts > 0){

        $parts = $audio_dur / $parts;
        $int=round($parts * 1) / 1; //Redondeo decimales
        $int = number_format($parts, 0, '', '');  //Elimina los decimales
        $parts = str_pad($int, '8', '0', STR_PAD_LEFT); //poner ceros al inicio
        $parts_to_duration = $parts; // une el tiempo con separadores : hasta desde donde se empieza a cortar 
        
        for($i=7; $i<strlen($parts_to_duration); $i++){
            $hr = $parts_to_duration[0].$parts_to_duration[1];
            echo ' HORAS ' . $hr . "\n";//debug    
            $mn = $parts_to_duration[2].$parts_to_duration[3]; //Permite 59
            echo ' MINUTOS ' . $mn . "\n";//debug
            $sg = $parts_to_duration[4].$parts_to_duration[5];//Permite 59
            echo ' SEGUNDOS ' . $sg . "\n";//debug
            $ms = $parts_to_duration[6].$parts_to_duration[7];// Permite 99 ms
            echo ' MICROSEGUNDOS ' . $ms . "\n";//debug

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
            echo ' ** D ***** D ****** D  **** D ***** DURACION AUDIO ' . $audio_dur . "\n";//debug

            echo ' ** MIN ***** MIN ****** MIN  **** MIN ***** MIN DURACION  ' . $min_duration . "\n";//debug
            $audio_dur = str_pad($audio_dur, '8', '0', STR_PAD_LEFT); //poner ceros al inicio 
            $cmd = shell_exec("ffmpeg -i " . $audio_file . " -acodec copy -t " . $min_duration . " -ss  " . $startCut . ' part' .  $cont . '.mp3');// Linea que divide el codigo
            $startCut = $min_dur * $cont;        
            $startCut = str_pad($startCut, '8', '0', STR_PAD_LEFT); //poner ceros al inicio
            
        } while ($audio_dur > 99); 
    }
?>
