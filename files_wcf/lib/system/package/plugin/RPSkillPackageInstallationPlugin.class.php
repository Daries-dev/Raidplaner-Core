<?php

namespace wcf\system\package\plugin;

use rp\data\game\GameCache;
use rp\data\skill\SkillEditor;
use rp\data\skill\SkillList;
use wcf\system\devtools\pip\IDevtoolsPipEntryList;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\devtools\pip\TXmlGuiPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\language\LanguageFactory;
use wcf\system\Regex;
use wcf\system\WCF;

/**
 * Installs, updates and deletes skills.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPSkillPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements
    IGuiPackageInstallationPlugin,
    IUniqueNameXMLPackageInstallationPlugin
{
    use TXmlGuiPackageInstallationPlugin;

    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = SkillEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'skill';

    /**
     * @inheritDoc
     */
    public $tagName = 'skill';

    /**
     * @inheritDoc
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
            TextFormField::create('identifier')
                ->label('wcf.acp.pip.rpSkill.identifier')
                ->description('wcf.acp.pip.rpSkill.identifier.description')
                ->required()
                ->addValidator(new FormFieldValidator('regex', function (TextFormField $formField) {
                    $regex = Regex::compile('^[A-z0-9\-\_]+$');

                    if (!$regex->match($formField->getSaveValue())) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'wcf.acp.pip.rpSkill.identifier.error.invalid'
                            )
                        );
                    }
                }))
                ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                    if (
                        $formField->getDocument()->getFormMode() === IFormDocument::FORM_MODE_CREATE
                        || $this->editedEntry->getAttribute('identifier') !== $formField->getValue()
                    ) {
                        /** @var SingleSelectionFormField $gameFormField */
                        $gameFormField = $formField->getDocument()->getNodeById('game');
                        $gameID = GameCache::getInstance()->getGameByIdentifier($gameFormField->getSaveValue())->gameID;

                        $skillList = new SkillList();
                        $skillList->getConditionBuilder()->add('identifier = ?', [$formField->getValue()]);

                        if ($skillList->countObjects() > 0) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'notUnique',
                                    'wcf.acp.pip.rpSkill.identifier.error.noUnique'
                                )
                            );
                        }
                    }
                })),

            TitleFormField::create()
                ->required()
                ->i18n()
                ->i18nRequired()
                ->languageItemPattern('__NONE__'),

            SingleSelectionFormField::create('game')
                ->label('wcf.acp.pip.rpSkill.game')
                ->description('wcf.acp.pip.rpSkill.game.description')
                ->required()
                ->options(static function () {
                    $options = [];
                    foreach (GameCache::getInstance()->getGames() as $game) {
                        $options[$game->identifier] = $game->getTitle();
                    }

                    \asort($options);

                    return $options;
                }),

            TextFormField::create('icon')
                ->label('wcf.acp.pip.rpSkill.icon')
                ->description('wcf.acp.pip.rpSkill.icon.description'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function fetchElementData(\DOMElement $element, $saveData): array
    {
        $data = [
            'identifier' => $element->getAttribute('identifier'),
            'packageID' => $this->installation->getPackageID(),
            'title' => [],
        ];

        /** @var \DOMElement $title */
        foreach ($element->getElementsByTagName('title') as $title) {
            $data['title'][LanguageFactory::getInstance()->getLanguageByCode($title->getAttribute('language'))->languageID] = $title->nodeValue;
        }

        foreach (['game', 'icon'] as $optionalElementName) {
            $optionalElement = $element->getElementsByTagName($optionalElementName)->item(0);
            if ($optionalElement !== null) {
                $data[$optionalElementName] = $optionalElement->nodeValue;
            } elseif ($saveData) {
                $data[$optionalElementName] = '';
            }
        }

        if ($saveData) {
            if (isset($data['game'])) {
                $data['gameID'] = GameCache::getInstance()->getGameByIdentifier($data['game'])?->gameID;
            }
            unset($data['game']);

            $titles = [];
            foreach ($data['title'] as $languageID => $title) {
                $titles[LanguageFactory::getInstance()->getLanguage($languageID)->languageCode] = $title;
            }

            $data['title'] = $titles;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_skill
                WHERE   identifier = ?
                    AND packageID = ?";
        $parameters = [
            $data['identifier'],
            $this->installation->getPackageID(),
        ];

        return [
            'sql' => $sql,
            'parameters' => $parameters,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpSkill.xml';
    }

    /**
     * @inheritDoc
     * @throws  SystemException
     */
    protected function getElement(\DOMXPath $xpath, array &$elements, \DOMElement $element): void
    {
        $nodeValue = $element->nodeValue;

        if ($element->tagName === 'title') {
            if (empty($element->getAttribute('language'))) {
                throw new SystemException("Missing required attribute 'language' for skill item '" . $element->parentNode->getAttribute('identifier') . "'");
            }

            // <title> can occur multiple items using the `language` attribute
            $elements['title'] ??= [];

            $elements['title'][$element->getAttribute('language')] = $element->nodeValue;
        } else {
            $elements[$element->tagName] = $nodeValue;
        }
    }

    /**
     * @inheritDoc
     */
    public function getElementIdentifier(\DOMElement $element): string
    {
        return $element->getAttribute('identifier');
    }

    /**
     * @inheritDoc
     */
    public function getNameByData(array $data): string
    {
        return $data['identifier'];
    }

    /**
     * @inheritDoc
     */
    public static function getSyncDependencies()
    {
        return ['language', 'rpGame'];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items)
    {
        $sql = "DELETE FROM rp1_skill
                WHERE       identifier = ?
                    AND     packageID = ?";
        $statement = WCF::getDB()->prepare($sql);

        $sql = "DELETE FROM wcf1_language_item
                WHERE       languageItem = ?";
        $languageItemStatement = WCF::getDB()->prepare($sql);

        WCF::getDB()->beginTransaction();
        foreach ($items as $item) {
            $statement->execute([
                $item['attributes']['identifier'],
                $this->installation->getPackageID(),
            ]);

            $languageItemStatement->execute([
                'rp.skill.' . $item['attributes']['identifier'],
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form): \DOMElement
    {
        $formData = $form->getData();
        $data = $formData['data'];

        $skill = $document->createElement($this->tagName);
        $skill->setAttribute('identifier', $data['identifier']);

        if (!empty($data['game'])) {
            $skill->appendChild($document->createElement('game', $data['game']));
        }

        foreach ($formData['title_i18n'] as $languageID => $title) {
            $title = $document->createElement('title', $this->getAutoCdataValue($title));
            $title->setAttribute('language', LanguageFactory::getInstance()->getLanguage($languageID)->languageCode);

            $skill->appendChild($title);
        }

        $this->appendElementChildren(
            $skill,
            [
                'icon' => '',
            ],
            $form
        );

        return $skill;
    }

    /**
     * @inheritDoc
     * @throws  SystemException
     */
    protected function prepareImport(array $data): array
    {
        $gameID = GameCache::getInstance()->getGameByIdentifier($data['elements']['game'] ?? '')?->gameID;

        if ($gameID === null) {
            throw new SystemException("The skill '" . $data['attributes']['identifier'] . "' must either have an associated game or unable to find game '" . $data['elements']['game'] . "'.");
        }

        return [
            'gameID' => $gameID,
            'icon' => $data['elements']['icon'] ?? '',
            'identifier' => $data['attributes']['identifier'],
            'title' => $this->getI18nValues($data['elements']['title']),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setEntryListKeys(IDevtoolsPipEntryList $entryList): void
    {
        $entryList->setKeys([
            'identifier' => 'wcf.acp.pip.rpSkill.identifier',
            'game' => 'wcf.acp.pip.rpSkill.game',
        ]);
    }
}
