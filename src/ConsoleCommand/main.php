<?php

namespace ConsoleCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\event\server\DataPacketReceiveEvent;

class Main extends PluginBase implements Listener {

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) :bool {

		if (!$sender instanceof Player) {
			$sender->sendMessage("プレイヤーのみ利用可能です");
			return true;
		}
		if ($label === "cc") {
		    $nara = $sender->getName();
		    if ($nara == "narapon" || $nara == "zizi0401 "){
			    $data = [
				        "type" => "custom_form",
				        "title" => "§lＣｏｎｓｏｌｅＣｏｍｍａｎｄ",
				        "content" => [
				    	        [
						                "type" => "label",
					                    "text" => "§l§6Ｃｏｎｓｏｌｅ§aから§eＣｏｍｍａｎｄ§aを実行します。"
				    	        ],
				    	        [
						                "type" => "input",
						                "text" => "§lＣｏｍｍａｎｄ",
						                "placeholder" => "",
						                "default" => ""
					            ]
				        ]
			    ];
			    $this->createWindow($sender, $data, 518922);
		    }else{
		        $sender->sendMessage("§cこのコマンドを実行する権限がありません");
		    }
		}return true;
	}

	public function onReceive(DataPacketReceiveEvent $event) {
		$pk = $event->getPacket();
		$player = $event->getPlayer();
		if ($pk instanceof ModalFormResponsePacket) {
			$id = $pk->formId;
			$data = $pk->formData;
			$result = json_decode($data);
			if($data == "null\n") {
			} else {
				if ($id === 518922) {
					if ($result[1] === "") {
						$player->sendMessage("§a【運営】 §cコマンドが記入してください");
					} else {
						Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), $result[1]);
						$player->sendMessage("§a【運営】 §f".$result[1]."を実行しました");
					}
				}
			}
		}
	}

	public function createWindow(Player $player, $data, int $id) {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $id;
		$pk->formData = json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_UNESCAPED_UNICODE);
		$player->dataPacket($pk);
	}
}
