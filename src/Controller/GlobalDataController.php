<?php

namespace Jtl\Connector\Example\Controller;

use Jtl\Connector\Core\Controller\PullInterface;
use Jtl\Connector\Core\Model\Currency;
use Jtl\Connector\Core\Model\CustomerGroup;
use Jtl\Connector\Core\Model\CustomerGroupI18n;
use Jtl\Connector\Core\Model\GlobalData;
use Jtl\Connector\Core\Model\Identity;
use Jtl\Connector\Core\Model\Language;
use Jtl\Connector\Core\Model\QueryFilter;
use Jtl\Connector\Core\Model\ShippingMethod;
use Jtl\Connector\Core\Model\TaxRate;
use Ramsey\Uuid\Uuid;

class GlobalDataController implements PullInterface
{
    /**
     * @inheritDoc
     */
    public function pull(QueryFilter $queryFilter) : array
    {
        $result = [];
        
        $globalData = new GlobalData;
        
        // ***************************************
        // * Static values for presentation only *
        // ***************************************
        
        // Languages
        $globalData->addLanguage(
            (new Language())->setId(new Identity('4faa508a23e3427889bfae0561d7915d'))
                ->setLanguageISO('ger')
                ->setIsDefault(true)
                ->setNameGerman('Deutsch')
                ->setNameEnglish('German')
        );
        
        $globalData->addLanguage(
            (new Language())->setId(new Identity('8acb0d79a1bc407e9194cc5d8359aaec'))
                ->setLanguageISO('eng')
                ->setIsDefault(false)
                ->setNameGerman('Englisch')
                ->setNameEnglish('English')
        );
        
        // Currencies
        $globalData->addCurrency(
            (new Currency())->setId(new Identity('56b0d7e12feb47838e2cd6c49f2cfd82'))
                ->setIsDefault(true)
                ->setName('Euro')
                ->setDelimiterCent(',')
                ->setDelimiterThousand('.')
                ->setFactor(1.0)
                ->setHasCurrencySignBeforeValue(false)
                ->setIso('EUR')
                ->setNameHtml('&euro;')
        );
        
        // CustomerGroups
        $globalData->addCustomerGroup(
            (new CustomerGroup())->setId(new Identity('c2c6154f05b342d4b2da85e51ec805c9'))
                ->setIsDefault(true)
                ->setApplyNetPrice(false)
                ->addI18n((new CustomerGroupI18n())->setName('Endkunde'))
        );
        
        $globalData->addCustomerGroup(
            (new CustomerGroup())->setId(new Identity('b1d7b4cbe4d846f0b323a9d840800177'))
                ->setIsDefault(false)
                ->setApplyNetPrice(true)
                ->addI18n((new CustomerGroupI18n())->setName('Haendler'))
        );
        
        // TaxRates
        $globalData->addTaxRate(
            (new TaxRate())->setId(new Identity('f1ec9220f3f64049926a83f5ba8df985'))
                ->setRate(19.0)
        );
        
        $globalData->addTaxRate(
            (new TaxRate())->setId(new Identity('ec0a029a85554745aa42fb708d3c5c8c'))
                ->setRate(7.0)
        );
        
        // shippingMethods
        $globalData->addShippingMethod(
            (new ShippingMethod())->setId(new Identity('7adeec3fbbe942c6a8e910ead168703d'))
                ->setName('DHL Versand')
        );
        
        $result[] = $globalData;
        
        return $result;
    }
}
