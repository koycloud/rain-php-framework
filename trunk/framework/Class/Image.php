<?php
class Image
{
	private static function init()
	{
		if (!extension_loaded('gd'))
			die('please open gd extension first');
	}

	public static function getCode($width = 70, $height = 24, $len = 4)
	{
		self::init();
		header('content-type:image/png');
		$checkWord = '';
		$checkChar = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ1234567890';
		for ($num = 0; $num < $len; $num++)
		{
		   $char = rand(0, strlen($checkChar) - 1);
		   $checkWord .= $checkChar[$char];
		}
		$_SESSION['code'] = strtolower($checkWord);
		$image = imagecreate($width, $height);
		$font = FONTS_PATH.'ariblk.ttf';
		$red = imagecolorallocate($image, 0xf3, 0x61, 0x61);
		$blue = imagecolorallocate($image, 0x53, 0x68, 0xbd);
		$green = imagecolorallocate($image, 0x6b, 0xc1, 0x46);
		$colors = array($red, $blue, $green);
		$gray = imagecolorallocate($image, 0xf5, 0xf5, 0xf5);
		imagefill($image,0,0,$gray);
		imageline($image,rand(0,5),rand(6,18),rand(65,70),rand(6,18),$colors[rand(0,2)]);
		for($num = 0; $num < $len; $num++)
		   imagettftext($image, rand(12,16), (rand(0,60)+330)%360, 5+15*$num+rand(0,4), 18+rand(0,4), $colors[rand(0,2)], $font, $checkWord[$num]);
		imagepng($image);
		imagedestroy($image);
	}
}
