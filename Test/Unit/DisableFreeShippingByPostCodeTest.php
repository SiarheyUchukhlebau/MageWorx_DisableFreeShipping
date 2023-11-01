<?php

namespace MageWorx\DisableFreeShipping\Test\Unit;

use MageWorx\DisableFreeShipping\Plugin\DisableFreeShippingByPostCode;
use Magento\OfflineShipping\Model\Carrier\Freeshipping;
use Magento\Quote\Model\Quote\Address\RateRequest;
use PHPUnit\Framework\TestCase;

class DisableFreeShippingByPostCodeTest extends TestCase
{
    /** @var DisableFreeShippingByPostCode */
    private $plugin;

    /** @var Freeshipping|\PHPUnit\Framework\MockObject\MockObject */
    private $subjectMock;

    /** @var RateRequest|\PHPUnit\Framework\MockObject\MockObject */
    private $rateRequestMock;

    protected function setUp(): void
    {
        // Initialize the plugin instance
        $this->plugin = new DisableFreeShippingByPostCode();

        // Create a mock instance for Freeshipping and RateRequest
        $this->subjectMock     = $this->createMock(Freeshipping::class);
        $this->rateRequestMock = $this->getMockBuilder(RateRequest::class)
                                      ->disableOriginalConstructor()
                                      ->setMethods(['getDestPostcode'])
                                      ->getMock();
    }

    /**
     * Test to ensure that free shipping is disabled for restricted UK postcodes.
     *
     * Given a UK postcode which is in the restricted list,
     * the method should disable free shipping.
     *
     * @return void
     */
    public function testUKRestrictedPostCode()
    {
        // Set expected restricted UK post code
        $this->rateRequestMock->expects($this->any())
                              ->method('getDestPostcode')
                              ->willReturn('PH1 1AA');

        $proceed = function () {
            return true;
        };

        $result = $this->plugin->aroundCollectRates($this->subjectMock, $proceed, $this->rateRequestMock);

        $this->assertFalse($result, "Free shipping should be disabled for restricted UK postcode");
    }

    /**
     * Test to ensure that free shipping is not disabled for unrestricted UK postcodes.
     *
     * Given a UK postcode which is not in the restricted list,
     * the method should not interfere with the free shipping determination.
     *
     * @return void
     */
    public function testUKNorRestrictedPostCode()
    {
        // Set expected unrestricted UK post code
        $this->rateRequestMock->expects($this->any())
                              ->method('getDestPostcode')
                              ->willReturn('SW1A 1AA');

        $proceed = function () {
            return true;
        };

        $result = $this->plugin->aroundCollectRates($this->subjectMock, $proceed, $this->rateRequestMock);

        $this->assertTrue($result, "Free shipping should not be disabled for unrestricted UK postcode");
    }

    /**
     * Test to ensure that free shipping is not disabled for US postcodes.
     *
     * Given a typical US postcode, the method should not interfere
     * with the free shipping determination.
     *
     * @return void
     */
    public function testUSPostCode()
    {
        // Set expected US post code
        $this->rateRequestMock->expects($this->any())
                              ->method('getDestPostcode')
                              ->willReturn('90210');

        $proceed = function () {
            return true;
        };

        $result = $this->plugin->aroundCollectRates($this->subjectMock, $proceed, $this->rateRequestMock);

        $this->assertTrue($result, "Free shipping should not be disabled for US postcode");
    }

    /**
     * Test to ensure that free shipping is not disabled for Australian postcodes.
     *
     * Given a typical Australian postcode, the method should not interfere
     * with the free shipping determination.
     *
     * @return void
     */
    public function testAustraliaPostCode()
    {
        // Set expected Australian post code
        $this->rateRequestMock->expects($this->any())
                              ->method('getDestPostcode')
                              ->willReturn('2000');

        $proceed = function () {
            return true;
        };

        $result = $this->plugin->aroundCollectRates($this->subjectMock, $proceed, $this->rateRequestMock);

        $this->assertTrue($result, "Free shipping should not be disabled for Australian postcode");
    }

    /**
     * Test to ensure that free shipping is not disabled for short postcodes.
     *
     * Given a short postcode (which is invalid according to UK format),
     * the method should not interfere with the free shipping determination.
     *
     * @return void
     */
    public function testShortPostCode()
    {
        // Set expected short post code (UK-like)
        $this->rateRequestMock->expects($this->any())
                              ->method('getDestPostcode')
                              ->willReturn('P1');

        $proceed = function () {
            return true;
        };

        $result = $this->plugin->aroundCollectRates($this->subjectMock, $proceed, $this->rateRequestMock);

        $this->assertTrue($result, "Free shipping should not be disabled for short postcode");
    }
}
