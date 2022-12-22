<?php

namespace Paytic\Omnipay\Euplatesc\Message;

use Paytic\Omnipay\Common\Message\Traits\RedirectHtmlTrait;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * PayU Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    use RedirectHtmlTrait;

    /**
     * @return array
     */
    public function getRedirectData()
    {
        $data = array_merge($this->getDataProperty('order'), $this->getDataProperty('bill'));
        $data['fp_hash'] = $this->getDataProperty('fp_hash');
        $data['lang'] = $this->getDataProperty('lang', 'ro');

        return $data;
    }

    /**
     * @return string
     */
    public function generateHiddenInputs()
    {
        $hiddenFields = '';
        foreach ($this->getRedirectData() as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $iKey => $iValue) {
                    $k = $key . '[' . $iKey . ']';
                    $hiddenFields .= $this->generateHiddenInput($k, $iValue) . "\n";
                }
            } else {
                $hiddenFields .= $this->generateHiddenInput($key, $value) . "\n";
            }
        }

        return $hiddenFields;
    }

}
