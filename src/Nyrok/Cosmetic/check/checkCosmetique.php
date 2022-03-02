<?php

namespace Nyrok\Cosmetic\check;

use Nyrok\Cosmetic\Main;

class checkCosmetique {
    public function checkCosmetique(){
        $main = Main::$instance;
        $checkFileAvailable = [];
        $path = $main->getDataFolder();
        $allDirs = scandir($path);
        foreach ($allDirs as $foldersName) {
            if(is_dir($path.$foldersName)){
                array_push($main->cosmetiqueTypes,$foldersName);
                $allFiles = scandir($path.$foldersName);
                foreach ($allFiles as $allFilesName) {
                    if(strpos($allFilesName, ".json")) {
                      array_push($checkFileAvailable, str_replace('.json', '', $allFilesName));
                    }
                }
                foreach ($checkFileAvailable as $value) {
                    if(!in_array($value.".png", $allFiles)) {
                       unset($checkFileAvailable[array_search($value, $checkFileAvailable)]);
                    }
                }
                $main->cosmetiqueDetails[$foldersName] = $checkFileAvailable;
                sort($main->cosmetiqueDetails[$foldersName]);
                $checkFileAvailable = [];
            }
        }
        unset($main->cosmetiqueTypes[0]);
        unset($main->cosmetiqueTypes[1]);
        unset($main->cosmetiqueTypes[array_search("saveskin",$main->cosmetiqueTypes)]);
        unset($main->cosmetiqueDetails["."]);
        unset($main->cosmetiqueDetails[".."]);
        unset($main->cosmetiqueDetails["saveskin"]);
        sort($main->cosmetiqueTypes);
    }
}