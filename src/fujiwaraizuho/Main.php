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

	const FORM_ID = 114514;
	public $config_Data = [];
	public $data = [];

	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this,$this);

		if (!file_exists($this->getDataFolder())) {
			mkdir($this->getDataFolder(), 0744, true);
		}

		$this->license = new Config($this->getDataFolder() . "LICENSE.yml", Config::YAML,
			array(
				'LICENSE' => '入力してください'
			));

		$this->config = new Config($this->getDataFolder() . "Config.yml", Config::YAML,
			array(
				'タイトル' => 'サーバー紹介',
				'サーバー紹介文' => 'Hello World!',
				'アイコンを設定するか' => true,
				'アイコンURL' => 'https://minecraft-ja.gamepedia.com/media/minecraft-ja.gamepedia.com/c/c5/Grass.png',
			));

		$config_Data = $this->config->getAll();

		////【警告】以下のライセンス認証コードを改変および削除することはライセンス違反となります【警告】////
		$url = "https://fujipvp.info/pluginLICENSE.txt";
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$license =  curl_exec($ch);
		curl_close($ch);

		$number = $this->license->get('LICENSE');

		if($license == $number){

			$this->getLogger()->info("§aINFO§f >> §aライセンス認証に成功しました。");

		}else{

			$this->getLogger()->info("§cERROR§f >> §cライセンス認証に失敗しました。");
			$this->getLogger()->info("§cERROR§f >> §cLICENSE.ymlに");
			$this->getLogger()->info("§cERROR§f >> §chttps://fujipvp.info/license.html");
			$this->getLogger()->info("§cERROR§f >> §cの正しいライセンスキーを入力してください");

			$this->getServer()->getPluginManager()->disablePlugin($this);

			return true;
		}
		////【警告】以上のライセンス認証コードを改変および削除することはライセンス違反となります【警告】////

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

		if ($packet instanceof ServerSettingsRequestPacket) {

			$pk = new ServerSettingsResponsePacket();
			$pk->formId = self::FORM_ID;
			$pk->formData = $this->data;
			$player->dataPacket($pk);

		}
	}


	public function onDisable()
	{
		$this->getLogger()->info("§cINFO§f >> §cDisabled...");
	}
}
