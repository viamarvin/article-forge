<?php

namespace viamarvin\ArticleForge;

class ArticleForge 
{
    // API Url
    const API_URL = 'https://af.articleforge.com/api';

	// API key
	private $key = '';

	// String error
	private $error_message = '';

	public function __construct($key = '') {
		$this->setApiKey($key);
	}

	/**
     * Checks the response status, if the error, returns FALSE and writes the error string into a variable
     *
     * @param array server response
     * @return bool
     */
	private function isStatusSuccess($data) {
		$valid = isset($data['status']) && $data['status'] == 'Success';
		if (!$valid) {
			$error_message = !empty($data['error_message']) ? $data['error_message'] : 'Unknown error'; 
			$this->setErrorMessage($error_message);
		}

		return $valid;
	}

	/**
     * Setter $error_message
     *
     * @param string $error
     */
	private function setErrorMessage($error) {
		$this->error_message = $error;
	}

	/**
     * Returns the error
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $error = $af->getLastError();
     * </code>
     *
     * @return string last error
     */
	public function getLastError() {
		return $this->error_message;
	}

	/**
     * Return array with information the usage of your account
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $account = $af->checkUsage();
     * </code>
     *
     * Example output
	 * [
	 *  'status' => 'Success',
	 *  'API Requests' => 0,
	 *  'Words remaining' => 200000,
	 *  'Overuse Protection' => 'NO',
	 *  'Prepaid Amount' => '$20.00'
	 * ];
     *
     * @return array
     */
	public function checkUsage() {
		$result = $this->execute('check_usage');
		if ($this->isStatusSuccess($result)) {
			return $result;
		}

		return false;
	}

	/**
     * Returns in array format all articles in descending order. You can provide an optional parameter limit to limit the number of the returned results.
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $articles = $af->viewArticles();
     * </code>
     *
     * Example output
	 * [
	 *  0 => [
	 *	 'id' => 1,
	 *   'title' => 'Title1',
	 *   'created_at' => '2015-08-23T14:36:17.000Z',
	 *   'spintax' => 'Your Article Spintax',
	 *   'keyword' => 'Your Keyword',
	 *   'sub_keywords': '',
	 *   'quality' => 'custom'
	 *  ],
	 * ...
	 * ];
     *
     * @return array with articles
     */
	public function viewArticles() {
		$result = $this->execute('view_articles');
		if ($this->isStatusSuccess($result)) {
			$articles = !empty($result['data']) ? $result['data'] : [];

			return $articles;
		}

		return false;
	}

	/**
     * Returns the spintax for the article with article_id
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $spintax = $af->viewSpintax(5);
     * </code>
     *
     * @param int $article_id Article ID
     * @return string article spintax
     */
	public function viewSpintax($article_id) {
		$article_id = (int) $article_id;
		if ($article_id == 0) {
			$this->setErrorMessage('Parameter "article_id" is required');
			return false;
		}
		
		$result = $this->execute('view_articles', ['article_id' => $article_id]);
		if ($this->isStatusSuccess($result)) {
			return isset($result['data']) ? $result['data'] : '';
		}

		return false;
	}

	/**
     * Return a spin for the article with article_id
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $spin = $af->viewSpin(5);
     * </code>
     *
     * @param int $article_id Article ID
     * @return string article spin
     */
	public function viewSpin($article_id) {
		$article_id = (int) $article_id;
		if ($article_id == 0) {
			$this->setErrorMessage('Parameter "article_id" is required');
			return false;
		}
		
		$result = $this->execute('view_spin', ['article_id' => $article_id]);
		if ($this->isStatusSuccess($result)) {
			return isset($result['data']) ? $result['data'] : '';
		}

		return false;
	}

	/**
     * Creates an article with keyword 'keyword' with given settings. Returns the spintax for the article. This API call has usage restrictions - 
     * to view the specific limitations for your account visit https://af.articleforge.com/api_info
     *
     * <code>
     *  $params = [
     *   'keyword' => 'Starwars',
 	 *   'sub_keywords' => 'Master Yoda, Darth Vader'
	 *  ];
	 *  
	 *	$article = $af->createArticle($params);
     * </code>
     *
     * @param array $params 
     *	keyword: Word for generation article
	 *	sub_keywords: a list of sub-keywords separated by comma (e.g. subkeyword1,subkeyword2,subkeyword3).  
	 *  sentence_variation:	number of sentence variations. It can be either 1, 2, or 3. The default value is 1.
	 *  paragraph_variation: number of paragraph variations. It can be either 1, 2, or 3. The default value is 1.
	 *  shuffle_paragraphs: enable shuffle paragraphs or not. It can be either 0(disabled) or 1(enabled). The default value is 0.
	 *  length: the length of the article. It can be either ‘very_short’(approximately 50 words), ‘short’(approximately 200 words), 
	 *  		‘medium’(approximately 500 words), or ‘long’(approximately 750 words). The default value is ‘short’.
	 *  title: It can be either 0 or 1. If it is set to be 0, the article generated is without titles and headings. The default value is 0.
	 *  image: the probability of adding an image into the article. It should be a float number from 0.00 to 1.00. The default value is 0.00.
	 *  video: the probability of adding a video into the article. It should be a float number from 0.00 to 1.00. The default value is 0.00.
	 *  auto_links: replace specific keyword within the article with a designated link. You can choose whetherto replace just the first occurrence orall of them. 
	 *				The data structure should be an array following this pattern: [keyword1, url1, all_occurrence?, keyword2, url2, all_occurrence?,...] 
	 *				An example scenario would be: Replace ‘keyword1’ with ‘www.keyword1.com’ (Only first occurrence), Replace keyword2 with ‘www.keyword2.com’ 
	 *				(All occurrences) auto_links shouldbeasfollows: ["keyword1","www.keyword1.com", false, "keyword2", "www.keyword2.com",true]
	 *
	 *  The following parameters are only available when your account is linked to valid WordAi API key. You can go to this URL 
	 *  (https://af.articleforge.com/users/edit) to update your WordAi API key. Overwrite any of these parameters without a valid WordAi API key 
	 *  linked to your account willget anerror.
	 *
	 *  quality: the quality of article. It can be either 1(Regular), 2(Unique), 3(Very Unique), 4(Readable), or 5(Very Readable). The default value is 4.
	 *  regular_spinner: enable regular spinner or not. It can be either 0(disabled) or 1(enabled). The default value is 0.
	 *  turing_spinner: enable turing spinner or not. It can be either 0(disabled) or 1(enabled). The default value is 0. 
	 *					Note: if regular_spinner and turing_spinner are both set to 1, we will use turing spinner.
	 *  rewrite_sentence: enable sentences rewrite or not. It can be either 0(disabled) or 1(enabled). The default value is 0. Note: this will automatically 
	 *					  enable regular spinner if both regular_spinner and turing_spinner are disabled.
	 *  rearrange_sentence: enable add/remove/rearrange sentences or not. It can be either 0(disabled) or 1(enabled). The default value is 0. 
	 *						Note: this will automatically enable regular spinner if both regular_spinner and turing_spinner are disabled.
	 *
     * @return string article
     */
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

	/**
     * Initiates an article with keyword keyword with given settings. Returns number format the ref_key which will be used in getApiProgress method metioned below.
     *
     * <code>
     *  $params = [
     *   'keyword' => 'Starwars',
 	 *   'sub_keywords' => 'Master Yoda, Darth Vader'
	 *  ];
	 *  
	 *	$article = $af->initiateArticle($params);
     * </code>
     *
     * @param array $params 
     *	keyword: Word for generation article
	 *	sub_keywords: a list of sub-keywords separated by comma (e.g. subkeyword1,subkeyword2,subkeyword3).  
	 *  sentence_variation:	number of sentence variations. It can be either 1, 2, or 3. The default value is 1.
	 *  paragraph_variation: number of paragraph variations. It can be either 1, 2, or 3. The default value is 1.
	 *  shuffle_paragraphs: enable shuffle paragraphs or not. It can be either 0(disabled) or 1(enabled). The default value is 0.
	 *  length: the length of the article. It can be either ‘very_short’(approximately 50 words), ‘short’(approximately 200 words), 
	 *  		‘medium’(approximately 500 words), or ‘long’(approximately 750 words). The default value is ‘short’.
	 *  title: It can be either 0 or 1. If it is set to be 0, the article generated is without titles and headings. The default value is 0.
	 *  image: the probability of adding an image into the article. It should be a float number from 0.00 to 1.00. The default value is 0.00.
	 *  video: the probability of adding a video into the article. It should be a float number from 0.00 to 1.00. The default value is 0.00.
	 *  auto_links: replace specific keyword within the article with a designated link. You can choose whetherto replace just the first occurrence orall of them. 
	 *				The data structure should be an array following this pattern: [keyword1, url1, all_occurrence?, keyword2, url2, all_occurrence?,...] 
	 *				An example scenario would be: Replace ‘keyword1’ with ‘www.keyword1.com’ (Only first occurrence), Replace keyword2 with ‘www.keyword2.com’ 
	 *				(All occurrences) auto_links shouldbeasfollows: ["keyword1","www.keyword1.com", false, "keyword2", "www.keyword2.com",true]
	 *
	 *  The following parameters are only available when your account is linked to valid WordAi API key. You can go to this URL 
	 *  (https://af.articleforge.com/users/edit) to update your WordAi API key. Overwrite any of these parameters without a valid WordAi API key 
	 *  linked to your account willget anerror.
	 *
	 *  quality: the quality of article. It can be either 1(Regular), 2(Unique), 3(Very Unique), 4(Readable), or 5(Very Readable). The default value is 4.
	 *  regular_spinner: enable regular spinner or not. It can be either 0(disabled) or 1(enabled). The default value is 0.
	 *  turing_spinner: enable turing spinner or not. It can be either 0(disabled) or 1(enabled). The default value is 0. 
	 *					Note: if regular_spinner and turing_spinner are both set to 1, we will use turing spinner.
	 *  rewrite_sentence: enable sentences rewrite or not. It can be either 0(disabled) or 1(enabled). The default value is 0. Note: this will automatically 
	 *					  enable regular spinner if both regular_spinner and turing_spinner are disabled.
	 *  rearrange_sentence: enable add/remove/rearrange sentences or not. It can be either 0(disabled) or 1(enabled). The default value is 0. 
	 *						Note: this will automatically enable regular spinner if both regular_spinner and turing_spinner are disabled.
	 *
     * @return int the ref_key which will be used in getApiProgress
     */
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

	/**
     * Return the percentage of completion
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $progress = $af->getApiProgress(1234);
     * </code>
     *
     * @param int $ref_key ref ID in ArticleForge
     * @return int the percentage of completion, range 0-100
     */
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

	/**
     * Returns the spintax of the article identified by ref_key
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $article = $af->getApiArticleResult(1234);
     * </code>
     *
     * @param int $ref_key ref ID in ArticleForge
     * @return string article text
     */
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

	/**
     * Sets the value of the API key
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $af->setApiKey('API key');
     * </code>
     *
     * @param string $key API key
     */
	public function setApiKey($key) {
		$this->key = $key;
	}

	/**
     * Returns API key
     *
     * <code>
     * $af = new viamarvin\ArticleForge\ArticleForge($apiKey);
     * $key = $af->getApiKey();
     * </code>
     *
     * @return string API key
     */
	public function getApiKey() {
		return $this->key;
	}

	/**
     * Executes the query on the server
     *
     * @param string $method Method API
     * @param array $params Params for method
     * @return array returns the response from the server
     */
	private function execute($method, $params = []) {
		$params['key'] = $this->getApiKey();
        if (empty($params['key'])) {
            $this->setErrorMessage('API key is required');
            return false;
        }

		$ch = curl_init(self::API_URL . '/' . $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $params));

		$result = curl_exec($ch);
		curl_close($ch);

		return json_decode($result, true);
	}
}