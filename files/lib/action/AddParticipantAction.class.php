<?php

namespace rp\action;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use rp\data\classification\ClassificationCache;
use rp\data\event\Event;
use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use rp\data\role\RoleCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\character\AvailableCharacter;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\field\EmailFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\WCF;

/**
 * Handles add participant
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AddParticipantAction implements RequestHandlerInterface
{
    private ?Event $event = null;

    private function assertEventCanBeAdd(): void
    {
        if (!$this->event) {
            throw new IllegalLinkException();
        }

        if (!WCF::getSession()->getPermission('user.rp.canParticipate')) {
            throw new PermissionDeniedException();
        }

        if ($this->event->getController()->isExpired()) {
            throw new UserInputException('isExpired');
        }

        if (WCF::getUser()->userID) {
            if (!count($this->event->getController()->getContentData('availableCharacters'))) {
                throw new UserInputException('availableCharacters');
            }

            if ($this->event->getController()->getContentData('hasAttendee')) {
                throw new UserInputException('hasAttendee');
            }
        }
    }

    private function getForm(): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            static::class,
            WCF::getLanguage()->get('rp.event.raid.attendee.add')
        );

        if (WCF::getUser()->userID) {
            $availableCharacters = [
                '' => WCF::getLanguage()->get('wcf.global.noSelection'),
                ...$this->event->getController()->getContentData('availableCharacters'),
            ];

            $form->appendChildren([
                SingleSelectionFormField::create('characterID')
                    ->label('rp.event.raid.attendee.character')
                    ->required()
                    ->options($availableCharacters)
                    ->addValidator(new FormFieldValidator('notExists', function (SingleSelectionFormField $formField) {
                        $availableCharacters = $this->event->getController()->getContentData('availableCharacters');
                        $value = $formField->getSaveValue();

                        if (!isset($availableCharacters[$value])) {
                            $formField->addValidationError(
                                new FormFieldValidationError('empty')
                            );
                        }
                    })),
                SingleSelectionFormField::create('status')
                    ->label('rp.event.raid.status')
                    ->required()
                    ->options($this->event->getController()->getContentData('raidStatus')),
            ]);
        } else {
            $form->appendChildren([
                TextFormField::create('characterName')
                    ->label('rp.event.raid.attendee.character')
                    ->required()
                    ->maximumLength(255),
                EmailFormField::create('email')
                    ->label('rp.event.raid.attendee.email')
                    ->required(),
                SingleSelectionFormField::create('roleID')
                    ->label('rp.role.title')
                    ->required()
                    ->options(['' => 'wcf.global.noSelection'] + RoleCache::getInstance()->getRoles())
                    ->addValidator(new FormFieldValidator('uniqueness', function (SingleSelectionFormField $formField) {
                        $value = $formField->getSaveValue();

                        if (empty($value)) {
                            $formField->addValidationError(
                                new FormFieldValidationError('empty')
                            );
                        } else {
                            $role = RoleCache::getInstance()->getRoleByID($value);
                            if ($role === null) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'invalid',
                                        'rp.role.error.invalid'
                                    )
                                );
                            }
                        }
                    })),
                SingleSelectionFormField::create('classificationID')
                    ->label('rp.classification.title')
                    ->required()
                    ->options(['' => 'wcf.global.noSelection'] + ClassificationCache::getInstance()->getClassifications())
                    ->addValidator(new FormFieldValidator('uniqueness', function (SingleSelectionFormField $formField) {
                        $value = $formField->getSaveValue();

                        if (empty($value)) {
                            $formField->addValidationError(
                                new FormFieldValidationError('empty')
                            );
                        } else {
                            $classification = ClassificationCache::getInstance()->getClassificationByID($value);
                            if ($classification === null) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'invalid',
                                        'rp.classification.error.invalid'
                                    )
                                );
                            }
                        }
                    })),
            ]);
        }

        $form->appendChild(
            TextFormField::create('notes')
                ->label('rp.event.raid.attendee.notes')
                ->maximumLength(255)
        );

        $form->build();

        return $form;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parameters = Helper::mapQueryParameters(
            $request->getQueryParams(),
            <<<'EOT'
                array {
                    id: positive-int
                }
                EOT
        );

        $this->event = EventRuntimeCache::getInstance()->getObject($parameters['id']);
        $this->assertEventCanBeAdd();

        $form = $this->getForm();

        if ($request->getMethod() === 'GET') {
            return $form->toResponse();
        } elseif ($request->getMethod() === 'POST') {
            $response = $form->validateRequest($request);
            if ($response !== null) {
                return $response;
            }

            $formData = $form->getData()['data'];
            $attendeeData = [];
            if (WCF::getUser()->userID) {
                $availableCharacters = $this->event->getController()->getContentData('availableCharacters');

                /** @var AvailableCharacter $availableCharacter */
                $availableCharacter = $availableCharacters[$formData['characterID']];

                [$characterID] = \explode('_', $availableCharacter->getID(), 2);
                $character = CharacterRuntimeCache::getInstance()->getObject($characterID);

                $attendeeData = [
                    'characterID' => $character->characterID,
                    'characterName' => $character->characterName,
                    'classificationID' => $availableCharacter->getClassificationID(),
                    'internID' => $availableCharacter->getID(),
                    'roleID' => $availableCharacter->getRoleID(),
                    'status' => $formData['status'],
                ];
            } else {
                $attendeeData = [
                    'characterName' => $formData['characterName'],
                    'email' => $formData['email'],
                    'classificationID' => $formData['classificationID'],
                    'roleID' => $formData['roleID'],
                    'status' => EventRaidAttendee::STATUS_LOGIN,
                ];
            }

            $attendeeData['eventID'] = $this->event->eventID;
            $attendeeData['notes'] = $formData['notes'];

            $attendee = (new EventRaidAttendeeAction([], 'create', ['data' => $attendeeData]))->executeAction()['returnValues'];

            $distributionID = 0;
            switch ($this->event->distributionMode) {
                case 'class':
                    $distributionID = $attendee->classificationID;
                    break;
                case 'role':
                    $distributionID = $attendee->roleID;
                    break;
            }

            return new JsonResponse([
                'result' => [
                    'attendeeId' => $attendee->attendeeID,
                    'distributionId' => $distributionID,
                    'status' => $attendee->status,
                ],
            ]);
        } else {
            throw new \LogicException('Unreachable');
        }
    }
}
