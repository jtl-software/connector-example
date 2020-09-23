<?php

namespace Jtl\Connector\Tests\Unit;

use Jtl\Connector\Core\Definition\IdentityType;
use Jtl\Connector\Core\Model\Category;
use Jtl\Connector\Core\Model\CategoryI18n;
use Jtl\Connector\Core\Model\Generator\AbstractModelFactory;
use Jtl\Connector\Core\Model\Identity;
use Jtl\Connector\Example\Controller\CategoryController;
use Jtl\Connector\Tests\AbstractTestCase;
use Ramsey\Uuid\Uuid;
use ReflectionException;

class CategoryControllerTest extends AbstractTestCase
{
    /**
     * @dataProvider createJtlCategoryDataProvider
     * @param $categoryData
     * @throws ReflectionException
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
     * @dataProvider createJtlCategoryI18nDataProvider
     * @param $categoryI18nData
     * @throws ReflectionException
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
    
    public function createJtlCategoryDataProvider()
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
    
    public function createJtlCategoryI18nDataProvider()
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
    
    /**
     * @dataProvider categoryDataProvider
     * @param Category $category
     * @throws ReflectionException
     */
    public function testCategoryDeleteIfIdExists(Category $category)
    {
        $pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();
        
        $pdoMock
            ->expects($this->once())
            ->method("prepare")
            ->willReturn($statementMock);
        
        $statementMock
            ->expects($this->once())
            ->method("execute")
            ->with([$category->getId()->getEndpoint()]);
        
        $this->invokeMethodFromObject(new CategoryController($pdoMock), "delete", $category);
    }
    
    /**
     * @dataProvider categoryDataProvider
     * @param Category $category
     * @throws ReflectionException
     */
    public function testCategoryPush(Category $category)
    {
        $pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();
        
        $pdoMock
            ->expects($this->exactly(2))
            ->method("prepare")
            ->willReturn($statementMock);
        
        $statementMock
            ->expects($this->exactly(2))
            ->method("execute");
        
        /** @var $result Category */
        $result = $this->invokeMethodFromObject(new CategoryController($pdoMock), "push", $category);
        $this->assertNotNull($result->getId()->getEndpoint());
    }
    
    public function categoryDataProvider()
    {
        $testData = [];
        $categoryFactory = AbstractModelFactory::createFactory("Category");
        
        $categories = $categoryFactory->make(mt_rand(1, 5));
        array_merge($categories, $categoryFactory->make(mt_rand(1, 5), ["id" => null]));
        
        foreach ($categories as $category) {
            $testData[] = [$category];
        }
        
        return $testData;
    }
    
    /**
     * @throws ReflectionException
     */
    public function testCategoryPull()
    {
        $pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $statementMock = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();
        
        $categoryControllerMock = $this->getMockBuilder(CategoryController::class)
            ->setConstructorArgs([$pdoMock])
            ->onlyMethods(["createJtlCategory"])
            ->getMock();
        
        $pdoMock
            ->expects($this->once())
            ->method("prepare")
            ->willReturn($statementMock);
        
        $statementMock
            ->expects($this->once())
            ->method("execute");
        
        $testDbResult = [
            [
                "id"        => "1",
                "status"    => true,
                "parent_id" => null,
            ],
            [
                "id"        => "2",
                "status"    => false,
                "parent_id" => "1",
            ],
        ];
        $statementMock
            ->expects($this->once())
            ->method("fetchAll")
            ->willReturn($testDbResult);
    
        $categoryControllerMock
            ->expects($this->exactly(count($testDbResult)))
            ->method("createJtlCategory");
            
        $result = $this->invokeMethodFromObject($categoryControllerMock, "pull");
        $this->assertCount(count($testDbResult), $result);
    }
}