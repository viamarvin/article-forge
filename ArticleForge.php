<?php

namespace viamarvin\ArticleForge;

class ArticleForge 
{
	private $key = '';
	private $error_message = '';

	public __construct($key = '') {
		$this->setApiKey($key);
	}

	private function isStatusSuccess($data) {
		$valid = isset($data['status']) && $data['status'] == 'Success';
		if (!$valid) {
			$error_message = !empty($data['error_message']) ? $data['error_message'] : 'Unknown error'; 
			$this->setErrorMessage($error_message);
		}

		return $valid;
	}

	private function setErrorMessage($error) {
		$this->error_message = $error;
	}

	public function getLastError() {
		return $this->error_message;
	}

	public function checkUsage() {
		$result = $this->execute('check_usage');
		if ($this->isStatusSuccess($result)) {
			return $result;
		}

		return false;
	}

	public function viewArticles() {
		$result = $this->execute('view_articles');
		if ($this->isStatusSuccess($result)) {
			$articles = !empty($result['data']) ? $result['data'] : [];

			return $articles;
		}

		return false;
	}

	public function viewSpintax($article_id) {
		$article_id = (int) $article_id;
		if ($article_id == 0) {
			$this->setErrorMessage('Parameter "article_id" is required');
			return false;
		}
		
		$result = $this->execute('view_articles', ['article_id' => $artcile_id]);
		if ($this->isStatusSuccess($result)) {
			return isset($result['data']) ? $result['data'] : '';
		}

		return false;
	}

	public function viewSpin($article_id) {
		$article_id = (int) $article_id;
		if ($article_id == 0) {
			$this->setErrorMessage('Parameter "article_id" is required');
			return false;
		}
		
		$result = $this->execute('view_spin', ['article_id' => $artcile_id]);
		if ($this->isStatusSuccess($result)) {
			return isset($result['data']) ? $result['data'] : '';
		}

		return false;
	}

	public function createArticle($params) {
		if (empty($params['keyword'])) {
			$this->setErrorMessage('Parameter "keyword" is required');
			return false;
		}

		$result = $this->execute('create_article', $params);
		if ($this->isStatusSuccess($result)) {
			return isset($result['article']) ? $result['article'] : '';
		}

		return false;
	}

	public function initiateArticle($params) {
		if (empty($params['keyword'])) {
			$this->setErrorMessage('Parameter "keyword" is required');
			return false;
		}

		$result = $this->execute('initiate_article', $params);
		if ($this->isStatusSuccess($result)) {
			return isset($result['ref_key']) ? (int) $result['ref_key'] : 0;
		}

		return false;
	}

	public function getApiProgress($ref_key) {
		$ref_key = (int) $ref_key;
		if ($ref_key == 0) {
			$this->setErrorMessage('Parameter "ref_key" is required');
			return false;
		}

		$result = $this->execute('get_api_progress', ['ref_key' => $ref_key]);
		if ($this->isStatusSuccess($result)) {
			if (!isset($result['api_status'])) {
				$result['api_status'] = 0;
			}

			switch ($result['api_status']) {
				case 0:
					$progress = 0;
					break;
				case 201:
					$progress = 100; 	
					break;
				default:
					$progress = isset($result['progress']) ? floatval($result['progress']) * 100 : 0;
			}
			
			return $progress;
		}

		return false;
	}

	public function getApiArticleResult($ref_key) {
		$ref_key = (int) $ref_key;
		if ($ref_key == 0) {
			$this->setErrorMessage('Parameter "ref_key" is required');
			return false;
		}

		$result = $this->execute('get_api_article_result', ['ref_key' => $ref_key]);
		if ($this->isStatusSuccess($result)) {
			return isset($result['article']) ? $result['article'] : '';
		}

		return false;
	}

	public function setApiKey($key) {
		$this->key = $key;
	}

	public function getApiKey() {
		return $this->key;
	}

	private function execute($method, $params = []) 
	{
		$params['key'] = $this->getApiKey();

		$ch = curl_init($this->apiUrl . '/' . $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $params));

		$result = curl_exec($ch);
		$curl_close($ch);

		return json_decode($result, true);
	}
}