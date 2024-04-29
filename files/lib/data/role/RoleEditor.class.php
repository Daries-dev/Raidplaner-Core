<?php

namespace rp\data\role;

use rp\system\cache\builder\RoleCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\data\language\category\LanguageCategory;
use wcf\data\language\LanguageList;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides functions to edit role.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 * 
 * @method  Role        getDecoratedObject()
 * @mixin   Role
 */
class RoleEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Role::class;

    /**
     * @inheritDoc
     */
    public static function create(array $parameters = []): Role
    {
        $titles = '';
        if (\is_array($parameters['title'])) {
            $titles = $parameters['title'];
            $parameters['title'] = '';
        }

        /** @var Role $role */
        $role = parent::create($parameters);

        // save role title
        if (!empty($titles)) {
            $roleEditor = new self($role);
            $roleEditor->saveTitles($titles);
            $role = new static::$baseClass($role->roleID);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $role;
    }

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RoleCacheBuilder::getInstance()->reset();
    }

    /**
     * Saves the titles of the role in language items.
     */
    protected function saveTitles(array $titles): void
    {
        // set default value
        if (isset($titles[''])) {
            $defaultValue = $titles[''];
        } elseif (isset($titles['en'])) {
            // fallback to English
            $defaultValue = $titles['en'];
        } elseif (isset($titles[WCF::getLanguage()->getFixedLanguageCode()])) {
            // fallback to the language of the current user
            $defaultValue = $titles[WCF::getLanguage()->getFixedLanguageCode()];
        } else {
            // fallback to first title
            $defaultValue = \reset($titles);
        }

        // fetch data directly from database during framework installation
        if (!PACKAGE_ID) {
            $sql = "SELECT  *
                    FROM    wcf1_language_category
                    WHERE   languageCategory = ?";
            $statement = WCF::getDB()->prepare($sql);
            $languageCategory = $statement->fetchObject(LanguageCategory::class);

            $languages = new LanguageList();
            $languages->readObjects();
        } else {
            $languages = LanguageFactory::getInstance()->getLanguages();
            $languageCategory = LanguageFactory::getInstance()->getCategory('rp.role');
        }

        // save new titles
        $sql = "INSERT INTO             wcf1_language_item
                                        (languageID, languageItem, languageItemValue, languageCategoryID, packageID)
                VALUES                  (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE languageItemValue = VALUES(languageItemValue),
                                        languageCategoryID = VALUES(languageCategoryID)";
        $statement = WCF::getDB()->prepare($sql);

        foreach ($languages as $language) {
            $value = $titles[$language->languageCode] ?? $defaultValue;

            $statement->execute([
                $language->languageID,
                'rp.role.' . $this->identifier,
                $value,
                $languageCategory->languageCategoryID,
                $this->packageID,
            ]);
        }

        // update role
        $this->update(['title' => 'rp.role.' . $this->identifier]);
    }

    /**
     * @inheritDoc
     */
    public function update(array $parameters = []): void
    {
        $titles = [];
        if (isset($parameters['title']) && \is_array($parameters['title'])) {
            if (\count($parameters['title']) > 1) {
                $titles = $parameters['title'];
                $parameters['title'] = '';
            } else {
                $parameters['title'] = \reset($parameters['title']);
            }
        }

        parent::update($parameters);

        // save role title
        if (!empty($titles)) {
            $this->saveTitles($titles);
        }
    }
}
