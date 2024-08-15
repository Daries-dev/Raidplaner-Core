<?php

namespace rp\system\form\builder\field;

use wcf\system\form\builder\field\SingleSelectionFormField;

/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class DynamicSelectFormField extends SingleSelectionFormField
{
    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = 'shared_dynamicSelectFormField';

    protected ?array $optionsMapping = null;

    protected ?string $triggerSelect = null;

    public function getOptionsMapping(): array
    {
        if ($this->optionsMapping === null) {
            throw new \BadMethodCallException("No options mapping have been set for field '{$this->getId()}'.");
        }

        return $this->optionsMapping;
    }

    public function getTriggerSelect(): string
    {
        if ($this->triggerSelect === null) {
            throw new \LogicException("\$triggerSelect property has not been set for class '" . static::class . "'.");
        }

        return $this->triggerSelect;
    }

    public function optionsMapping(array|callable $optionsMapping): self
    {
        if (!\is_array($optionsMapping) && !\is_callable($optionsMapping)) {
            throw new \InvalidArgumentException(
                "The given options mapping are neither iterable nor a callable, " . \gettype($optionsMapping) . " given for field '{$this->getId()}'."
            );
        }

        if (\is_callable($optionsMapping)) {
            $optionsMapping = $optionsMapping();

            if (!\is_array($optionsMapping)) {
                throw new \UnexpectedValueException(
                    "The options mapping callable is expected to return an iterable value, " . \gettype($options) . " returned for field '{$this->getId()}'."
                );
            }

            return $this->optionsMapping($optionsMapping);
        }

        $this->optionsMapping = [];
        foreach ($optionsMapping as $key => $values) {
            if (!\is_array($values)) {
                throw new \InvalidArgumentException(
                    "Options mapping must not contain any array. Array given for key '{$key}' for field '{$this->getId()}'."
                );
            }

            foreach ($values as $value) {
                if (!\is_numeric($value)) {
                    throw new \InvalidArgumentException(
                        "Options mapping values contain invalid values of type " . \gettype($value) . " for field '{$this->getId()}'."
                    );
                }
            }

            if (isset($this->optionsMapping[$key])) {
                throw new \InvalidArgumentException(
                    "Options mapping values must be unique, but '{$key}' appears at least twice as value for field '{$this->getId()}'."
                );
            }

            $this->optionsMapping[$key] = $values;
        }

        return $this;
    }

    public function triggerSelect(string $select): self
    {
        $this->triggerSelect = $select;

        return $this;
    }
}
