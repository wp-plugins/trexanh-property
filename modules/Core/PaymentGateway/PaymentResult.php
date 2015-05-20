<?php

namespace TreXanhProperty\Core\PaymentGateway;

class PaymentResult
{
    const SUCCESS = 1;
    const REDIRECT = 2;
    const ERROR = 0;
    
    protected $code;
    protected $message;
    protected $transaction_id;
    protected $redirect;


    /**
     * 
     * @param int $code
     * @param string $message
     * @param string $transaction_id
     */
    public function __construct($code, $message = '', $transaction_id = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->transaction_id = $transaction_id;
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_success()
    {
        return $this->code == static::SUCCESS;
    }
    
    /**
     * 
     * @return string
     */
    public function get_transaction_id()
    {
        return $this->transaction_id;
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_error()
    {
        return $this->code == static::ERROR;
    }
    
    /**
     * 
     * @return string
     */
    public function get_redirect()
    {
        if ($this->code != static::REDIRECT) {
            return null;
        }
        return $this->message;
    }
    
    /**
     * 
     * @return string
     */
    public function get_message()
    {
        return $this->message;
    }
}
