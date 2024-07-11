<?php

namespace byteShard\Internal\Form\FormObject;

class FormAlterations
{
    private array  $events          = [];
    private string $helpObject      = '';
    private array  $clientExecution = [];
    private bool   $setOptions      = false;
    private string $name            = '';
    private array  $properties      = [];


    public function getSelectedClientOption(): ?string
    {
        return $this->selectedClientOption;
    }

    public function __construct(private readonly array $parameters, private readonly ?string $selectedClientOption = null)
    {

    }

    public function getParameters(): array
    {
        return $this->parameters;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function getEvents(): array
    {
        return $this->events;
    }

    public function addEvent(string $event): void
    {
        $this->events[$event] = $event;
    }

    public function getHelpObject(): string
    {
        return $this->helpObject;
    }

    public function setHelpObject(string $helpObject): void
    {
        $this->helpObject = $helpObject;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * example: button with onClick executes javascript method hideFormObject on a list of objects
     * @param string $eventName
     * @param string $formObjectId
     * @param string $method
     * @param array<string> $targetObjects
     * @return void
     */
    public function addClientExecution(string $eventName, string $formObjectId, string $method, array $targetObjects): void
    {
        foreach ($targetObjects as $targetObject) {
            $this->clientExecution[$eventName][$formObjectId][$method][] = $targetObject;
        }
    }

    public function getArrayOfMethodsWhichWillBeExecutedOnTheClient(): array
    {
        return $this->clientExecution;
    }

    public function isSetOptions(): bool
    {
        return $this->setOptions;
    }

    public function setOptions(bool $setOptions): void
    {
        $this->setOptions = $setOptions;
    }
}