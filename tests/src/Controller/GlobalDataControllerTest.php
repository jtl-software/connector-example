<?php

namespace Jtl\Connector\Example\Tests\src\Controller;

use Jtl\Connector\Core\Model\Currency;
use Jtl\Connector\Core\Model\CustomerGroup;
use Jtl\Connector\Core\Model\GlobalData;
use Jtl\Connector\Core\Model\Language;
use Jtl\Connector\Core\Model\QueryFilter;
use Jtl\Connector\Example\Controller\GlobalDataController;
use Jtl\Connector\Example\Tests\TestCase;
use Jtl\Connector\Core\Model\Identity;
use Jtl\Connector\Core\Model\CustomerGroupI18n;
use Jtl\Connector\Core\Model\ShippingMethod;
use Jtl\Connector\Core\Model\TaxRate;

class GlobalDataControllerTest extends TestCase
{
    public function testPullReturnValidObjectArray()
    {
        $controller = new GlobalDataController();

        $result = $controller->pull(new QueryFilter());
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(GlobalData::class, $result);
    }

    public function testPullReturnStaticValues()
    {
        $controller = new GlobalDataController();

        /** @var $result GlobalData */
        $result = $controller->pull(new QueryFilter())[0];


        // Languages
        $this->assertContainsOnly(Language::class, $result->getLanguages());
        $this->assertCount(2, $result->getLanguages());
        $this->assertEquals((new Language())
            ->setId(new Identity('4faa508a23e3427889bfae0561d7915d'))
            ->setLanguageISO('ger')
            ->setIsDefault(true)
            ->setNameGerman('Deutsch')
            ->setNameEnglish('German'), $result->getLanguages()[0]);
        $this->assertEquals((new Language())
            ->setId(new Identity('8acb0d79a1bc407e9194cc5d8359aaec'))
            ->setLanguageISO('eng')
            ->setIsDefault(false)
            ->setNameGerman('Englisch')
            ->setNameEnglish('English'), $result->getLanguages()[1]);
        
        // Currencies
        $this->assertContainsOnly(Currency::class, $result->getCurrencies());
        $this->assertCount(1, $result->getCurrencies());
        $this->assertEquals((new Currency())
            ->setId(new Identity('56b0d7e12feb47838e2cd6c49f2cfd82'))
            ->setIsDefault(true)
            ->setName('Euro')
            ->setDelimiterCent(',')
            ->setDelimiterThousand('.')
            ->setFactor(1.0)
            ->setHasCurrencySignBeforeValue(false)
            ->setIso('EUR')
            ->setNameHtml('&euro;'), $result->getCurrencies()[0]);

        // CustomerGroups
        $this->assertContainsOnly(CustomerGroup::class, $result->getCustomerGroups());
        $this->assertCount(2, $result->getCustomerGroups());
        $this->assertEquals(
            (new CustomerGroup())->setId(new Identity('c2c6154f05b342d4b2da85e51ec805c9'))
                ->setIsDefault(true)
                ->setApplyNetPrice(false)
                ->addI18n((new CustomerGroupI18n())->setName('Endkunde')),
            $result->getCustomerGroups()[0]
        );
        $this->assertEquals(
            (new CustomerGroup())->setId(new Identity('b1d7b4cbe4d846f0b323a9d840800177'))
                ->setIsDefault(false)
                ->setApplyNetPrice(true)
                ->addI18n((new CustomerGroupI18n())->setName('Haendler')),
            $result->getCustomerGroups()[1]
        );

        // TaxRates
        $this->assertContainsOnly(TaxRate::class, $result->getTaxRates());
        $this->assertCount(2, $result->getTaxRates());
        $this->assertEquals(
            (new TaxRate())->setId(new Identity('f1ec9220f3f64049926a83f5ba8df985'))
                ->setRate(19.0),
            $result->getTaxRates()[0]
        );
        $this->assertEquals(
            (new TaxRate())->setId(new Identity('ec0a029a85554745aa42fb708d3c5c8c'))
                ->setRate(7.0),
            $result->getTaxRates()[1]
        );

        // shippingMethods
        $this->assertContainsOnly(ShippingMethod::class, $result->getShippingMethods());
        $this->assertCount(1, $result->getShippingMethods());
        $this->assertEquals(
            (new ShippingMethod())->setId(new Identity('7adeec3fbbe942c6a8e910ead168703d'))
                ->setName('DHL Versand'),
            $result->getShippingMethods()[0]
        );
    }
}
