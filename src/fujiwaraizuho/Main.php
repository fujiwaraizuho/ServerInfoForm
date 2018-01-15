<?php

namespace fujiwaraizuho;

/* Base */
use pocketmine\plugin\PluginBase;

/* Event */
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;

/* Utils */
use pocketmine\utils\Config;

/* Packet */
use pocketmine\network\mcpe\protocol\ServerSettingsResponsePacket;
use pocketmine\network\mcpe\protocol\ServerSettingsRequestPacket;


class Main extends PluginBase implements Listener
{

	public $config_Data = [];
	public $data = [];

	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this,$this);

		if (!file_exists($this->getDataFolder())) {
			mkdir($this->getDataFolder(), 0744, true);
		}

		$this->config = new Config($this->getDataFolder() . "Config.yml", Config::YAML,
			[
				'タイトル' => 'サーバー紹介',
				'サーバー紹介文' => 'Hello World!',
				'アイコンを設定するか' => true,
				'アイコンURL' => 'https://minecraft-ja.gamepedia.com/media/minecraft-ja.gamepedia.com/c/c5/Grass.png',
			]);

		$config_Data = $this->config->getAll();

		if ($config_Data["アイコンを設定するか"] === true) {

			$data = [
				"type" => "custom_form",
				"title" => $config_Data["タイトル"],
				"icon" => [
					"type" => "url",
					"data" => $config_Data["アイコンURL"]
				],
				"content" => [
					[
						"type" => "label",
						"text" => $config_Data["サーバー紹介文"]
					]
				]
			];

		} else {

			$data = [
				"type" => "custom_form",
				"title" => $config_Data["タイトル"],
				"content" => [
					[
						"type" => "label",
						"text" => $config_Data["サーバー紹介文"]
					]
				]
			];

		}

		$this->data = json_encode($data);

		$this->getLogger()->info("§aINFO§f >> §aEnabled...");
	}


	public function onData(DataPacketReceiveEvent $event)
	{
		$player = $event->getPlayer();
		$packet = $event->getPacket();
		$rand = mt_rand(PHP_INT_MIN, PHP_INT_MAX);

		if ($packet instanceof ServerSettingsRequestPacket) {

			$pk = new ServerSettingsResponsePacket();
			$pk->formId = $rand;
			$pk->formData = $this->data;
			$player->dataPacket($pk);

		}
	}


	public function onDisable()
	{
		$this->getLogger()->info("§cINFO§f >> §cDisabled...");
	}
}
