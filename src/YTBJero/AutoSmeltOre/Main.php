<?php
declare(strict_types=1);

namespace YTBJero\AutoSmeltOre;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\ItemIds;
use pocketmine\item\StringToItemParser;

class Main extends PluginBase implements Listener{
	/** @var Item[] */
	protected array $ores;

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->ores = [
			ItemIds::COAL_ORE => StringToItemParser::getInstance()->parse("coal"),
			ItemIds::IRON_ORE => StringToItemParser::getInstance()->parse("iron_ingot"),
			ItemIds::GOLD_ORE => StringToItemParser::getInstance()->parse("gold_ingot"),
			ItemIds::REDSTONE_ORE => StringToItemParser::getInstance()->parse("redstone"),
			ItemIds::LIT_REDSTONE_ORE => StringToItemParser::getInstance()->parse("redstone"),
			ItemIds::DIAMOND_ORE => StringToItemParser::getInstance()->parse("diamond"),
			ItemIds::EMERALD_ORE => StringToItemParser::getInstance()->parse("emerald"),
			ItemIds::QUARTZ_ORE => StringToItemParser::getInstance()->parse("quartz")
		];
	}

	/**
	 * @param BlockBreakEvent $event
	 *
	 * @priority NORMAL
	 * @handleCancelled FALSE
	 */
	public function onBreak(BlockBreakEvent $event) : void{
		$item = $event->getItem();
		$block = $event->getBlock();
		$player = $event->getPlayer();
		if($item->hasEnchantment(VanillaEnchantments::SILK_TOUCH())){
			return;
		}
		if($this->getConfig()->get("Permission", true) and $player->hasPermission("autosmeltore.action.allow")){

			if($this->getConfig()->get(str_replace(" ", "_", $block->getName()))){
				$drops = array_map(fn($item) => in_array($item->getId(), array_keys($this->ores)) ? $this->ores[$item->getId()] : $item, $event->getDrops());
				$event->setDrops($drops);
			}
		}
	}
}