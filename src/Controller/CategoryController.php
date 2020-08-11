<?php

namespace Jtl\Connector\Example\Controller;

use Jtl\Connector\Core\Controller\PullInterface;
use Jtl\Connector\Core\Controller\PushInterface;
use Jtl\Connector\Core\Definition\Model;
use Jtl\Connector\Core\Model\AbstractDataModel;
use Jtl\Connector\Core\Model\Category;
use Jtl\Connector\Core\Model\CategoryI18n;
use Jtl\Connector\Core\Model\QueryFilter;

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
        $return = [];
        
        $statement = $this->database->prepare("
            SELECT c.* FROM categories c
            LEFT JOIN mapping m ON c.id = m.endpoint
            WHERE m.host IS NULL OR m.type != ?
        ");
        
        $statement->execute([
            Model::getIdentityType("Category")
        ]);
    
        $categories = $statement->fetchAll();
    
        foreach ($categories as $category) {
            $return[] = $this->createJtlCategory($category);
        }
        
        return $return;
    }
    
    protected function createJtlCategory($category) {
        $jtlCategory = (new Category)->setIsActive($category["status"]);
        
        $jtlCategory->getParentCategoryId()->setEndpoint($category["parent_id"]);
        
        $jtlCategory->addI18n(
            (new CategoryI18n())
                ->setName($category["name"])
        );
        
        return $jtlCategory;
    }
}