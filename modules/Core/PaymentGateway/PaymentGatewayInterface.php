<?php

namespace TreXanhProperty\Core\PaymentGateway;

interface PaymentGatewayInterface
{
    public function get_setting_fields();
    public function process_payment( $order_id );
    public function is_enabled();
}
