<?php

namespace aieuo\mineflow;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use aieuo\mineflow\utils\Language;
use aieuo\mineflow\manager\BlockRecipeManager;
use aieuo\mineflow\condition\ConditionFactory;
use aieuo\mineflow\command\MineflowCommand;
use aieuo\mineflow\action\script\ScriptFactory;
use aieuo\mineflow\action\process\ProcessFactory;

class Main extends PluginBase {

    /** @var Main */
    private static $instance;

    /** @var Config */
    private $config;

    /** @var bool */
    private $loaded = false;

    public static function getInstance(): ?self {
        return self::$instance;
    }

    public function onEnable() {
        self::$instance = $this;

        $serverLanguage = $this->getServer()->getLanguage()->getLang();
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, [
            "language" => $serverLanguage,
        ]);
        $this->config->save();

        Language::setLanguage($this->config->get("language", "eng"));
        if (!Language::loadMessage()) {
            foreach (Language::getLoadErrorMessage($serverLanguage) as $error) {
                $this->getLogger()->warning($error);
            }
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        $this->getServer()->getCommandMap()->register($this->getName(), new MineflowCommand);

        ScriptFactory::init();
        ProcessFactory::init();
        ConditionFactory::init();

        $this->blockRecipe = new BlockRecipeManager($this);

        $this->loaded = true;
    }

    public function onDisable() {
        if (!$this->loaded) return;
        $this->blockRecipe->saveAll();
    }

    public function getConfig(): Config {
        return $this->config;
    }

    public function getBlockRecipeManager(): BlockRecipeManager {
        return $this->blockRecipe;
    }
}