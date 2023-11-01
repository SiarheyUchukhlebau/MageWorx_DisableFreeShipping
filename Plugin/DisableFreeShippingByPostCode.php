<?php
/**
 * Copyright © 2020 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DisableFreeShipping\Plugin;

/**
 * Class DisableFreeShippingByPostCode
 */
class DisableFreeShippingByPostCode
{
    protected $restrictedAreas = [
        'PH',
        'IV'
    ];

    /**
     * @param \Magento\OfflineShipping\Model\Carrier\Freeshipping $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function aroundCollectRates(
        \Magento\OfflineShipping\Model\Carrier\Freeshipping $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {
        if ($request->getDestPostcode()) { // Check is postcode exists in request
            if ($this->postCodeRestrictedByArea($request->getDestPostcode())) { // Check is postcode area in restriction list
                return false; // Disable method
            }
        }

        return $proceed($request);
    }

    /**
     * Test postcode
     *
     * @param string $postCode
     * @return bool
     */
    private function postCodeRestrictedByArea(string $postCode): bool
    {
        if (strlen($postCode) < 2) { // area in postcode must be at least 2 symbols
            return false;
        }

        if (empty($this->restrictedAreas)) { // Restricted areas list must have at least one area
            return false;
        }

        $area = substr($postCode, 0, 2); // Obtain area code

        return in_array($area, $this->restrictedAreas); // Validate area code from customer postcode
    }
}
