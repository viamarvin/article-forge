<?php

include('../vendor/autoload.php');
include('../ArticleForge.php');

const API_KEY = '';

class ArticleForgeTest extends PHPUnit_Framework_TestCase {
    private $af;
    private $ref_key;
    private $article_id;

    protected function setUp() {
        $this->af = new \viamarvin\ArticleForge\ArticleForge();
        $this->ref_key = NULL;
        $this->article_id = NULL;
    }

    protected function tearDown() {
        $this->af = NULL;
        $this->ref_key = NULL;
        $this->article_id = NULL;
    }

    public function testSetApiKey() {
        $this->af->setApiKey(API_KEY);

        $result = $this->af->getApiKey();
        $this->assertEquals(API_KEY, $result);
    }

    public function testCheckUsage() {
        $result = $this->af->checkUsage();

        $this->assertTrue($result);
        $this->assertInternalType('array', $result);
    }

    public function testViewArticles() {
        $result = $this->af->viewArticles();

        $this->assertTrue($result);
        $this->assertInternalType('array', $result);
    }

    public function testViewSpintax() {
        $result = $this->af->viewSpintax($this->article_id);

        $this->assertTrue($result);
        $this->assertInternalType('string', $result);
    }

    public function testViewSpin() {
        $result = $this->af->viewSpin($this->article_id);

        $this->assertTrue($result);
        $this->assertInternalType('string', $result);
    }

    public function testCreateArticle() {
        $params = ['keyword' => 'BMW car', 'sub_keywords' => 'bmw,cars,test drive bmw'];
        $result = $this->af->createArticle($params);

        $this->assertTrue($result);
        $this->assertInternalType('string', $result);
    }

    public function testInitiateArticle() {
        $params = ['keyword' => 'BMW car', 'sub_keywords' => 'bmw,cars,test drive bmw'];
        $result = $this->af->initiateArticle($params);

        $this->assertTrue($result);
        $this->assertInternalType('string', $result);

        $this->ref_key = $result;
    }

    public function testGetApiProgress() {
        $count_iteration = 20;
        $result = false;

        for ($i = 0; $i <= $count_iteration; $i++) {
            $response = $this->af->getApiProgress($this->ref_key);
            $this->assetTrue($response);

            $status = (int) $response;
            if ($status == 100) {
                $result = true;
                break;
            }

            sleep(30);
        }

        $this->assetTrue($result);
    }

    public function testGetApiArticleResult() {
        $result = $this->af->getApiProgress($this->ref_key);
        $this->assetTrue($result);
        $this->assertInternalType('string', $result);
    }
}