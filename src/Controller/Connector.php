<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package Jtl\Connector\Example\Controller
 */

namespace Jtl\Connector\Example\Controller;

use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Core\Model\QueryFilter;
use Jtl\Connector\Example\Utility\Mmc;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Model\ConnectorServerInfo;
use jtl\Connector\Result\Action;
use jtl\Connector\Model\ConnectorIdentification;

class Connector extends DataController
{
    /**
     * Statistic
     *
     * @param \jtl\Connector\Core\Model\QueryFilter $queryFilter
     * @return \jtl\Connector\Result\Action
     */
    public function statistic(QueryFilter $queryFilter)
    {
        $action = new Action();
        $action->setHandled(true);

        $results = array();

        $mainControllers = array(
            'Category',
            'Customer',
            'CustomerOrder',
            'CrossSelling',
            'DeliveryNote',
            'Image',
            'Product',
            'Manufacturer',
            'Payment'
        );

        foreach ($mainControllers as $mainController) {
            try {
                $controller = Mmc::getController($mainController);
                $result = $controller->statistic($queryFilter);
                if ($result !== null && $result->isHandled() && !$result->isError()) {
                    $results[] = $result->getResult();
                }
            } catch (\Exception $exc) {
                Logger::write(ExceptionFormatter::format($exc), Logger::WARNING, 'controller');
            }
        }

        $action->setResult($results);

        return $action;
    }

    /**
     * Identify
     *
     * @return \jtl\Connector\Result\Action
     */
    public function identify()
    {
        $action = new Action();
        $action->setHandled(true);

        $returnMegaBytes = function ($data) {
            $data = trim($data);
            $len = strlen($data);
            $value = substr($data, 0, $len - 1);
            $unit = strtolower(substr($data, $len - 1));
            switch ($unit) {
                case 'g':
                    $value *= 1024;
                    break;
                case 'k':
                    $value /= 1024;
                    break;
            }
            return (int)round($value);
        };

        $serverInfo = new ConnectorServerInfo();
        $serverInfo->setMemoryLimit($returnMegaBytes(ini_get('memory_limit')))
            ->setExecutionTime((int) ini_get('max_execution_time'))
            ->setPostMaxSize($returnMegaBytes(ini_get('post_max_size')))
            ->setUploadMaxFilesize($returnMegaBytes(ini_get('upload_max_filesize')));

        $identification = new ConnectorIdentification();
        $identification->setEndpointVersion('1.0.0')
            //Bulk platform is the license for third party connectors
            ->setPlatformName('Bulk')
            //Do not set platformVersion for Bulk platform
            //->setPlatformVersion('1.0')
            ->setProtocolVersion(Application()->getProtocolVersion())
            ->setServerInfo($serverInfo);

        $action->setResult($identification);

        return $action;
    }

    /**
     * Finish
     *
     * @return \jtl\Connector\Result\Action
     */
    public function finish()
    {
        $action = new Action();

        $action->setHandled(true);
        $action->setResult(true);

        return $action;
    }
}
