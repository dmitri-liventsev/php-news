<?php

namespace App\Tests\News\Domain\Service;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\Service\TopArticlesPolicy;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\CategoryTitle;
use App\News\Domain\ValueObject\ShortDescription;
use PHPUnit\Framework\TestCase;

class TopArticlesPolicyTest extends TestCase
{
    public function testMarksFirstNCandidatesAsTopWhenNoneAreTopYet(): void
    {
        $policy = new TopArticlesPolicy(topSize: 3);
        $latest = [$this->makeArticle(), $this->makeArticle(), $this->makeArticle()];

        $policy->apply(currentTop: [], latest: $latest);

        foreach ($latest as $article) {
            $this->assertTrue($article->isTop());
        }
    }

    public function testTakesOnlyFirstNCandidatesWhenMoreAreProvided(): void
    {
        $policy = new TopArticlesPolicy(topSize: 3);
        $candidates = [
            $this->makeArticle(), // 1
            $this->makeArticle(), // 2
            $this->makeArticle(), // 3
            $this->makeArticle(), // 4 — extra
        ];

        $policy->apply([], $candidates);

        $this->assertTrue($candidates[0]->isTop());
        $this->assertTrue($candidates[1]->isTop());
        $this->assertTrue($candidates[2]->isTop());
        $this->assertFalse($candidates[3]->isTop());
    }

    public function testDemotesOldTopsThatLeftTheLatestWindow(): void
    {
        $policy = new TopArticlesPolicy(topSize: 2);

        $oldTop = $this->makeArticle();
        $oldTop->markAsTop();

        $newest = $this->makeArticle();
        $stillTop = $this->makeArticle();
        $stillTop->markAsTop();

        $policy->apply(currentTop: [$oldTop, $stillTop], latest: [$newest, $stillTop]);

        $this->assertTrue($newest->isTop(), 'newest article should become top');
        $this->assertTrue($stillTop->isTop(), 'survivor should stay top');
        $this->assertFalse($oldTop->isTop(), 'older top should be demoted');
    }

    public function testNoOpWhenCurrentTopMatchesLatest(): void
    {
        $policy = new TopArticlesPolicy(topSize: 2);

        $a = $this->makeArticle();
        $a->markAsTop();
        $b = $this->makeArticle();
        $b->markAsTop();

        $updatedAtBefore = $a->getUpdatedAt();

        $policy->apply(currentTop: [$a, $b], latest: [$a, $b]);

        $this->assertTrue($a->isTop());
        $this->assertTrue($b->isTop());
        // markAsTop on an already-top article is a no-op (doesn't touch updatedAt)
        $this->assertSame($updatedAtBefore, $a->getUpdatedAt());
    }

    public function testEmptyLatestDemotesAllCurrentTops(): void
    {
        $policy = new TopArticlesPolicy(topSize: 3);

        $a = $this->makeArticle();
        $a->markAsTop();
        $b = $this->makeArticle();
        $b->markAsTop();

        $policy->apply(currentTop: [$a, $b], latest: []);

        $this->assertFalse($a->isTop());
        $this->assertFalse($b->isTop());
    }

    public function testSizeIsExposed(): void
    {
        $this->assertSame(5, (new TopArticlesPolicy(5))->size());
    }

    private function makeArticle(): Article
    {
        $category = Category::create(new CategoryTitle('cat'));

        return Article::create(
            new ArticleTitle('t'),
            new ShortDescription('s'),
            new ArticleContent('c'),
            null,
            [$category],
        );
    }
}