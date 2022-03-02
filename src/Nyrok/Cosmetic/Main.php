<?php

namespace Nyrok\Cosmetic;

use JsonException;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use Nyrok\Cosmetic\skin\resetSkin;
use Nyrok\Cosmetic\skin\saveSkin;
use Nyrok\Cosmetic\skin\setSkin;
use Nyrok\Cosmetic\check\checkRequirement;
use Nyrok\Cosmetic\check\checkCosmetique;

class Main extends PluginBase implements Listener {
    /** @var self $instance */
    public static Main $instance;

    public array $cosmetiqueTypes = [];

    public array $cosmetiqueDetails = [];

    public function onEnable(): void {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $a = new checkRequirement();
        $a->checkRequirement();

        $a = new checkCosmetique();
        $a->checkCosmetique();

        $this->getServer()->getLogger()->info("This Plugin has been updated by @Nyrok10 on Twitter, but still searching the original creator..");
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
        if($sender instanceof Player) {
            switch(strtolower($command->getName())) {
                case "cosmetic":
                    $this->mainform($sender, "");
                    break;
            }
        }else {
            $sender->sendMessage("§cVous pouvez uniquement executer cette commande en jeu !");
        }
        return true;
    }

    public function mainform(Player $player, string $txt): SimpleForm
    {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            $result = $data;
            if($result === null) {
                return;
            }
            if($result == 0){
                $this->resetSkin($player);
            }else{
                $this->deeperForm($player, "",$result -1);}
        }
        );
        $form->setTitle("§9[ §bCosmetic §9]");
        $form->setContent($txt);
        $i = 0;
        $form->addButton("§bReset your Skin");
        foreach ($this->cosmetiqueTypes as $value) {
            $form->addButton("§3".$value);
            if($i < 15){
                $i += 3;
            }else{
                $i = 0;
            }
        }
        $form->addButton("§9Close");
        $player->sendForm($form);
        return $form;
    }
    public function deeperForm(Player $player, string $txt,int $type): SimpleForm
    {
        $form = new SimpleForm(function(Player $player, int $data = null) use ($type){
            $result = $data;
            if($result === null) {
                return;
            }
            $cosmetiqueName = $this->cosmetiqueTypes[$type];
            if(!array_key_exists($result, $this->cosmetiqueDetails[$cosmetiqueName])) {
                $this->mainform($player, "");
                return;
            }
            $cosName = $this->cosmetiqueDetails[$cosmetiqueName][$result];
            $perms = $this->getConfig()->getNested('perms')[$cosName];
            if($player->hasPermission($perms) or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                $setskin = new setSkin();
                $setskin->setSkin($player, $cosName,$this->cosmetiqueTypes[$type]);
            }else {
                $player->sendMessage("§9You don't have permission to use this cosmetic.");
            }
        });
        $cosmetiqueName = $this->cosmetiqueTypes[$type];
        $form->setTitle("§9[ §bCosmetic §9]");
        if(!empty($this->cosmetiqueDetails[$cosmetiqueName])){
            foreach ($this->cosmetiqueDetails[$cosmetiqueName] as $value) {
                $perms = $this->getConfig()->get('perms')[$value];
                if($player->hasPermission($perms) or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                    $form->addButton($value . "\n§bAvailable", 0, "textures/ui/$value");
                }else {
                    $form->addButton($value . "\n§9Unavailable", 0, "textures/ui/$value");
                }
            }
            $form->setContent($txt);
        }
        $form->addButton("§9Close", 0, "textures/gui/newgui/undo");
        $player->sendForm($form);
        return $form;
    }

    /**
     * @throws JsonException
     */
    public function resetSkin(Player $player) {
        $player->sendMessage("§9Your Skin has been reset.");
        $reset = new resetSkin();
        $reset->setSkin($player);
    }

    public function onJoin(PlayerJoinEvent $e) {
        $name = $e->getPlayer()->getName();
        $skin = $e->getPlayer()->getSkin();
        $saveSkin = new saveSkin();
        $saveSkin->saveSkin($skin, $name);
    }
}
