<?php


namespace prokits;


use LogicException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use RuntimeException;
use SplFileInfo;
use SplFileObject;
use Throwable;

class Patches extends PluginBase {
	/** @var Patches */
	private static $instance = null;
	
	private $loaderPath = '';
	
	public function onLoad() : void {
		self::$instance = $this;
		$this->loaderPath = $this->getDataFolder() . DIRECTORY_SEPARATOR . 'patches' . DIRECTORY_SEPARATOR;
	}
	
	public function onCommand(CommandSender $sender , Command $command , string $label , array $args) : bool {
		if($sender instanceof ConsoleCommandSender && $label === 'loadPatch') {
			if(!isset($args[0])) {
				return false;
			}
			
		}
		return true;
		
	}
	
	public static function getInstance() : Patches {
		return self::$instance;
	}
	
	public function loadPatch(string $patchName) : void {
		try {
			try {
				$file = new SplFileObject($this->loaderPath . $patchName . DIRECTORY_SEPARATOR . 'bootstrap.php');
				require $file->getRealPath();
			} catch(RuntimeException $runtimeException) {
				$this->getLogger()->info(TextFormat::RED . "File is unreadable.");
				return;
			} catch(LogicException $logicException) {
				$this->getLogger()->info(TextFormat::RED . "Invalid Patch.");
				return;
			}
			
		} catch(Throwable $throwable) {
			$this->getLogger()->warning('Error when loading ' . $patchName . '');
		}
	}
}