<?php 

require 'StateManager.php';
require 'PaymentStateManager.php';
require 'Payment.php';

$payment = new Payment();

$paymentState = new PaymentStateManager($payment); // or $payment->getState();

echo 'current state: ' . $paymentState->getState() . PHP_EOL;

if ($paymentState->is('paid')) {
    echo 'payment is paid' . PHP_EOL;
}

if ($paymentState->is('not_paid')) {
    echo 'payment is not_paid' . PHP_EOL;
}

if ($paymentState->can('watch_by_customer')) {
    echo 'customer watching' . PHP_EOL;
    $paymentState->apply('watch_by_customer');
} else {
    echo 'cannot apply watch_by_customer' . PHP_EOL;
}

if ($paymentState->can('send_to_customer')) {
    echo 'sending to customer' . PHP_EOL;
    $paymentState->apply('send_to_customer');
    echo 'current state is now: ' . $paymentState->getState() . PHP_EOL;
}

if ($paymentState->can('watch_by_customer')) {
    echo 'customer now can watch' . PHP_EOL;
    $paymentState->apply('watch_by_customer');
}

if (!$paymentState->apply('watch_by_customer')) {
    echo 'by default apply operation works without exception' . PHP_EOL;
}

try {
    $paymentState->applyOrFail('watch_by_customer');
} catch (Exception $e) {
    echo 'but we can use applyOrFail to force fatal error' . PHP_EOL;
}

