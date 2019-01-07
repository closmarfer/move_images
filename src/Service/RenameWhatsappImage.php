<?php


namespace App\Service;


use DateTime;

class RenameWhatsappImage
{
	
	/**
	 * @param string $wa_name
	 * @param string $file_ext
	 * @param DateTime $date
	 * @return bool|string
	 */
	public function handle(string $wa_name, string $file_ext, DateTime $date)
	{
		if ((false === strpos($wa_name, 'WA') && false === strpos($wa_name, 'WA')) || ('jpg' !== $file_ext && 'jpeg' !== $file_ext)) {
			return false;
		}
		
		return $date->format("Y-m-d H.i.s") . "." . $file_ext;
	}
}