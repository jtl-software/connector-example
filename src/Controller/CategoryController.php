<?php

namespace Jtl\Connector\Example\Controller;

use Jtl\Connector\Core\Controller\PullInterface;
use Jtl\Connector\Core\Controller\PushInterface;
use Jtl\Connector\Core\Definition\IdentityType;
use Jtl\Connector\Core\Model\AbstractDataModel;
use Jtl\Connector\Core\Model\Category;
use Jtl\Connector\Core\Model\CategoryI18n;
use Jtl\Connector\Core\Model\QueryFilter;

/**
 * Creating the controller for the entity that the controller should support using the method interfaced to define supported methods
 *
 * Class CategoryController
 * @package Jtl\Connector\Example\Controller
 */
class CategoryController extends AbstractController implements PullInterface, PushInterface
{
    /**
     * @inheritDoc
     */
    public function push(AbstractDataModel $model) : AbstractDataModel
    {
        $statement = $this->pdo->prepare("INSERT INTO categories (id, parent_id, status) VALUES (NULL, ?, ?)");
        $statement->execute([
            $model->getParentCategoryId()->getEndpoint() === "" ? 0 : $model->getParentCategoryId()->getEndpoint(),
            (int)$model->getIsActive(),
        ]);
        
        $endpointId = $this->pdo->lastInsertId();
        $model->getId()->setEndpoint($endpointId);
        
        $statement = $this->pdo->prepare("
            INSERT INTO category_translations (id, category_id, name, description, title_tag, meta_description, meta_keywords, language_iso)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($model->getI18ns() as $i18n) {
            $statement->execute([
                $endpointId,
                $i18n->getName(),
                $i18n->getDescription(),
                $i18n->getTitleTag(),
                $i18n->getMetaDescription(),
                $i18n->getMetaKeywords(),
                $i18n->getLanguageIso(),
            ]);
        }
        
        return $model;
    }
    
    /**
     * @inheritDoc
     */
    public function pull(QueryFilter $queryFilter) : array
    {
        $return = [];
        
        $statement = $this->pdo->prepare("
            SELECT * FROM categories c
            LEFT JOIN mapping m ON c.id = m.endpoint
            WHERE m.host IS NULL OR m.type != ?
        ");
        
        $statement->execute([
            IdentityType::CATEGORY
        ]);
        
        $categories = $statement->fetchAll();
        
        foreach ($categories as $category) {
            $return[] = $this->createJtlCategory($category);
        }
        
        return $return;
    }

    /**
     * @param array $category
     * @return Category
     */
    protected function createJtlCategory(array $category) : Category
    {
        $jtlCategory = (new Category)->setIsActive($category["status"]);
        $jtlCategory->getParentCategoryId()->setEndpoint($category["parent_id"]);
        
        $statement = $this->pdo->prepare("
            SELECT * FROM category_translations t
            LEFT JOIN categories c ON c.id = t.category_id
        ");
        $statement->execute();
        $i18ns = $statement->fetchAll();
        
        foreach ($i18ns as $i18n) {
            $jtlCategory->addI18n($this->createJtlCategoryI18n($i18n));
        }
        
        return $jtlCategory;
    }

    /**
     * @param array $i18n
     * @return CategoryI18n
     */
    protected function createJtlCategoryI18n(array $i18n) : CategoryI18n
    {
        return (new CategoryI18n())
            ->setName($i18n["name"])
            ->setDescription($i18n["description"])
            ->setTitleTag($i18n["title_tag"])
            ->setMetaDescription($i18n["meta_description"])
            ->setMetaKeywords($i18n["meta_keywords"])
            ->setLanguageIso($i18n["language_iso"]);
    }
}
