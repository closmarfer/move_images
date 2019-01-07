<?php


use App\Service\RenameCanonImage;
use App\Service\RenameWhatsappImage;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class RenameWhatsappImageTest extends TestCase
{
	/**
	 * @param array $params
	 * @param array $expected
	 * @dataProvider dataTestProvider
	 */
	public function testHandle(array $params, array $expected)
	{

		$service = new RenameWhatsappImage();
		
		$result = $service->handle(
			$params['img'],
			$params['ext'],
			$params['date']
		);
		
		$this->assertSame($expected['result'], $result);
	}
	
	public function dataTestProvider()
	{
		return [
			"Valid Whatsapp Name" => [
				"params"   => [
					"img"  => "IMG-20160624-WA0002.jpg",
					"ext"  => "jpg",
					"date" => new DateTime('2018-07-12 15:23:56'),
				],
				"expected" => [
					"result" => '2018-07-12 15.23.56.jpg'
				],
			],
			"Valid Whatsapp Name 2" => [
				"params"   => [
					"img"  => "IMG-20160624-WA0003.jpeg",
					"ext"  => "jpg",
					"date" => new DateTime('2018-07-12 15:23:56'),
				],
				"expected" => [
					"result" => '2018-07-12 15.23.56.jpg'
				],
			],
			"Invalid Canon Name" => [
				"params"   => [
					"img"  => "im23_345.jpg",
					"ext"  => "jpg",
					"date" => new DateTime('2018-07-12 15:23:56'),
				],
				"expected" => [
					"result" => false
				],
			],
			"Invalid Canon Name 2" => [
				"params"   => [
					"img"  => "IMG23_345.mp4",
					"ext"  => "mp4",
					"date" => new DateTime('2018-07-12 15:23:56'),
				],
				"expected" => [
					"result" => false
				],
			],
		];
	}
}
