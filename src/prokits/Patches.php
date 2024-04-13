<?php


namespace prokits;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use SplFileObject;
use Symfony\Component\Filesystem\Path;
use Throwable;

class Patches extends PluginBase {
	private static self $instance;

	public function onLoad() : void {
		self::$instance = $this;
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if ($sender instanceof ConsoleCommandSender && $label === 'loadPatch') {
			if (!isset($args[0])) {
				return false;
			}
			$this->loadPatch($args[0]);
		}
		return true;

	}

	public static function getInstance() : Patches {
		return self::$instance;
	}

	public function loadPatch(string $patch) : void {
		$logger = $this->getLogger();
		try {
			try {
				$file = new SplFileObject(Path::join($this->getDataFolder(), $patch . ".php"));
			} catch (Throwable) {
				$logger->error("invalid patch $patch.");
				return;
			}
			require $file->getRealPath();
		} catch (Throwable $throwable) {
			$logger->error("error applying patch $patch.");
			$logger->logException($throwable);
		}
	}
}