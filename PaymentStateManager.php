<?php

final class PaymentStateManager extends StateManager
{
    private Payment $payment;

    protected array $states = [
        'not_paid',
        'sent_no_viewed',
        'viewed_no_paid',
        'paid',
    ];

    protected array $transitions = [
        'send_to_customer' => [
            'from' => ['not_paid', 'viewed_no_paid'],
            'to' => 'sent_no_viewed'
        ],
        'watch_by_customer' => [
            'from' => ['sent_no_viewed'],
            'to' => 'viewed_no_paid'
        ],
        'resend_to_customer' => [
            'from' => ['sent_no_viewed', 'viewed_no_paid'],
            'to' => 'sent_no_viewed'
        ],
        'pay_by_customer' => [
            'from' => ['viewed_no_paid'],
            'to' => 'paid'
        ],
        'set_paid_manually' => [
            'from' => ['not_paid', 'sent_no_viewed', 'viewed_no_paid'],
            'to' => 'paid'
        ],
        'cancel' => [
            'from' => ['paid'],
            'to' => 'not_paid'
        ]
    ];

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        parent::__construct();
    }

    protected function getGraphCode(): string
    {
        return 'crm_payment_state';
    }

    protected function getInitialState(): string
    {
        return 'not_paid';
    }

    protected function fetchCurrentState(): ?string
    {
        // select from db or get from $this->payment
        return $this->payment->state;
    }

    protected function persistCurrentState(): void
    {
        $this->payment->state = $this->getState();

        // create table b_crm_entity_state_history (
        // ID
        // ENTITY_ID
        // ENTITY_TYPE_ID
        // GRAPH_CODE
        // STATE
        // CREATED_AT
        // CREATED_BY
        //)
    }
}