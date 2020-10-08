<?php

namespace Jtl\Connector\Example\Tests\src\Controller;

use Jtl\Connector\Core\Model\Category;
use Jtl\Connector\Core\Model\CategoryI18n;
use Jtl\Connector\Core\Model\Generator\AbstractModelFactory;
use Jtl\Connector\Core\Model\QueryFilter;
use Jtl\Connector\Example\Controller\CategoryController;
use Jtl\Connector\Example\Tests\TestCase;
use ReflectionException;

class CategoryControllerTest extends TestCase
{
    /**
     * @dataProvider createJtlCategoryDataProvider
     * @param mixed[] $categoryData
     * @throws ReflectionException
     */
    public function testCreateJtlCategoryReturnsValidObjects(array $categoryData)
    {
        $pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $statementMock = $this->createMock(\PDOStatement::class);
        
        $pdoMock
            ->expects($this->once())
            ->method("prepare")
            ->willReturn($statementMock);
        
        $statementMock
            ->expects($this->once())
            ->method("execute")
            ->with([$categoryData["id"]]);
        
        $statementMock
            ->expects($this->once())
            ->method("fetchAll")
            ->willReturn([]);
        
        
        /** @var $result Category */
        $result = $this->invokeMethodFromObject(new CategoryController($pdoMock), "createJtlCategory", $categoryData);
        
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
        $categoryControllerMock = $this->getMockBuilder(CategoryController::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        /** @var $result CategoryI18n */
        $result = $this->invokeMethodFromObject($categoryControllerMock, "createJtlCategoryI18n", $categoryI18nData);
        
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
        
        $statementMock = $this->createMock(\PDOStatement::class);
        
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
        
        $statementMock = $this->createMock(\PDOStatement::class);
        
        $pdoMock
            ->expects($this->exactly(1 + count($category->getI18ns())))
            ->method("prepare")
            ->willReturn($statementMock);
        
        $statementMock
            ->expects($this->exactly(1 + count($category->getI18ns())))
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
     * @dataProvider categoryPullDataProvider
     * @param array $testDbResult
     * @throws ReflectionException
     */
    public function testCategoryPull(array $testDbResult)
    {
        $pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $statementMock = $this->createMock(\PDOStatement::class);
        
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
        
        $statementMock
            ->expects($this->once())
            ->method("fetchAll")
            ->willReturn($testDbResult);
        
        $categoryControllerMock
            ->expects($this->exactly(count($testDbResult)))
            ->method("createJtlCategory");
        
        $result = $this->invokeMethodFromObject($categoryControllerMock, "pull", new QueryFilter());
        $this->assertCount(count($testDbResult), $result);
    }
    
    public function categoryPullDataProvider()
    {
        return [
            [
                [
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
                ],
            ],
        ];
    }
}
