<?php
    function subir_archivo($archivo, $ruta, $nombre) {
        $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf");
        
        $temp = explode(".", $archivo["name"]);
        
        $extension = end($temp);
        
        if (in_array(strtolower($extension), $allowedExts))
        {
        	//&& ($archivo["size"] < 300000)
        	 
            if ($archivo["error"] > 0)
            {
                echo "Return Code: " . $archivo["error"] . "<br>";
            }
            else
            {
//                echo "Upload: " . $archivo["name"] . "<br/>";
//                echo "Type: " . $archivo["type"] . "<br/>";
//                echo "Size: " . ($archivo["size"] / 1024) . " kB<br/>";
//                echo "Temp file: " . $archivo["tmp_name"] . "<br/>";
                
                if (file_exists($ruta . "/" . $nombre))
                    unlink($ruta . "/" . $nombre);

                move_uploaded_file($archivo["tmp_name"], $ruta . "/" . $nombre);
                
//                echo "Stored in: " . $ruta . "/" . $nombre."<br/>";
            }
        }
        else
        {
            echo "Invalid file<br>";
        }
        
    }
    
    function fixFilesArray(&$files)
    {
    	$names = array( 'name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);
    
    	foreach ($files as $key => $part) {
    		// only deal with valid keys and multiple files
    		$key = (string) $key;
    		if (isset($names[$key]) && is_array($part)) {
    			foreach ($part as $position => $value) {
    				$files[$position][$key] = $value;
    			}
    			// remove old key reference
    			unset($files[$key]);
    		}
    	}
    }
?>