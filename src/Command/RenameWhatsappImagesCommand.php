<?php


namespace App\Command;


use App\Service\RenameCanonImage;
use App\Service\RenameWhatsappImage;
use DateTime;
use DirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenameWhatsappImagesCommand extends Command
{
	protected static $defaultName = "images-wa";
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
	private $renameWhatsappImage;
	
	/**
	 * RenameCanonImages constructor.
	 * @param RenameCanonImage $renameWhatsappImage
	 */
	public function __construct(RenameWhatsappImage $renameWhatsappImage)
	{
		parent::__construct();
		$this->renameWhatsappImage = $renameWhatsappImage;
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
		$iterator   = new DirectoryIterator($this->path);
		$duplicated = 0;
		foreach ($iterator as $file) {
			if ($file->isDot()) {
				continue;
			}
			
			if (!$file->isFile()) {
				continue;
			}
			
			$file_ext = strtolower($file->getExtension());
			
			if ($file_ext !== "jpg" && $file_ext !== "jpeg") {
				continue;
			}
			
			$time_str = $this->getBTime($file->getRealPath());
			
			if (null === $time_str) {
				continue;
			}
			
			$date     = new DateTime($time_str);
			$fileName = $file->getFilename();
			
			$new_name = $this->renameWhatsappImage->handle($fileName, $file_ext, $date);
			
			if ($new_name === false) {
				continue;
			}
			
			$new_name = $this->preventReplaceDuplicated($new_name);
			
			rename($this->createFullPath($fileName), $this->createFullPath($new_name));
			$this->output->writeln(sprintf("Renamed %s to %s", $fileName, $new_name));
			
		}
		
	}
	
	private function createFullPath(string $file_name)
	{
		return sprintf($this->path . "/%s", $file_name);
	}
	
	private function getBTime(string $path)
	{
		//https://stackoverflow.com/questions/6176140/how-do-i-get-actual-creation-time-for-a-file-in-php-on-a-mac
		$handle = popen('stat -f %B ' . escapeshellarg($path), 'r');
		
		if (!$handle) {
			return null;
		}
		
		$btime         = trim(fread($handle, 100));
		$creation_time = strftime("%Y-%m-%d %H:%M:%S", $btime);
		pclose($handle);
		
		return $creation_time;
	}
	
	private function preventReplaceDuplicated(string $original_name, int $duplicated = 0)
	{
		if (!file_exists($this->createFullPath($original_name))) {
			return $original_name;
		}
		
		$new_name = $this->createDuplicatedFileName($original_name, $duplicated);
		
		while (file_exists($this->createFullPath($new_name))) {
			$duplicated ++;
			$new_name = $this->createDuplicatedFileName($original_name, $duplicated);
		}
		
		return $new_name;
		
	}
	
	private function createDuplicatedFileName($original_name, $duplicated = 0)
	{
		$extension_pos = strrpos($original_name, '.'); // find position of the last dot, so where the extension starts
		
		return substr($original_name, 0, $extension_pos) . '_' . $duplicated . substr($original_name, $extension_pos);
		
	}
}