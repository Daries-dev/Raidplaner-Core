<?php

namespace rp\data\faction;

use rp\system\cache\builder\FactionCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides functions to edit faction.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @method  Faction     getDecoratedObject()
 * @mixin   Faction
 */
class FactionEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Faction::class;

    /**
     * @inheritDoc
     */
    public static function create(array $parameters = []): Faction
    {
        $title = '';
        if (\is_array($parameters['title'])) {
            $title = $parameters['title'];
            $parameters['title'] = '';
        }

        /** @var Faction $faction */
        $faction = parent::create($parameters);

        if (\is_array($title)) {
            if (\count($title) > 1) {
                $sql = "SELECT  languageCategoryID
                        FROM    wcf1_language_category
                        WHERE   languageCategory = ?";
                $statement = WCF::getDB()->prepare($sql, 1);
                $statement->execute(['rp.faction']);
                $languageCategoryID = $statement->fetchSingleColumn();

                $sql = "INSERT INTO wcf1_language_item
                                    (languageID, languageItem, languageItemValue, languageItemOriginIsSystem, languageCategoryID, packageID)
                        VALUES      (?, ?, ?, ?, ?, ?)";
                $statement = WCF::getDB()->prepare($sql);

                WCF::getDB()->beginTransaction();
                foreach ($title as $languageCode => $value) {
                    $statement->execute([
                        LanguageFactory::getInstance()->getLanguageByCode($languageCode)->languageID,
                        'rp.faction.' . $faction->identifier,
                        $value,
                        1,
                        $languageCategoryID,
                        $faction->packageID,
                    ]);
                }
                WCF::getDB()->commitTransaction();

                $title = 'rp.faction.' . $faction->identifier;
            } else {
                $title = \reset($title);
            }

            $factionEditor = new self($faction);
            $factionEditor->update(['title' => $title]);
            $faction = new static::$baseClass($faction->factionID);
        }

        return $faction;
    }

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        FactionCacheBuilder::getInstance()->reset();
    }
}
