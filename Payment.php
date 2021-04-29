<?php 

class Payment
{
    public ?string $state = null;

    public function getState(): PaymentStateManager
    {
        return new PaymentStateManager($this);
    }
}