<?php
declare(strict_types=1);

namespace YTBJero\AutoSmeltOre;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\EventPriority;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\ItemIds;

class Main extends PluginBase implements Listener{
    /** @var Item[] */
    protected array $ores;

    /**
     * @throws \ReflectionException
     */
    public function onEnable() : void{
        $this->saveDefaultConfig();
        $this->ores = [
            VanillaBlocks::IRON_ORE()->getName() => VanillaItems::IRON_INGOT(),
            VanillaBlocks::DEEPSLATE_IRON_ORE()->getName() => VanillaItems::IRON_INGOT(),
            VanillaBlocks::GOLD_ORE()->getName() => VanillaItems::GOLD_INGOT(),
            VanillaBlocks::DEEPSLATE_GOLD_ORE()->getName() => VanillaItems::GOLD_INGOT(),
            VanillaBlocks::ANCIENT_DEBRIS()->getName() => VanillaItems::NETHERITE_SCRAP()
        ];
        $onBreak = \Closure::fromCallable([$this, "onBreak"]);
        $this->getServer()->getPluginManager()->registerEvent(BlockBreakEvent::class, $onBreak, EventPriority::NORMAL, $this);
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
        if (!$player->hasPermission("autosmeltore.action.allow")) return;
        if ($this->getConfig()->get("permission", true)) {

            if($this->getConfig()->get(str_replace(" ", "_", $block->getName()), true)){
                $drops = [];
                foreach ($block->getDrops($item) as $drop) {
                    $drops[] = $this->ores[$block->getName()] ?? $drop;
                }
                $event->setDrops($drops);
            }
        }
    }
}