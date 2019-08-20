<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package Jtl\Connector\Example\Controller
 */

namespace Jtl\Connector\Example\Controller;

use jtl\Connector\Core\Controller\Controller;
use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Core\Model\QueryFilter;
use jtl\Connector\Core\Rpc\Error;
use jtl\Connector\Core\Utilities\ClassName;
use Jtl\Connector\Example\Utility\Mmc;
use jtl\Connector\Formatter\ExceptionFormatter;
use jtl\Connector\Core\Model\DataModel;
use jtl\Connector\Model\Statistic;
use jtl\Connector\Result\Action;
use jtl\Connector\Serializer\JMS\SerializerBuilder;

abstract class DataController extends Controller
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

        try {
            $class = ClassName::getFromNS(get_called_class());

            $statModel = new Statistic();
            $mapper = Mmc::getMapper($class);

            $statModel->setAvailable($mapper->fetchCount());
            $statModel->setControllerName(lcfirst($class));

            $action->setResult($statModel->getPublic());
        } catch (\Exception $exc) {
            $action->setError($this->handleException($exc));
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
            $class = ClassName::getFromNS(get_called_class());

            $mapper = Mmc::getMapper($class);
            $mapper->save($model);
            $action->setResult($model);
        } catch (\Exception $exc) {
            $action->setError($this->handleException($exc));
        }

        return $action;
    }

    /**
     * Select
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

            $class = ClassName::getFromNS(get_called_class());

            $mapper = Mmc::getMapper($class);
            $models = $mapper->findAll($limit);

            $serializer = SerializerBuilder::create();
            foreach ($models as $model) {
                $result[] = $serializer->deserialize($model, sprintf('jtl\Connector\Model\%s', $class), 'json');
            }

            $action->setResult($result);
        } catch (\Exception $exc) {
            $action->setError($this->handleException($exc));
        }

        return $action;
    }

    /**
     * Delete
     *
     * @param \jtl\Connector\Core\Model\DataModel $model
     * @return \jtl\Connector\Result\Action
     */
    public function delete(DataModel $model)
    {
        $action = new Action();
        $action->setHandled(true);

        try {
            $class = ClassName::getFromNS(get_called_class());

            $mapper = Mmc::getMapper($class);
            $res = $mapper->delete($model);

            $action->setResult($res);
        } catch (\Exception $exc) {
            $action->setError($this->handleException($exc));
        }

        return $action;
    }

    protected function handleException(\Exception $e)
    {
        Logger::write(ExceptionFormatter::format($e), Logger::WARNING, 'controller');

        $err = new Error();
        $err->setCode($e->getCode());
        $err->setMessage($e->getMessage());

        return $err;
    }
}
