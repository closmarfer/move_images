<?php


namespace App\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;

class GetValidDropboxImageLocation
{
	private const MONTHS = [
		'01' => 'enero',
		'02' => 'febrero',
		'03' => 'marzo',
		'04' => 'abril',
		'05' => 'mayo',
		'06' => 'junio',
		'07' => 'julio',
		'08' => 'agosto',
		'09' => 'septiembre',
		'10' => 'octubre',
		'11' => 'noviembre',
		'12' => 'diciembre',
	];
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	
	/**
	 * GetValidDropboxImageLocation constructor.
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	public function handle(string $image_name)
	{
		if (!$this->isValidName($image_name)) {
			return null;
		}
		
		$root = $this->container->getParameter('images_root');
		
		$parts = explode('-', $image_name);
		
		$year  = $parts[0];
		$month = $parts[1];
		
		$month_name = self::MONTHS[$month];
		
		return sprintf(
			'%s/%s/%s-%s-%s/%s',
			$root,
			$year,
			$year,
			$month,
			$month_name,
			$image_name
		);
		
	}
	
	private function isValidName(string $name)
	{
		return preg_match('/\d\d-\d\d-\d\d\s\d\d\.\d\d\.\d\d(-\d)?\.(jpg|png|mp4)/', $name) === 1;
	}
}