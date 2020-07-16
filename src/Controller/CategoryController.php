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

class CategoryController implements PullInterface, PushInterface
{
    /**
     * @inheritDoc
     */
    public function pull(QueryFilter $queryFilter) : array
    {
        $result = [];
        $limit = $queryFilter->getLimit();
    
        $category = new Category();
    
        // ***************************************
        // * Static values for presentation only *
        // ***************************************
    
        $id1 = new Identity(1);
        $id2 = new Identity(2);
    
        //Attributes
        $category->addAttribute((new TranslatableAttribute)
            ->addI18n((new TranslatableAttributeI18n)
                ->setName('TestAttribute')
                ->setValue(1)
                ->setLanguageIso('ger')
            )
        );
        
        //I18n
        $category->addI18n((new CategoryI18n)
            ->setName('Test Category')
            ->setTitleTag('test')
            ->setMetaKeywords('test')
            ->setMetaDescription('this is a test')
            ->setUrlPath('/test')
        );
        
        // CustomerGroups
        $category->addCustomerGroup(
            (new CategoryCustomerGroup())
                ->setCustomerGroupId($id1)
                ->setDiscount(0)
        );
        
        $category->setIsActive(true);
        $category->setLevel(0);
        $category->setSort(0);
        
        $result[] = $category;
    
        return $result;
    }
    
    /**
     * @inheritDoc
     */
    public function push(AbstractDataModel $model) : AbstractDataModel
    {
        return $model;
    }
}