<?php

namespace rp\acp\form;

use rp\data\character\Character;
use rp\data\character\CharacterAction;
use rp\system\character\event\CharacterAddCreateForm;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\event\EventHandler;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\container\TabTabMenuFormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\user\UserFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * Shows the character add form.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
class CharacterAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.character.add';

    /**
     * ids of the fields containing object data
     * @var string[]
     */
    public array $characterFields = [
        'characterName',
        'guildName',
        'notes',
        'userID',
    ];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = CharacterAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('characterTabMenu');
        $this->form->appendChild($tabMenu);

        // data tab
        $dataTab = TabFormContainer::create('dataTab');
        $dataTab->label('wcf.global.form.data');
        $tabMenu->appendChild($dataTab);

        $dataContainer = FormContainer::create('data')
            ->appendChildren([
                TextFormField::create('characterName')
                    ->label('rp.character.characterName')
                    ->required()
                    ->autoFocus()
                    ->maximumLength(100)
                    ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                        $value = $formField->getSaveValue();

                        if (
                            $this->formAction === IFormDocument::FORM_MODE_CREATE
                            || ($value !== $this->formObject->characterName)
                        ) {

                            $character = Character::getCharacterByCharacterName($value);
                            if ($character->characterID) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'notUnique',
                                        'rp.character.characterName.error.notUnique'
                                    )
                                );
                            }
                        }
                    })),
                UserFormField::create('userID')
                    ->label('wcf.user.username')
                    ->available(RequestHandler::getInstance()->isACPRequest()),
                TextFormField::create('guildName')
                    ->label('rp.character.guildName')
                    ->maximumLength(100),
                MultilineTextFormField::create('notes')
                    ->label('rp.character.notes'),
            ]);
        $dataTab->appendChild($dataContainer);

        // character tab
        $characterTab = TabTabMenuFormContainer::create('characterTab');
        $characterTab->label('rp.character.category.character');
        $tabMenu->appendChild($characterTab);

        $characterGeneralTab = TabFormContainer::create('characterGeneralTab')
            ->label('rp.character.category.character')
            ->appendChild(FormContainer::create('characterGeneralSection'));
        $characterTab->appendChild($characterGeneralTab);

        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'customs',
                static function (IFormDocument $document, array $parameters) {
                    if (!RequestHandler::getInstance()->isACPRequest()) {
                        $parameters['data']['userID'] = WCF::getUser()->userID;
                    }

                    $parameters['data']['userID'] ??= null;

                    return $parameters;
                }
            )
        );

        EventHandler::getInstance()->fire(
            new CharacterAddCreateForm($this->form)
        );
    }

    /**
     * @inheritDoc
     */
    public function save(): void
    {
        AbstractForm::save();

        $action = $this->formAction;
        if ($this->objectActionName) {
            $action = $this->objectActionName;
        } elseif ($this->formAction === 'edit') {
            $action = IFormDocument::FORM_MODE_UPDATE;
        }

        $formData = $this->form->getData();
        $formData['data'] ??= [];

        $formData['data'] = [
            ...$this->additionalFields,
            ...$formData['data'],
        ];

        $characterData = [
            'gameID' => RP_CURRENT_GAME_ID,
        ];
        foreach ($this->characterFields as $field) {
            if (isset($formData['data'][$field])) {
                $characterData[$field] = $formData['data'][$field];
                unset($formData['data'][$field]);
            }
        }

        $characterData['additionalData'] = @\serialize($formData['data']);
        unset($formData['data']);

        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $action,
            [
                ...$formData,
                'data' => $characterData,
            ]
        );
        $this->objectAction->executeAction();

        $this->saved();

        WCF::getTPL()->assign('success', true);

        if ($this->formAction === 'create') {
            WCF::getTPL()->assign(
                'objectEditLink',
                LinkHandler::getInstance()->getControllerLink(CharacterEditForm::class, [
                    'application' => 'rp',
                    'id' => $this->objectAction->getReturnValues()['returnValues']->getObjectID(),
                ])
            );
        }
    }
}
