<?php
namespace TheQuiver;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\OfflinePlayer;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\CallbackTask;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\sound\FizzSound;
use pocketmine\level\sound\ClickSound;
use pocketmine;

class Main extends PluginBase implements Listener
{

	private static $obj = null;
	public static function getInstance()
	{
		return self::$obj;
	}
	public function onEnable()
	{
		if(!self::$obj instanceof Main)
		{
			self::$obj = $this;
		}
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this,"gameTimber"]),20);
		@mkdir($this->getDataFolder(), 0777, true);
		$this->config=new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
		$this->lastTime=0;
		$this->players=array();
		$this->all=0;
		$this->config->save();
		$this->getServer()->getLogger()->info(TextFormat::GRAY."===============================");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."[OneTheQuiver] Plugin Aktivite Edildi");
		$this->getServer()->getLogger()->info(TextFormat::AQUA."[OneTheQuiver] Plugin Yapimi : EmreTr1");
		$this->getServer()->getLogger()->info(TextFormat::RED."[OneTheQuiver] Config Kaydedildi");
	    $this->getServer()->getLogger()->info(TextFormat::GOLD."[OneTheQuiver] Eğlence!!");
		$this->getServer()->getLogger()->info(TextFormat::GRAY."===============================");
	}
	
	public function OnCommand(CommandSender $sender, Command $command, $label, array $args)
	{
		if($command->getName()=="thequiver"){
			$sender->sendMessage(TextFormat::RED . "This Game not avaliable!");
		}
	}
	public function onPlayerCommand(PlayerCommandPreprocessEvent $event)
	{
		if(!$this->PlayerIsInGame($event->getPlayer()->getName()) || $event->getPlayer()->isOp() || substr($event->getMessage(),0,1)!="/")
		{
			unset($event);
			return;
		}
		switch(strtolower(explode(" ",$event->getMessage())[0]))
		{
		case "/lobby":
			break;
		default:
			$event->setCancelled();
			$event->getPlayer()->sendMessage("Oyun içinde Komut Kullanmak Yasak!");
			break;
		}
		unset($event);
	}
	
	public function onDamage(EntityDamageEvent $event)
	{
		$player = $event->getEntity();
		if ($event instanceof EntityDamageByEntityEvent)
		{
        	$player = $event->getEntity();
        	$killer = $event->getDamager();
			if($player instanceof Player && $killer instanceof Player)
			{
		    	if($this->PlayerIsInGame($player->getName()) && ($this->gameStatus==2 || $this->gameStatus==1))
		    	{
		    		$event->setCancelled();
		    	}
		    	if($this->PlayerIsInGame($player->getName()) && !$this->PlayerIsInGame($killer->getName()) && !$killer->isOp())
		    	{
		    		$event->setCancelled();
		    		$killer->sendPopUp("damage!");
		    		$killer->kill();
		    	}
		    }
		}
		
		unset($player,$killer,$event);
	}
	
	public function PlayerIsInGame($name)
	{
		return isset($this->players[$name]);
	}
	
	public function PlayerDeath(PlayerDeathEvent $event)
	{
	$p = $event->getPlayer;
		$p->setLevel($this->level);
		eval("\$p->teleport(\$this->pos".$i.");");
		unset($p);
		$p->sendTip("§aRespawning...");
		$p->getInventory()->addItem(Item::get(272, 0, 1));
		$p->getInventory()->addItem(Item::get(261, 0, 1));
		$p->getInventory()->addItem(Item::get(262, 0, 1));
	}
	
	public function sendToAll($msg){
		foreach($this->players as $pl)
		{
			$this->getServer()->getPlayer($pl["id"])->sendMessage($msg);
		}
		$this->getServer()->getLogger()->info($msg);
		unset($pl,$msg);
	}
	
	public function gameTimber(){
		$this->lastTime--;
			switch($this->lastTime)
			{
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
			case 20:
			case 30:
				$this->sendToAll("§a§aOneTheQuiver] Başlıyorr §d" .$this->lastTime. " §bSaniye");
				break;
			case 60:
				$this->sendToAll(" §aOneTheQuiver] §eOyunun Başlamasına §b1 Dakika §aKaldı ! ");
				break;
			case 90:
				$this->sendToAll(" §a[OneTheQuiver] §eOyunun Başlamasına §b1 Dakika §a 30 Saniye §dKaldı !");
				break;
			case 120:
				$this->sendToAll(" §a[OneTheQuiver] §eOyunun Başlamasına §b2 Dakika §dKaldı ! ");
				break;
			case 150:
				$this->sendToAll(" §a[OneTheQuiver] §eOyunun Başlamasına §b2 Dakika §a30 Saniye §9Kaldı ! ");
				break;
			case 0:
				$this->gameStatus=2;
				$this->sendToAll("§aGame Started!!!");
				$this->lastTime=$this->godTime;
				foreach($this->players as $key=>$val)
				{
					$p->setHealth(25);
				}
				$this->all=count($this->players);
				break;
			}
		}
		if(/* sonra */)
		{
			$this->lastTime--;
			switch($this->lastTime)
			{
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
				$this->sendToAll("§e".$this->lastTime);
				break;
			case 0:
				$this->sendToAll("§aYeniden doğdun.");
				foreach($this->players as $pl)
				{
					$p=$this->getServer()->getPlayer($pl["id"]);
					unset($p,$pl);
				}
				//$this->lastTime=$this->endTime;
				break;
			}
		}
		if($this->gameStatus==4)
		{
			$this->lastTime--;
			switch($this->lastTime)
			{
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
			case 20:
			case 30:
				$this->sendToAll("Maçın Sonlanmasına ".$this->lastTime." Sn. Kaldı !");
				break;
			case 0:
				$this->sendToAll("§eMaç Sonlandı !");
				Server::getInstance()->broadcastMessage("§bKazanan §c Yok !");
				foreach($this->players as $pl)
				{
					$p=$this->getServer()->getPlayer($pl["id"]);
					$p->getInventory()->clearAll();
					$p->setMaxHealth(25);
					$p->setHealth(25);
					unset($p,$pl);
				}
				$this->ClearAllInv();
				$this->players=array();
				$this->lastTime=0;
				break;
			}
		}
	}
	
	public function getMoney($name){
		return EconomyAPI::getInstance()->myMoney($name);
	}
	
	public function addMoney($name,$money){
		EconomyAPI::getInstance()->addMoney($name,$money);
		unset($name,$money);
	}
	
	public function setMoney($name,$money){
		EconomyAPI::getInstance()->setMoney($name,$money);
		unset($name,$money);
	}
	
	public function ClearInv($player)
	{
		if(!$player instanceof Player)
		{
			unset($player);
			return;
		}
		$inv=$player->getInventory();
		if(!$inv instanceof Inventory)
		{
			unset($player,$inv);
			return;
		}
		$inv->clearAll();
		unset($player,$inv);
	}
	
	public function ClearAllInv()
	{
		foreach($this->players as $pl)
		{
			$player=$this->getServer()->getPlayer($pl["id"]);
			if(!$player instanceof Player)
			{
				continue;
			}
			$this->ClearInv($player);
		}
		unset($pl,$player);
	}
	
	public function onDisable(){
		$this->getLogger()->info(TextFormat::GOLD . "============================");
		$this->getLogger()->info(TextFormat::RED . "TheQuiver Has Been Disabed!");
		$this->getLogger()->info(TextFormat::GOLD . "============================");
		$this->config->save();
		$this->getLogger()->info(TextFormat::GREEN . "* Config kaydedildi! *");
	}
}
