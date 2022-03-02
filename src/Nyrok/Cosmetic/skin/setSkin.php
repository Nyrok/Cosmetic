<?php

namespace Nyrok\Cosmetic\skin;

use GdImage;
use JsonException;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use Nyrok\Cosmetic\Main;

class setSkin {
    /**
     * @throws JsonException
     */
    public function setSkin(Player $player, string $stuffName, string $locate) {
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = Main::$instance->getDataFolder()."saveskin/".$name.".txt";
        if(filesize($path) == 65536){
            $path = $this->imgTricky($name, $stuffName, $locate,128);
            $size = 128;
        }else{
            $size = 64;
            $path = $this->imgTricky($name, $stuffName, $locate,64);
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
                $skinbytes .= chr($r).chr($g).chr($b).chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.".$locate, file_get_contents(Main::$instance->getDataFolder().$locate."/".$stuffName.".json")));
        $player->sendSkin();
    }
    public function imgTricky(string $name,string $stuffName,string $locate,$size): string
    {
        $path = Main::$instance->getDataFolder();

        $down = imagecreatefrompng($path."saveskin/".$name.".png");
        if($size == 128){
            if(file_exists($path.$locate."/".$stuffName."_".$size.".png")){
               $upper = imagecreatefrompng($path.$locate."/".$stuffName."_".$size.".png");
            }else{
               $upper = $this->resize_image($path.$locate."/".$stuffName.".png",128,128);
            }
        }else{
            $upper = imagecreatefrompng($path.$locate."/".$stuffName.".png");
        }
        //Remove black color out of the png
        imagecolortransparent($upper, imagecolorallocatealpha($upper, 0, 0, 0, 127));
        
        imagealphablending($down, true);
        imagesavealpha($down, true);
        
        imagecopymerge($down,$upper, 0, 0, 0, 0,$size,$size,100);
             
        imagepng($down, $path.'do_not_delete.png');
        return Main::$instance->getDataFolder().'do_not_delete.png';
    }

    public function resize_image($file, $w, $h, $crop=FALSE): GdImage|bool
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    
        return $dst;
    }
}