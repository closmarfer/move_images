<?php


namespace App\Service;


use DateTime;

class RenameCanonImage
{
	
	/**
	 * @param string $canon_name
	 * @param string $file_ext
	 * @param DateTime $date
	 * @return bool|string
	 */
	public function handle(string $canon_name, string $file_ext, DateTime $date)
	{
		if (false === strpos($canon_name, 'IMG') || 'jpg' !== $file_ext) {
			return false;
		}
		
		return $date->format("Y-m-d H.i.s") . "." . $file_ext;
	}
}