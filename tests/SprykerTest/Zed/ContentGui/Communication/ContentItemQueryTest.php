<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ContentGui\Communication;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ContentTransfer;
use Generated\Shared\Transfer\LocalizedContentTransfer;
use Orm\Zed\Content\Persistence\SpyContentQuery;
use Spryker\Zed\ContentGui\Communication\Table\ContentTableConstants;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ContentGui
 * @group Communication
 * @group ContentItemQueryTest
 * Add your own group annotations below this line
 */
class ContentItemQueryTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ContentGui\ContentGuiBusinessTester
     */
    protected $tester;

    public function testOrderContentItemsBySelectedItem(): void
    {
        // Arrange
        $this->tester->createBannerContentItem('br-test1');
        $selectedContentItem = $this->tester->createBannerContentItem('br-test2');
        $this->tester->createBannerContentItem('br-test3');

        // Act
        $contentQuery = SpyContentQuery::create();
        $tableMock = new ContentByTypeTableMock('Banner', $contentQuery, $selectedContentItem->getKey());
        $result = $tableMock->fetchData();

        // Assert
        $this->assertNotEmpty($result);
        $this->assertSame($selectedContentItem->getKey(), $result[0][ContentTableConstants::COL_KEY]);
    }

    public function testContentKeyWithSqlSyntaxIsNotExecutedAsSql(): void
    {
        // Arrange
        $this->tester->createBannerContentItem('br-test1');

        // Act
        $contentQuery = SpyContentQuery::create();
        $tableMock = new ContentByTypeTableMock('Banner', $contentQuery, "br-test1'");
        $result = $tableMock->fetchData();

        // Assert
        $this->assertNotEmpty($result);
        $this->assertNoContentItemIsChecked($result);
        $this->assertContentItemsAreOrderedByIdContent($result);
    }

    public function testContentItemNameAndKeyAreHtmlEscapedInRadioButton(): void
    {
        // Arrange
        $contentTransfer = $this->tester->haveContent([
            ContentTransfer::CONTENT_TERM_KEY => 'Banner',
            ContentTransfer::CONTENT_TYPE_KEY => 'Banner',
            ContentTransfer::DESCRIPTION => 'Test Banner',
            ContentTransfer::NAME => '"><script>alert(1)</script>',
            ContentTransfer::KEY => 'br-test"><img src=x>',
            ContentTransfer::LOCALIZED_CONTENTS => [
                [
                    LocalizedContentTransfer::PARAMETERS => '{}',
                ],
            ],
        ]);

        // Act
        $contentQuery = SpyContentQuery::create();
        $tableMock = new ContentByTypeTableMock('Banner', $contentQuery, $contentTransfer->getKey());
        $result = $tableMock->fetchData();

        // Assert
        $actions = $result[0][ContentTableConstants::COL_ACTIONS];
        $this->assertStringNotContainsString('<script>', $actions);
        $this->assertStringNotContainsString('<img', $actions);
        $this->assertStringContainsString('data-content-item-name="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;"', $actions);
        $this->assertStringContainsString('value="br-test&quot;&gt;&lt;img src=x&gt;"', $actions);
    }

    /**
     * @param array<array<string, mixed>> $result
     */
    protected function assertNoContentItemIsChecked(array $result): void
    {
        foreach ($result as $row) {
            $this->assertStringNotContainsString('checked="checked"', $row[ContentTableConstants::COL_ACTIONS]);
        }
    }

    /**
     * @param array<array<string, mixed>> $result
     */
    protected function assertContentItemsAreOrderedByIdContent(array $result): void
    {
        $contentIds = [];

        foreach ($result as $row) {
            preg_match('/data-id="(\d+)"/', $row[ContentTableConstants::COL_ACTIONS], $matches);
            $contentIds[] = (int)$matches[1];
        }

        $sortedContentIds = $contentIds;
        sort($sortedContentIds);

        $this->assertSame($sortedContentIds, $contentIds);
    }
}
