<?php

namespace rp\system\form\builder\field\raid\item;

use rp\data\character\Character;
use rp\data\character\CharacterList;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\data\IStorableObject;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\TDefaultIdFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * Implementation of a form field for raid item.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class RaidItemFormField extends AbstractFormField
{
    use TDefaultIdFormField;

    /**
     * character objects
     * @var Character[]
     */
    protected ?array $characters = null;

    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = 'shared_raidItemsFormField';

    /**
     * @inheritDoc
     */
    protected $value = [];

    public function getCharacters(): array
    {
        if ($this->characters === null) {
            $this->characters = [];

            $list = new CharacterList();
            $list->getConditionBuilder()->add('isDisabled = ?', [0]);
            $list->sqlOrderBy = 'characterName ASC';
            $list->readObjects();
            foreach ($list as $character) {
                $this->characters[$character->getObjectID()] = $character;
            }
        }

        return $this->characters;
    }

    /**
     * @inheritDoc
     */
    protected static function getDefaultId(): string
    {
        return 'raidItems';
    }

    /**
     * Returns all point accounts.
     */
    public function getPointAccounts(): array
    {
        return PointAccountCache::getInstance()->getAccounts();
    }

    /**
     * @inheritDoc
     */
    public function hasSaveValue(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function populate(): self
    {
        parent::populate();

        $this->getDocument()->getDataHandler()->addProcessor(new CustomFormDataProcessor(
            'raidItems',
            function (IFormDocument $document, array $parameters) {
                if ($this->checkDependencies() && $this->getValue() !== null && !empty($this->getValue())) {
                    $parameters[$this->getObjectProperty()] = $this->getValue();
                }

                return $parameters;
            }
        ));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readValue(): void
    {
        if ($this->getDocument()->hasRequestData($this->getPrefixedId()) && \is_array($this->getDocument()->getRequestData($this->getPrefixedId()))) {
            $this->value = $this->getDocument()->getRequestData($this->getPrefixedId());
        } else {
            $this->value = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function updatedObject(array $data, IStorableObject $object, $loadValues = true): self
    {
        if ($loadValues) {
            $sql = "SELECT      item_to_raid.characterID, item_to_raid.itemID, item.itemName, item_to_raid.pointAccountID, item_to_raid.points
                    FROM        rp1_item_to_raid item_to_raid
                    LEFT JOIN   rp1_item item
                    ON          (item_to_raid.itemID = item.itemID)
                    WHERE       item_to_raid.raidID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$object->getObjectID()]);

            $items = [];
            while ($row = $statement->fetchArray()) {
                $character = CharacterRuntimeCache::getInstance()->getObject($row['characterID']);
                $pointAccount = PointAccountCache::getInstance()->getAccountByID($row['pointAccountID']);

                $items[] = [
                    'characterID' => $character->getObjectID(),
                    'characterName' => $character->getTitle(),
                    'itemID' => $row['itemID'],
                    'itemName' => $row['itemName'],
                    'pointAccountID' => $pointAccount->getObjectID(),
                    'pointAccountName' => $pointAccount->getTitle(),
                    'points' => $row['points']
                ];
            }

            $this->value($items);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        if ($this->isRequired() && ($this->getValue() === null || empty($this->getValue()))) {
            $this->addValidationError(new FormFieldValidationError('empty'));
        } else {
            $items = [];
            foreach ($this->getValue() as $item) {
                if (!\is_array($item)) continue;
                foreach (['characterID', 'itemID', 'pointAccountID', 'points'] as $key) {
                    if (!\array_key_exists($key, $item)) continue 2;
                }
                $items[] = $item;
            }

            $this->value($items);
        }

        parent::validate();
    }

    /**
     * @inheritDoc
     */
    public function value($value): self
    {
        if (!\is_array($value)) $value = [];
        return parent::value($value);
    }
}
