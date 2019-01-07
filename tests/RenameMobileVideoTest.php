<?php


use App\Service\RenameCanonImage;
use App\Service\RenameMobileVideo;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class RenameMobileVideoTest extends TestCase
{
	/**
	 * @param array $params
	 * @param array $expected
	 * @dataProvider dataTestProvider
	 */
	public function testHandle(array $params, array $expected)
	{
		$service = new RenameMobileVideo();
		
		$result = $service->handle(
			$params['img'],
			$params['ext']
		);
		
		$this->assertSame($expected['result'], $result);
	}
	
	public function dataTestProvider()
	{
		return [
			"Valid Video Name" => [
				"params"   => [
					"img"  => "20161224_210752.mp4",
					"ext"  => "mp4",
				],
				"expected" => [
					"result" => '2016-12-24 21.07.52.mp4'
				],
			],
			"Valid Video Name 2" => [
				"params"   => [
					"img"  => "20180420_134706.mp4",
					"ext"  => "mp4",
				],
				"expected" => [
					"result" => '2018-04-20 13.47.06.mp4'
				],
			],
			"Invalid Video Name" => [
				"params"   => [
					"img"  => "20180420_134706.img",
					"ext"  => "img",
				],
				"expected" => [
					"result" => false
				],
			],
			"Invalid Video Name 2" => [
				"params"   => [
					"img"  => "IMG23_345.mp4",
					"ext"  => "mp4",
				],
				"expected" => [
					"result" => false
				],
			],
		];
	}
}
