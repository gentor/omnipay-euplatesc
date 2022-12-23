<?php

namespace Paytic\Omnipay\Euplatesc\Message;

use Omnipay\Common\Message\AbstractResponse as CommonAbstractResponse;

/**
 * FetchTransactionResponse Response
 */
class FetchTransactionResponse extends CommonAbstractResponse
{
    public function getData()
    {
        if (isset($this->data['success'])) {
            $this->data = json_decode($this->data['success'], true);
            if (isset($this->data[0])) {
                $this->data = $this->data[0];
            }
        }

        return $this->data;
    }

    public function isSuccessful()
    {
        return isset($this->data['action']) && $this->data['action'] == 0;
    }

    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->data['error'] ?? $this->data['message'];
    }

    /**
     * Response code
     *
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        return $this->data['ecode'] ?? $this->data['rrn'];
    }

    /**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->data['ep_id'] ?? null;
    }
}
