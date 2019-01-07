<?php


namespace App\Command;


use App\Service\RenameCanonImage;
use DateTime;
use DirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenameCanonImagesCommand extends Command
{
	protected static $defaultName = "images-canon";
	/**
	 * @var string
	 */
	private $path;
	/**
	 * @var OutputInterface
	 */
	private $output;
	/**
	 * @var RenameCanonImage
	 */
	private $renameCanonImage;
	
	/**
	 * RenameCanonImages constructor.
	 * @param RenameCanonImage $renameCanonImage
	 */
	public function __construct(RenameCanonImage $renameCanonImage)
	{
		parent::__construct();
		$this->renameCanonImage = $renameCanonImage;
	}
	
	
	public function configure()
	{
		$this->addArgument("path", InputArgument::REQUIRED);
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$path         = $input->getArgument("path");
		$this->output = $output;
		
		$this->renameImages($path);
	}
	
	private function renameImages(string $path)
	{
		$this->path = $path;
		$iterator = new DirectoryIterator($this->path);
		
		foreach ($iterator as $file) {
			if ($file->isDot()) {
				continue;
			}
			
			if (!$file->isFile()) {
				continue;
			}
			
			$file_ext = strtolower($file->getExtension());
			
			if ($file_ext !== "jpg") {
				continue;
			}
			
			$exif_data = read_exif_data($file->getFileName());
			$date      = new DateTime($exif_data['DateTimeDigitized']);
			
			$fileName = $file->getFileName();
			
			$new_name = $this->renameCanonImage->handle($fileName, $file_ext, $date);
			
			if ($new_name === false) {
				continue;
			}
			
			rename($this->createFullPath($fileName), $this->createFullPath($new_name));
			$this->output->writeln(sprintf("Renamed %s to %s", $fileName, $new_name));
			
		}
		
	}
	
	private function createFullPath(string $file_name)
	{
		return sprintf($this->path . "/%s", $file_name);
	}
}