<?php 
	session_start();
	header("Content-type: image/png");
	$imagen=imagecreate(50,20);
	$color_fondo=imagecolorallocate($imagen,187,187,187);
	$color_texto=imagecolorallocate($imagen,255,255,255);

	function generate_captcha($chars, $length) {
		$captcha = null;
		for ($x = 0; $x < $length; $x++) {
			$rand = rand(0, count($chars) - 1);
			$captcha .= $chars[$rand];
		}
		return $captcha;
	}

	$captcha = generate_captcha(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f'), 5);
	//setcookie('captcha', sha1($captcha), time()+60*3);
	$_SESSION['captcha'] = sha1($captcha);
	imagettftext($imagen,11,0,3,15,$color_texto,"arial.ttf",$captcha);
	imagepng($imagen);
	imagedestroy($imagen);
?>