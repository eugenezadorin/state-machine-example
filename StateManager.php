<?php 

abstract class StateManager
{
    protected array $states = [];

    protected array $transitions = [];

    protected string $currentState = '';

    protected abstract function getInitialState(): string;

    protected abstract function fetchCurrentState(): ?string;

    protected abstract function persistCurrentState(): void;

    protected abstract function getGraphCode(): string;

    public function __construct()
    {
        $state = $this->fetchCurrentState();
        if ($state === null) {
            $this->currentState = $this->getInitialState();
        } else {
            $this->currentState = $state;
        }
        if (!in_array($this->currentState, $this->states)) {
            throw new Exception("Invalid state {$this->currentState}");
        }
    }

    public function is(string $currentState): bool
    {
        return $currentState === $this->currentState;
    }

    public function can(string $transitionName): bool
    {
        if (!isset($this->transitions[$transitionName])) {
            return false;
        }

        $transition = $this->transitions[$transitionName];
        if (!isset($transition['from']) || !is_array($transition['from'])) {
            return false;
        }

        if (!isset($transition['to']) || !in_array($transition['to'], $this->states)) {
            return false;
        }
        
        if (in_array($this->currentState, $transition['from'])) {
            return true;
        }

        return false;
    }

    public function applyOrFail(string $transitionName): void
    {
        if ($this->can($transitionName)) {
            $this->currentState = $this->transitions[$transitionName]['to'];
            $this->persistCurrentState();
        } else {
            throw new Exception("Cannot apply $transitionName transition to payment");
        }
    }

    public function apply(string $transition): bool
    {
        try {
            $this->applyOrFail($transition);
            return true;
        } catch (Exception $e) {}
        return false;
    }

    public function getState(): string
    {
        return $this->currentState;
    }
}
