<?php


namespace prokits;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use Symfony\Component\Filesystem\Path;
use Throwable;

class Patches extends PluginBase {
	private static self $instance;

	public function onLoad() : void {
		self::$instance = $this;
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if ($sender instanceof ConsoleCommandSender) {
			switch ($label) {
				case 'loadPatch':
					if (!isset($args[0])) {
						return false;
					}

					$path = realpath(Path::join($this->getDataFolder(), $args[0] . ".php"));
					if ($path === false) {
						$sender->sendMessage(TextFormat::RED . "Failed to obtain $args[0]'s absolute path");
						return true;
					}
					if (!is_file($path)) {
						$sender->sendMessage(TextFormat::RED . "$args[0] is not a file");
						return true;
					}
					try {
						require $path;
					} catch (Throwable $e) {
						$sender->sendMessage(TextFormat::YELLOW . "Error while loading patch $args[0]");
						$sender->sendMessage(implode(TextFormat::YELLOW . "\n", Utils::printableExceptionInfo($e)));
					}
					break;
				case 'listPatch':
					$dir = scandir($this->getDataFolder(), SCANDIR_SORT_NONE);
					if ($dir === false) {
						$sender->sendMessage(TextFormat::RED . "Failed to scan directory");
						return false;
					}
					$dir = array_diff($dir, ['..', '.']);
					$count = count($dir);
					$sender->sendMessage(TextFormat::GREEN . "Available patches($count): " . implode(", ", array_map(Path::getFilenameWithoutExtension(...), $dir)));
			}
		} else {
			$sender->sendMessage(TextFormat::RED . "This command is only available via the console");
		}
		return true;

	}

	public static function getInstance() : Patches {
		return self::$instance;
	}
}