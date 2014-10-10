<?php
    $fileName = "mountain.mp3";
    $startCut = '00:01:00.00';
    $endCut = '00:00:00.00';
 
    $duration = shell_exec('ffmpeg -i ' . $fileName . ' 2>&1 |grep -oP "[0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}"');
 
        list($hr, $mn, $sg, $ms) = split("[:.]", $duration);
        $dur = $hr . $mn . $sg . $ms;

        list($hr, $mn, $sg, $ms) = split("[:.]", $startCut);
        $start = $hr . $mn . $sg . $ms;

        list($hr, $mn, $sg, $ms) = split("[:.]", $endCut);
        $end = $hr . $mn . $sg . $ms;        

    $cont = 0;    
    do {
          
        $cont ++;      
        $partTime = $end; // une el tiempo con separadores : hasta desde donde se empieza a cortar 
            for($i=7; $i<strlen($partTime); $i++){

            $end = $partTime[0].$partTime[1].':'.$partTime[2].$partTime[3].':'.$partTime[4].$partTime[5].'.'.$partTime[6].$partTime[7];
           
            $hr = $partTime[0].$partTime[1];
            $mn = $partTime[2].$partTime[3];
            $sg = $partTime[4].$partTime[5];
            $ms = $partTime[6].$partTime[7];
        }

        $cmd = shell_exec("ffmpeg -i " . $fileName . " -acodec copy -t " . $startCut . " -ss  " . $end . ' part' .  $cont . '.mp3');
                
        $dur = $dur - $start; 
        $dur = str_pad($dur, '8', '0', STR_PAD_LEFT); //poner ceros al inicio        
        $end = $start + $start; 

        $end = $start * $cont;
        $result = str_pad($end, '8', '0', STR_PAD_LEFT); //poner ceros al inicio
        $end = $result;

    } while ($dur > 00000000); 
    
?>