<?php

namespace jtl\Connector\Example\Controller;

use jtl\Connector\Core\Controller\Controller;
use jtl\Connector\Core\Model\DataModel;
use jtl\Connector\Core\Model\QueryFilter;
use jtl\Connector\Core\Rpc\Error;
use jtl\Connector\Result\Action;
use jtl\Connector\Model\ConnectorIdentification;

class Connector extends Controller
{
    private $controllers = array(
        'Product'
    );

    public function push(DataModel $model)
    {

    }

    public function delete(DataModel $model)
    {

    }

    public function pull(QueryFilter $filter)
    {

    }

    public function statistic(QueryFilter $filter)
    {
        $action = new Action();
        $action->setHandled(true);

        try {
            $result = array();

            foreach ($this->controllers as $controller) {
                $controller = __NAMESPACE__ . '\\' . $controller;
                $obj = new $controller();

                if (method_exists($obj, 'statistic')) {
                    $method_result = $obj->statistic($filter);

                    $result[] = $method_result->getResult();
                }
            }

            $action->setResult($result);
        }
        catch (\Exception $exc) {
            $err = new Error();
            $err->setCode($exc->getCode());
            $err->setMessage($exc->getMessage());
            $action->setError($err);
        }

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

        $identification = new ConnectorIdentification();
        $identification->setEndpointVersion('1.0.0.0')
            ->setPlatformName('Example')
            ->setPlatformVersion('1.0')
            ->setProtocolVersion(Application()->getProtocolVersion());

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

        return $action;
    }
}
