<?php


namespace prokits;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use SplFileObject;
use Throwable;

class Patches extends PluginBase {
	/** @var Patches */
	private static $instance = null;
	
	private $loaderPath = '';
	
	public function onLoad() : void {
		self::$instance = $this;
		$this->loaderPath = $this->getDataFolder() . DIRECTORY_SEPARATOR . 'patches' . DIRECTORY_SEPARATOR;
		//TODO Improve this
		$str = $this->loaderPath . 'first';
		@mkdir($str);
		if(!file_exists($str . DIRECTORY_SEPARATOR . 'bootstrap.php')) {
			@file_put_contents($str . DIRECTORY_SEPARATOR . 'bootstrap.php' , '<?php
			use prokits\Patches;
			Patches::getInstance()->getLogger()->warning(\'This is demo Patch.\');
			');
		}
		@mkdir($this->loaderPath);
	}
	
	public function onCommand(CommandSender $sender , Command $command , string $label , array $args) : bool {
		if($sender instanceof ConsoleCommandSender && $label === 'loadPatch') {
			if(!isset($args[0])) {
				return false;
			}
			$this->loadPatch($this->loaderPath , $args[0]);
		}
		return true;
		
	}
	
	public static function getInstance() : Patches {
		return self::$instance;
	}
	
	public function loadPatch(string $basePath , string $patchName) : void {
		try {
			try {
				$file = new SplFileObject($basePath . $patchName . DIRECTORY_SEPARATOR . 'bootstrap.php');
			} catch(Throwable $logicException) {
				$this->getLogger()->info(TextFormat::RED . "Invalid Patch(or Bad Format).");
				return;
			}
			require $file->getRealPath();
		} catch(Throwable $throwable) {
			$this->getLogger()->warning('Error when loading ' . $patchName);
			$this->getLogger()->emergency($throwable);
		}
	}
}