<?php


namespace App\Command;


use App\Service\GetValidDropboxImageLocation;
use DirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderDropboxImagesCommand extends Command
{
	protected static $defaultName = "dropbox:order";
	/**
	 * @var SymfonyStyle
	 */
	private $io;
	/**
	 * @var string
	 */
	private $path;
	/**
	 * @var OutputInterface
	 */
	private $output;
	/**
	 * @var GetValidDropboxImageLocation
	 */
	private $getValidDropboxImageLocation;
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * RenameCanonImages constructor.
	 * @param GetValidDropboxImageLocation $getValidDropboxImageLocation
	 * @param ContainerInterface $container
	 * @internal param RenameCanonImage $renameCanonImage
	 */
	public function __construct(GetValidDropboxImageLocation $getValidDropboxImageLocation, ContainerInterface $container)
	{
		parent::__construct();
		$this->getValidDropboxImageLocation = $getValidDropboxImageLocation;
		$this->container                    = $container;
	}
	
	
	public function configure()
	{
		$this
			->setDescription('Moves dropbox named images from original folder to destiny folder (images with name yyyy-mm-dd hh:mm:ss')
			->addArgument("path", InputArgument::OPTIONAL);
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$path = $input->getArgument("path") ?? $this->container->getParameter('dropbox_to_order_root');
		
		$this->output = $output;
		$this->io     = new SymfonyStyle($input, $output);
		$this->orderImages($path);
	}
	
	private function orderImages(string $path)
	{
		$this->path = $path;
		$iterator   = new DirectoryIterator($this->path);
		
		foreach ($iterator as $file) {
			if ($file->isDot()) {
				continue;
			}
			
			if (!$file->isFile()) {
				continue;
			}
			
			$old_path    = $file->getRealPath();
			$destination = $this->getValidDropboxImageLocation->handle($file->getFileName());
			
			if(null === $destination){
				continue;
			}
			
			$this->createFolder($destination);
			
			if (rename($old_path, $destination)) {
				$this->io->success(sprintf("Moved from %s to %s", $old_path, $destination));
				continue;
			}
			
			$this->io->error(sprintf("ERROR MOVING FILE from %s to %s", $old_path, $destination));
			
		}
		
	}
	
	private function createFolder(string $destination)
	{
		$dir_path = dirname($destination);
		if(!is_dir($dir_path)){
			mkdir($dir_path, 0777, true);
		}
	}
}