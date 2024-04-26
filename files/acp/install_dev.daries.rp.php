<?php

use wcf\data\language\item\LanguageItemEditor;
use wcf\data\option\OptionEditor;
use wcf\data\package\PackageCache;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

// set default game
$sql = "INSERT INTO rp1_game
                    (identifier, title, packageID)
        VALUES      (?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    'dev.daries.rp.game.default',
    'rp.game.dev.daries.rp.game.default',
    PackageCache::getInstance()->getPackageByIdentifier('dev.daries.rp')->packageID,
]);
$gameID = WCF::getDB()->getInsertID('rp1_game', 'gameID');

$titles = [
    'de' => 'Standard',
    'en' => 'Default',
];

foreach ($titles as $languageCode => $value) {
    $language = LanguageFactory::getInstance()->getLanguageByCode($languageCode);
    $languageCategory = LanguageFactory::getInstance()->getCategory('rp.game');

    LanguageItemEditor::create([
        'languageID' => $language->languageID,
        'languageItem' => 'rp.game.dev.daries.rp.game.default',
        'languageItemValue' => $value,
        'languageCategoryID' => $languageCategory->languageCategoryID,
        'packageID' => PackageCache::getInstance()->getPackageByIdentifier('dev.daries.rp')->packageID,
    ]);
}

// set default game to option
$sql = "UPDATE  wcf1_option
        SET     optionValue = ?
        WHERE   optionName = ?";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    $gameID,
    'rp_default_game_id',
]);
OptionEditor::resetCache();
