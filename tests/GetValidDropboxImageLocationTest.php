<?php

use App\Service\GetValidDropboxImageLocation;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GetValidDropboxImageLocationTest extends TestCase
{
	/**
	 * @param array $params
	 * @param array $expected
	 * @dataProvider dataTestProvider
	 */
	public function testHandle(array $params, array $expected)
	{
		$container = m::mock(ContainerInterface::class);
		$container
			->allows('getParameter')
			->withAnyArgs()
			->andReturn('/Users/kanfor/Pictures');
		
		$service = new GetValidDropboxImageLocation($container);
		
		$result = $service->handle(
			$params['img']
		);
		
		$this->assertSame($expected['result'], $result);
	}
	
	public function dataTestProvider()
	{
		return [
			"Invalid image name" => [
				"params"   => [
					"img"  => "IMG_23_345.JPG",
				],
				"expected" => [
					"result" => null
				],
			],
			"Invalid image name 2" => [
				"params"   => [
					"img"  => '2018-07-12 15-23-56.jpg',
				],
				"expected" => [
					"result" => null
				],
			],
			"Invalid image name 3" => [
				"params"   => [
					"img"  => '2018.07.12 15-23-56.jpg',
				],
				"expected" => [
					"result" => null
				],
			],
			"Valid image name" => [
				"params"   => [
					"img"  => '2018-07-12 15.23.56.jpg',
				],
				"expected" => [
					"result" => '/Users/kanfor/Pictures/2018/2018-07-julio/2018-07-12 15.23.56.jpg'
				],
			],
			"Valid image Name 2" => [
				"params"   => [
					"img"  => '2019-09-15 16.25.53.jpg',
				],
				"expected" => [
					"result" => '/Users/kanfor/Pictures/2019/2019-09-septiembre/2019-09-15 16.25.53.jpg'
				],
			],
			"Valid image Name png" => [
				"params"   => [
					"img"  => '2019-09-15 16.25.53.png',
				],
				"expected" => [
					"result" => '/Users/kanfor/Pictures/2019/2019-09-septiembre/2019-09-15 16.25.53.png'
				],
			],
			"Valid video Name mp4" => [
				"params"   => [
					"img"  => '2019-09-15 16.25.53.mp4',
				],
				"expected" => [
					"result" => '/Users/kanfor/Pictures/2019/2019-09-septiembre/2019-09-15 16.25.53.mp4'
				],
			],
			"Valid image Name with duplicated" => [
				"params"   => [
					"img"  => '2019-09-15 16.25.53-1.jpg',
				],
				"expected" => [
					"result" => '/Users/kanfor/Pictures/2019/2019-09-septiembre/2019-09-15 16.25.53-1.jpg'
				],
			],
		];
	}
}
