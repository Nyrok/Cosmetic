<?php

namespace Nyrok\Cosmetic\skin;

use JsonException;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use Nyrok\Cosmetic\Main;

class resetSkin {


    /**
     * @throws JsonException
     */
    public function setSkin(Player $player) {
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = Main::$instance->getDataFolder()."saveskin/".$name.".png";
        $path2 = Main::$instance->getDataFolder()."saveskin/".$name.".txt";
        if(filesize($path2) == 65536){
            $size = 128;
        }else {
            $size = 64;
        }
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];

        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < $size; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~($colorat >> 24)) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.humanoid.custom",file_get_contents(Main::$instance->getDataFolder(). "steve.json")));
        $player->sendSkin();
    }
}