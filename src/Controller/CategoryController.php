<?php

namespace Jtl\Connector\Example\Controller;

use Jtl\Connector\Core\Controller\DeleteInterface;
use Jtl\Connector\Core\Controller\PullInterface;
use Jtl\Connector\Core\Controller\PushInterface;
use Jtl\Connector\Core\Controller\StatisticInterface;
use Jtl\Connector\Core\Definition\IdentityType;
use Jtl\Connector\Core\Model\AbstractDataModel;
use Jtl\Connector\Core\Model\Category;
use Jtl\Connector\Core\Model\CategoryI18n;
use Jtl\Connector\Core\Model\Identity;
use Jtl\Connector\Core\Model\QueryFilter;

/**
 * Creating the controller for the entity that the controller should support using the method interfaced to define supported methods
 *
 * Class CategoryController
 * @package Jtl\Connector\Example\Controller
 */
class CategoryController extends AbstractController implements PullInterface, PushInterface, StatisticInterface, DeleteInterface
{
    /**
     * @param AbstractDataModel $model
     * @return AbstractDataModel
     */
    public function delete(AbstractDataModel $model): AbstractDataModel
    {
        /** @var $model Category */
        if (!empty($categoryId = $model->getId()->getEndpoint())) {
            $statement = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
            $statement->execute([$categoryId]);
        }

        return $model;
    }

    /**
     * @param AbstractDataModel $model
     * @return AbstractDataModel
     */
    public function push(AbstractDataModel $model): AbstractDataModel
    {
        /** @var Category $model */
        $endpointId = $model->getId()->getEndpoint();
        $endpointId = $this->saveCategory($model, !empty($endpointId) ? $endpointId : null);

        foreach ($model->getI18ns() as $i18n) {
            $statement = $this->pdo->prepare("SELECT * FROM category_translations WHERE category_id = ? AND language_iso = ?");
            $statement->execute([
                $endpointId,
                $i18n->getLanguageIso()
            ]);

            $categoryTranslation = $statement->fetch(\PDO::FETCH_ASSOC);
            if (!empty($categoryTranslation)) {
                $this->saveTranslation($i18n, $endpointId, true);
            } else {
                $this->saveTranslation($i18n, $endpointId);
            }
        }


        return $model;
    }

    /**
     * @param Category $category
     * @param int|null $endpointId
     * @return int
     */
    protected function saveCategory(Category $category, int $endpointId = null): int
    {
        if (!is_null($endpointId)) {
            $sql = "UPDATE categories SET parent_id = ?, status = ? WHERE id = ?";
            $params = [
                $category->getParentCategoryId()->getEndpoint() === "" ? 0 : $category->getParentCategoryId()->getEndpoint(),
                (int)$category->getIsActive(),
                $endpointId
            ];
        } else {
            $sql = "INSERT INTO categories (id, parent_id, status) VALUES (NULL, ?, ?)";
            $params = [
                $category->getParentCategoryId()->getEndpoint() === "" ? 0 : $category->getParentCategoryId()->getEndpoint(),
                (int)$category->getIsActive(),
            ];
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        if (is_null($endpointId)) {
            $endpointId = $this->pdo->lastInsertId();
        }

        $category->getId()->setEndpoint($endpointId);

        return $endpointId;
    }

    /**
     * @param CategoryI18n $categoryI18n
     * @param int $endpointId
     * @param bool $isUpdate
     * @return bool
     */
    protected function saveTranslation(CategoryI18n $categoryI18n, int $endpointId, bool $isUpdate = false): bool
    {
        if ($isUpdate === true) {
            $query = "UPDATE category_translations SET name = ?, description = ?, title_tag = ?, meta_description = ?, meta_keywords = ?
            WHERE language_iso = ? AND category_id = ?";
        } else {
            $query = "INSERT INTO category_translations (name, description, title_tag, meta_description, meta_keywords, language_iso, category_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        }
        $statement = $this->pdo->prepare($query);

        return $statement->execute([
            $categoryI18n->getName(),
            $categoryI18n->getDescription(),
            $categoryI18n->getTitleTag(),
            $categoryI18n->getMetaDescription(),
            $categoryI18n->getMetaKeywords(),
            $categoryI18n->getLanguageIso(),
            $endpointId
        ]);
    }

    /**
     * @inheritDoc
     */
    public function pull(QueryFilter $queryFilter): array
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
     * @param QueryFilter $queryFilter
     * @return int
     */
    public function statistic(QueryFilter $queryFilter): int
    {
        $statement = $this->pdo->prepare("
            SELECT * FROM categories c
            LEFT JOIN mapping m ON c.id = m.endpoint
            WHERE m.host IS NULL OR m.type != ?
        ");
        $statement->execute([
            IdentityType::CATEGORY
        ]);

        return $statement->rowCount();
    }

    /**
     * @param array $category
     * @return Category
     */
    protected function createJtlCategory(array $category): Category
    {
        $jtlCategory = (new Category)
            ->setId(new Identity($category['id']))
            ->setIsActive($category["status"])
            ->setParentCategoryId(new Identity($category['parent_id']));

        $statement = $this->pdo->prepare("
            SELECT * FROM category_translations t
            LEFT JOIN categories c ON c.id = t.category_id
            WHERE c.id = ?
        ");
        $statement->execute([$category['id']]);
        $i18ns = $statement->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($i18ns as $i18n) {
            $jtlCategory->addI18n($this->createJtlCategoryI18n($i18n));
        }

        return $jtlCategory;
    }

    /**
     * @param array $i18n
     * @return CategoryI18n
     */
    protected function createJtlCategoryI18n(array $i18n): CategoryI18n
    {
        return (new CategoryI18n())
            ->setName($i18n["name"])
            ->setDescription($i18n["description"] ?? "")
            ->setTitleTag($i18n["title_tag"] ?? "")
            ->setMetaDescription($i18n["meta_description"] ?? "")
            ->setMetaKeywords($i18n["meta_keywords"] ?? "")
            ->setLanguageIso($i18n["language_iso"] ?? "");
    }
}
