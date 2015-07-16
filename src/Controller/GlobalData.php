<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package jtl\Connector\Example\Controller
 */

namespace jtl\Connector\Example\Controller;

use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Core\Model\QueryFilter;
use jtl\Connector\Core\Rpc\Error;
use jtl\Connector\Example\Utility\Mmc;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Model\Currency;
use jtl\Connector\Model\CustomerGroup;
use jtl\Connector\Model\CustomerGroupI18n;
use jtl\Connector\Model\Identity;
use jtl\Connector\Model\Language;
use jtl\Connector\Model\TaxRate;
use jtl\Connector\Result\Action;
use jtl\Connector\Serializer\JMS\SerializerBuilder;

class GlobalData extends DataController
{
    /**
     * Pull
     *
     * @param \jtl\Connector\Core\Model\QueryFilter $queryFilter
     * @return \jtl\Connector\Result\Action
     */
    public function pull(QueryFilter $queryFilter)
    {
        $action = new Action();
        $action->setHandled(true);

        try {
            $result = [];
            $limit = $queryFilter->isLimit() ? $queryFilter->getLimit() : 100;

            $globalData = Mmc::getModel('GlobalData');

            $id = new Identity(1);

            // Currencies
            $globalData->addLanguage(
                (new Language())->setId($id)
                    ->setLanguageISO('ger')
                    ->setIsDefault(true)
                    ->setNameGerman('Deutsch')
                    ->setNameEnglish('German')
            );

            // Languages
            $globalData->addCurrency(
                (new Currency())->setId($id)
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
                (new CustomerGroup())->setId(new Identity(1))
                    ->setIsDefault(true)
                    ->setApplyNetPrice(false)
                    ->addI18n((new CustomerGroupI18n())->setCustomerGroupId($id)->setLanguageISO('ger')->setName('Endkunde'))
            );

            // TaxRates
            $globalData->addTaxRate(
                (new TaxRate())->setId($id)
                    ->setRate(19.0)
            );

            $result[] = $globalData;
            $action->setResult($result);
        } catch (\Exception $exc) {
            Logger::write(ExceptionFormatter::format($exc), Logger::WARNING, 'controller');

            $err = new Error();
            $err->setCode($exc->getCode());
            $err->setMessage($exc->getMessage());
            $action->setError($err);
        }

        return $action;
    }
}
