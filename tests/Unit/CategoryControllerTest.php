<?php

namespace Jtl\Connector\Tests\Unit;

use Jtl\Connector\Core\Model\Category;
use Jtl\Connector\Core\Model\CategoryI18n;
use Jtl\Connector\Example\Controller\CategoryController;
use Jtl\Connector\Tests\AbstractTestCase;

class CategoryControllerTest extends AbstractTestCase
{
    /**
     * @dataProvider categoryDataProvider
     * @param $categoryData
     * @throws \ReflectionException
     */
    public function testCreateJtlCategoryReturnsValidObjects($categoryData)
    {
        /** @var $result Category */
        $result = $this->invokeMethodFromObject(new CategoryController($this->pdo), "createJtlCategory", $categoryData);
        
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($categoryData["id"], $result->getId()->getEndpoint());
        $this->assertEquals($categoryData["status"], $result->getIsActive());
        $this->assertEquals($categoryData["parent_id"], $result->getParentCategoryId()->getEndpoint());
    }
    
    /**
     * @dataProvider categoryI18nDataProvider
     * @param $categoryI18nData
     * @throws \ReflectionException
     */
    public function testCreateJtlCategoryI18nReturnsValidObjects($categoryI18nData)
    {
        
        /** @var $result CategoryI18n */
        $result = $this->invokeMethodFromObject(new CategoryController($this->pdo), "createJtlCategoryI18n", $categoryI18nData);
     
        $this->assertInstanceOf(CategoryI18n::class, $result);
        $this->assertEquals($categoryI18nData["name"], $result->getName());
        $this->assertEquals($categoryI18nData["description"], $result->getDescription());
        $this->assertEquals($categoryI18nData["title_tag"], $result->getTitleTag());
        $this->assertEquals($categoryI18nData["meta_description"], $result->getMetaDescription());
        $this->assertEquals($categoryI18nData["meta_keywords"], $result->getMetaKeywords());
        $this->assertEquals($categoryI18nData["language_iso"], $result->getLanguageIso());
    }
    
    public function categoryDataProvider()
    {
        return [
            [
                [
                    "id"        => "1",
                    "status"    => true,
                    "parent_id" => "1",
                ],
            ],
            [
                [
                    "id"        => "1",
                    "status"    => false,
                    "parent_id" => "1",
                ],
            ],
        ];
    }
    
    public function categoryI18nDataProvider()
    {
        return [
            [
                [
                    "name"             => "testName",
                    "description"      => "testDescription",
                    "title_tag"        => "testTitleTag",
                    "meta_description" => "testMetaDescription",
                    "meta_keywords"    => "testMetaKeywords",
                    "language_iso"     => "ger",
                ],
            ],
            [
                [
                    "name"             => "testName",
                    "description"      => "testDescription",
                    "title_tag"        => "testTitleTag",
                    "meta_description" => "testMetaDescription",
                    "meta_keywords"    => "testMetaKeywords",
                    "language_iso"     => "eng",
                ],
            ],
            [
                [
                    "name"             => "testName",
                    "description"      => "testDescription",
                    "title_tag"        => "testTitleTag",
                    "meta_description" => "testMetaDescription",
                    "meta_keywords"    => "testMetaKeywords",
                    "language_iso"     => "testInvalidValue",
                
                ],
            ],
        ];
    }
}