<?php


namespace App\Service;


use DateTime;

class RenameMobileVideo
{
	
	/**
	 * @param string $mobile_name
	 * @param string $file_ext
	 * @return bool|string
	 */
	public function handle(string $mobile_name, string $file_ext)
	{
		$path_parts = pathinfo($mobile_name);
		
		try {
			$date = DateTime::createFromFormat('Ymd_His', $path_parts['filename']);
		} catch (\Exception $e) {
			return false;
		}
		
		if ('mp4' !== $path_parts['extension'] || false === $date) {
			return false;
		}
		
		return $date->format("Y-m-d H.i.s") . "." . $file_ext;
	}
}