<?php

namespace Jtl\Connector\Example\Controller;

use Jtl\Connector\Core\Controller\PullInterface;
use Jtl\Connector\Core\Controller\PushInterface;
use Jtl\Connector\Core\Model\AbstractDataModel;
use Jtl\Connector\Core\Model\Category;
use Jtl\Connector\Core\Model\CategoryCustomerGroup;
use Jtl\Connector\Core\Model\CategoryI18n;
use Jtl\Connector\Core\Model\Identity;
use Jtl\Connector\Core\Model\QueryFilter;
use Jtl\Connector\Core\Model\TranslatableAttribute;
use Jtl\Connector\Core\Model\TranslatableAttributeI18n;
use stdClass;

class CategoryController extends AbstractController implements PullInterface, PushInterface
{
    /**
     * @inheritDoc
     */
    public function push(AbstractDataModel $model) : AbstractDataModel
    {
        $statement = $this->database->prepare("INSERT INTO categories (id, name, parent_id, status) VALUES (NULL, ?, ?, ?)");
        $statement->execute([
            $model->getI18ns()[0]->getName(),
            $model->getParentCategoryId()->getEndpoint() === "" ? 0 : $model->getParentCategoryId()->getEndpoint(),
            (int)$model->getIsActive(),
        ]);
        
        $endpointId = $this->database->lastInsertId();
        $model->getId()->setEndpoint($endpointId);
        
        return $model;
    }
    
    /**
     * @inheritDoc
     */
    public function pull(QueryFilter $queryFilter) : array
    {
    }
}