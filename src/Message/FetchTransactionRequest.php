<?php

namespace Paytic\Omnipay\Euplatesc\Message;

use Paytic\Omnipay\Common\Message\Traits\RequestDataGetWithValidationTrait;
use Paytic\Omnipay\Euplatesc\Helper;

/**
 * FetchTransactionRequest Request
 */
class FetchTransactionRequest extends AbstractRequest
{
    use RequestDataGetWithValidationTrait;

    const WS_ENDPOINT = 'https://manager.euplatesc.ro/v3/?action=ws';

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    public function validateDataFields()
    {
        return [
            'orderId',
            'key',
            'mid',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function populateData()
    {
        $data = [
            'method' => 'check_status',
            'mid' => $this->getMid(),
            'invoice_id' => $this->getOrderId(),
            'timestamp' => gmdate("YmdHis"),
            'nonce' => md5(microtime() . mt_rand()),
        ];

        $data["fp_hash"] = $this->generateHmac($this->generateHashString($data));

        return $data;
    }

    /**
     * Send the request with specified data
     *
     * @param mixed $data The data to send
     * @return bool
     */
    public function sendData($data)
    {
        $response = $this->httpClient->request(
            'POST',
            self::WS_ENDPOINT,
            [
                'Content-type' => 'application/x-www-form-urlencoded',
            ],
            http_build_query($data)
        );

        $responseContents = $response->getBody()->getContents();
        $responseData = json_decode($responseContents, true);

        return $this->response = new FetchTransactionResponse($this, $responseData);
    }

    /**
     * @param $data
     * @return string
     */
    protected function generateHmac($data)
    {
        $key = $this->getKey();

        return Helper::generateHmac($data, $key);
    }

    /**
     * @param array $data
     * @return string
     */
    public function generateHashString(array $data)
    {
        $return = "";
        foreach ($data as $digit) {
            $return .= Helper::generateHashFromString($digit);
        }

        return $return;
    }
}
