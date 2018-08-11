<?php 
	function generate_captcha($chars, $length) {
		$captcha = null;
		for ($x = 0; $x < $length; $x++) {
			$rand = rand(0, count($chars) - 1);
			$captcha .= $chars[$rand];
		}
		return $captcha;
	}

	$captcha = generate_captcha(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f'), 5);
	setcookie('captcha', sha1($captcha), time()+60*3);
	echo $captcha;
?>