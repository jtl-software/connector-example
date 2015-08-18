<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package jtl\Connector\Example\Controller
 */

namespace jtl\Connector\Example\Controller;

use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Core\Model\DataModel;
use jtl\Connector\Core\Model\QueryFilter;
use jtl\Connector\Core\Rpc\Error;
use jtl\Connector\Example\Utility\Mmc;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Model\Currency;
use jtl\Connector\Model\CustomerGroup;
use jtl\Connector\Model\CustomerGroupI18n;
use jtl\Connector\Model\Identity;
use jtl\Connector\Model\Language;
use jtl\Connector\Model\ShippingMethod;
use jtl\Connector\Model\TaxRate;
use jtl\Connector\Result\Action;

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

            // ***************************************
            // * Static values for presentation only *
            // ***************************************

            $id1 = new Identity(1);
            $id2 = new Identity(2);

            // Languages
            $globalData->addLanguage(
                (new Language())->setId($id1)
                    ->setLanguageISO('ger')
                    ->setIsDefault(true)
                    ->setNameGerman('Deutsch')
                    ->setNameEnglish('German')
            );

            $globalData->addLanguage(
                (new Language())->setId($id2)
                    ->setLanguageISO('eng')
                    ->setIsDefault(false)
                    ->setNameGerman('Englisch')
                    ->setNameEnglish('English')
            );

            // Currencies
            $globalData->addCurrency(
                (new Currency())->setId($id1)
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
                (new CustomerGroup())->setId($id1)
                    ->setIsDefault(true)
                    ->setApplyNetPrice(false)
                    ->addI18n((new CustomerGroupI18n())->setCustomerGroupId($id1)->setLanguageISO('ger')->setName('Endkunde'))
            );

            $globalData->addCustomerGroup(
                (new CustomerGroup())->setId($id2)
                    ->setIsDefault(false)
                    ->setApplyNetPrice(true)
                    ->addI18n((new CustomerGroupI18n())->setCustomerGroupId($id2)->setLanguageISO('ger')->setName('Haendler'))
            );

            // TaxRates
            $globalData->addTaxRate(
                (new TaxRate())->setId($id1)
                    ->setRate(19.0)
            );

            $globalData->addTaxRate(
                (new TaxRate())->setId($id2)
                    ->setRate(7.0)
            );

            // shippingMethods
            $globalData->addShippingMethod(
                (new ShippingMethod())->setId($id1)
                    ->setName('DHL Versand')
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

    /**
     * Insert or update
     *
     * @param \jtl\Connector\Core\Model\DataModel $model
     * @return \jtl\Connector\Result\Action
     */
    public function push(DataModel $model)
    {
        $action = new Action();
        $action->setHandled(true);

        try {
            $action->setResult(new GlobalData());
        } catch (\Exception $exc) {
            $action->setError($this->handleException($exc));
        }

        return $action;
    }
}
