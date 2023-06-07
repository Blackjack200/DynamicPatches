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
	private string $loaderPath;

	public function onLoad() : void {
		self::$instance = $this;
		$this->loaderPath = Path::join($this->getDataFolder(), 'patches');
		@mkdir($this->loaderPath);
		$demoPatch = Path::join($this->loaderPath, 'demo');
		if (!is_dir($demoPatch)) {
			@mkdir($demoPatch);
			if (!file_exists(Path::join($demoPatch, 'bootstrap.php'))) {
				@file_put_contents(Path::join($demoPatch, 'bootstrap.php'), '<?php
			use prokits\Patches;
			Patches::getInstance()->getLogger()->warning(\'This is demo Patch.\');
			');
			}
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if ($sender instanceof ConsoleCommandSender && $label === 'loadPatch') {
			if (!isset($args[0])) {
				return false;
			}
			$this->loadPatch($this->loaderPath, $args[0]);
		}
		return true;

	}

	public static function getInstance() : Patches {
		return self::$instance;
	}

	public function loadPatch(string $basePath, string $patch) : void {
		try {
			try {
				$file = new SplFileObject(Path::join($basePath, $patch, 'bootstrap.php'));
			} catch (Throwable $logicException) {
				$this->getLogger()->error("Invalid Patch $patch");
				return;
			}
			require $file->getRealPath();
		} catch (Throwable $throwable) {
			$this->getLogger()->error("Error when applying patch $patch");
			$this->getLogger()->logException($throwable);
		}
	}
}