    public function insertWatermark($file = ''){
      
        $stamp = imagecreatefrompng('cabecalho_logo.png');
        $im = imagecreatefromjpeg($file); 
        $marge_right = 10;
		$marge_bottom = 10;

		list($width, $height) = getimagesize($file);

		$newwidth=1200;
		$newheight=$height*($newwidth/$width);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresized($dst, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		$sx = imagesx($stamp);
		$sy = imagesy($stamp);

		 imagecopy($dst, $stamp, imagesx($dst) - $sx - $marge_right, imagesy($dst) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
		// header('Content-type: image/png');
		imagepng($dst,$file);
		imagedestroy($im);  
		imagedestroy($dst); 

    }