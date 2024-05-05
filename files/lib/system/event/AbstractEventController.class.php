<?php

namespace rp\system\event;

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\event\EventHandler;
use wcf\system\form\builder\container\IFormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\dependency\NonEmptyFormFieldDependency;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * Default implementation for event controllers.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
abstract class AbstractEventController implements IEventController
{
    /**
     * type name of this event controller
     */
    protected string $eventController = '';

    /**
     * ids of the fields containing object data
     */
    protected array $savedFields = [];

    /**
     * @inheritDoc
     */
    public function createForm(IFormDocument $form): void
    {
        $event = new EventCreateForm($form, $this->eventController);
        EventHandler::getInstance()->fire($event);
    }

    /**
     * Adds a Boolean form field for enabling comments.
     */
    protected function formComment(IFormContainer $container): void
    {
        $container->appendChild(
            BooleanFormField::create('enableComments')
                ->label('rp.event.enableComments')
                ->value(1)
        );
    }

    /**
     * Adds an event date to the container.
     */
    protected function formEventDate(IFormContainer $container, bool $supportFullDay = false): void
    {
        $isFullDay = BooleanFormField::create('isFullDay')
            ->label('rp.event.isFullDay')
            ->value(0)
            ->available($supportFullDay);

        $container->appendChildren([
            $isFullDay,
            DateFormField::create('startTime')
                ->label('rp.event.startTime')
                ->required()
                ->supportTime()
                ->value(TIME_NOW)
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    $value = $formField->getSaveValue();
                    if ($value === null || $value < -2147483647 || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.startTime.error.invalid'
                            )
                        );
                    }
                })),
            DateFormField::create('endTime')
                ->label('rp.event.endTime')
                ->required()
                ->supportTime()
                ->value(TIME_NOW + 7200) // +2h
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('startTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value === null || $value < $startValue || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.endTime.error.invalid'
                            )
                        );
                    }
                }))
                ->addValidator(new FormFieldValidator('long', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('startTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value - $startValue > RP_CALENDAR_MAX_EVENT_LENGTH * 86400) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'tooLong',
                                'rp.event.endTime.error.tooLong'
                            )
                        );
                    }
                })),
            DateFormField::create('fStartTime')
                ->label('rp.event.startTime')
                ->required()
                ->value(TIME_NOW)
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    $value = $formField->getSaveValue();
                    if ($value === null || $value < -2147483647 || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.startTime.error.invalid'
                            )
                        );
                    }
                }))
                ->addDependency(
                    NonEmptyFormFieldDependency::create('isFullDay')
                        ->field($isFullDay)
                ),
            DateFormField::create('fEndTime')
                ->label('rp.event.endTime')
                ->required()
                ->value(TIME_NOW + 7200) // +2h
                ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('fStartTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value === null || $value < $startValue || $value > 2147483647) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'invalid',
                                'rp.event.endTime.error.invalid'
                            )
                        );
                    }
                }))
                ->addValidator(new FormFieldValidator('long', function (DateFormField $formField) {
                    /** @var DateFormField $startFormField */
                    $startFormField = $formField->getDocument()->getNodeById('fStartTime');
                    $startValue = $startFormField->getSaveValue();

                    $value = $formField->getSaveValue();

                    if ($value - $startValue > RP_CALENDAR_MAX_EVENT_LENGTH * 86400) {
                        $formField->addValidationError(
                            new FormFieldValidationError(
                                'tooLong',
                                'rp.event.endTime.error.tooLong'
                            )
                        );
                    }
                }))
                ->addDependency(
                    NonEmptyFormFieldDependency::create('isFullDay')
                        ->field($isFullDay)
                ),
        ]);

        $form = $container->getDocument();

        if ($supportFullDay) {
            foreach (['startTime', 'endTime'] as $id) {
                $formField = $form->getNodeById($id);
                $formField?->addDependency(
                    EmptyFormFieldDependency::create('isFullDay')
                        ->field($isFullDay)
                );
            }
        }

        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('startTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('endTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fStartTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fEndTime'));

        $form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'eventDate',
                static function (IFormDocument $document, array $parameters) {
                    /** @var BooleanFormField $fullDay */
                    $fullDay = $document->getNodeById('isFullDay');
                    /** @var DateFormField $startTime */
                    $startTime = $document->getNodeById($fullDay->getSaveValue() ? 'fStartTime' : 'startTime');
                    /** @var DateFormField $endTime */
                    $endTime = $document->getNodeById($fullDay->getSaveValue() ? 'fEndTime' : 'endTime');

                    $st = $et = null;

                    if ($fullDay->getSaveValue()) {
                        $st = \DateTimeImmutable::createFromFormat(
                            DateFormField::DATE_FORMAT,
                            $startTime->getValue(),
                            new \DateTimeZone('UTC')
                        );
                        $st->setTime(0, 0);

                        $et = \DateTimeImmutable::createFromFormat(
                            DateFormField::DATE_FORMAT,
                            $endTime->getValue(),
                            new \DateTimeZone('UTC')
                        );
                        $et->setTime(23, 59);
                    } else {
                        $st = \DateTimeImmutable::createFromFormat(
                            DateFormField::TIME_FORMAT,
                            $startTime->getValue(),
                            WCF::getUser()->getTimeZone()
                        );

                        $et = \DateTimeImmutable::createFromFormat(
                            DateFormField::TIME_FORMAT,
                            $endTime->getValue(),
                            WCF::getUser()->getTimeZone()
                        );
                    }

                    $parameters['data']['startTime'] = $st->getTimestamp();
                    $parameters['data']['endTime'] = $et->getTimestamp();
                    $parameters['data']['timezone'] = $fullDay->getSaveValue() ? 'UTC' : WCF::getUser()->getTimeZone()->getName();

                    return $parameters;
                }
            )
        );
    }

    /**
     * Adds a wysiwyg form field for notes.
     */
    protected function formNotes(IFormContainer $container): void
    {
        $container->appendChild(
            WysiwygFormField::create('notes')
                ->label('rp.event.notes')
                ->objectType('dev.daries.rp.event.notes')
        );
    }

    /**
     * @inheritDoc
     */
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('user.rp.canCreateEvent');
    }

    /**
     * @inheritDoc
     */
    public function saveForm(array $formData): array
    {
        if (empty($this->savedFields)) return $formData;

        $data = [];
        $data = \array_intersect_key($formData['data'], \array_flip($this->savedFields)) + $data;
        $formData['data'] = \array_diff_key($formData['data'], \array_flip($this->savedFields));

        $data['objectTypeID'] = (ObjectTypeCache::getInstance()->getObjectTypeByName('dev.daries.rp.event.controller', $this->eventController))->objectTypeID;

        $data['additionalData'] = \serialize($formData['data']);
        unset($formData['data']);

        return \array_merge(
            $formData,
            ['data' => $data]
        );
    }
}
