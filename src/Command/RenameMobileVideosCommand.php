<?php


namespace App\Command;


use App\Service\RenameMobileVideo;
use DirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenameMobileVideosCommand extends Command
{
	protected static $defaultName = "videos-mobile";
	/**
	 * @var string
	 */
	private $path;
	/**
	 * @var OutputInterface
	 */
	private $output;
	/**
	 * @var RenameMobileVideo
	 */
	private $renameMobileVideo;
	
	/**
	 * RenameMobileVideosCommand constructor.
	 * @param RenameMobileVideo $renameMobileVideo
	 */
	public function __construct(RenameMobileVideo $renameMobileVideo)
	{
		parent::__construct();
		$this->renameMobileVideo = $renameMobileVideo;
	}
	
	
	public function configure()
	{
		
		$this
			->setDescription('Moves dropbox named images from original folder to destiny folder (images with name yyyy-mm-dd hh:mm:ss')
			->addArgument("path", InputArgument::REQUIRED);
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->output = $output;
		$path         = $input->getArgument("path");
		
		$this->renameVideos($path);
	}
	
	private function renameVideos(string $path)
	{
		$this->path = $path;
		$iterator   = new DirectoryIterator($path);
		
		foreach ($iterator as $file) {
			if ($file->isDot()) {
				continue;
			}
			
			if (!$file->isFile()) {
				continue;
			}
			
			$file_ext = strtolower($file->getExtension());
			
			if ($file_ext !== "mp4") {
				continue;
			}
			
			$file_name = $file->getFilename();
			
			$new_name = $this->renameMobileVideo->handle(
				$file->getFilename(),
				$file_ext
			);
			
			if (false === $new_name) {
				continue;
			}
			
			rename($this->createFullPath($file_name), $this->createFullPath($new_name));
			$this->output->writeln(sprintf("Renamed %s to %s", $file_name, $new_name));
		}
		
	}
	
	private function createFullPath(string $file_name)
	{
		return sprintf($this->path . "/%s", $file_name);
	}
}