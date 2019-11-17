<?php

namespace aieuo\mineflow\condition;

use pocketmine\entity\Entity;
use pocketmine\Player;
use aieuo\mineflow\utils\Language;
use aieuo\mineflow\recipe\Recipe;
use aieuo\mineflow\condition\TypeItem;

class ExistsItem extends TypeItem {

    protected $id = self::EXISTS_ITEM;

    protected $name = "@condition.existsItem.name";
    protected $description = "@condition.existsItem.description";
    protected $detail = "condition.existsItem.detail";

    public function execute(?Entity $target, ?Recipe $origin = null): ?bool {
        if (!($target instanceof Player)) return null;

        if (!$this->isDataValid()) {
            $target->sendMessage(Language::get("invalid.contents", [$this->getName()]));
            return null;
        }

        $item = $this->getItem();
        return $target->getInventory()->contains($item);
    }
}