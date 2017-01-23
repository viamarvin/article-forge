# ArticleForge API

PHP Library for ArticleForge API.

Support API
  - check_usage
  - view_articles
  - view_spintax
  - view_spin
  - create_article
  - initiate_article
  - get_api_progress
  - get_api_article_result

### Installation
Download and extract the [latest release](https://github.com/viamarvin/ArticleForge/releases).

### How to use
```php
$af = new viamarvin\ArticleForge\ArticleForge($apiKey);
```
### Check usage
Return array with information the usage of your account

```php
$account = $af->checkUsage();

// Example output
// $account = [
//	'status' => 'Success',
//	'API Requests' => 0,
//	"Words remaining" => 200000,
//	"Overuse Protection" => "NO",
//	"Prepaid Amount" => "$20.00"
// ];
```

### View Articles
Returns in array format all articles in descending order. You can provide an optional parameter limit to limit the number of the returned results.

```php
$articles = $af->viewArticles();

// Example output
// $articles = [
//  0 => [
//    'id' => 1,
//    'title' => 'Title1',
//    'created_at' => '2015-08-23T14:36:17.000Z',
//    'spintax' => 'Your Article Spintax',
//    'keyword' => 'Your Keyword',
//    'sub_keywords': '',
//    'quality' => 'custom'
//  ],
//  ...
// ];
```

### View spintax
Returns the spintax for the article with article_id

```php
$spintax = $af->viewArticle(5);

// Example output
// $spintax = 'Your Article Spintax'
```

### View spin
Return a spin for the article with article_id

```php
$spin = $af->viewSpin(5);

// Example output
// $spin = 'Your Article Spin'
```

### Create Article
Creates an article with keyword 'keyword' with given settings. Returns the spintax for the article. This API call has usage restrictions - to view the specific limitations for your account visit https://af.articleforge.com/api_info

```php
$params = [
  'keyword' => 'Starwars',
  'sub_keywords' => 'Master Yoda, Darth Vader'
];

$article = $af->createArticle($params);

// Example output
// $article = 'Your Article Spintax';
```

#### Params
Param | Description
------|------------
keyword | Word for generation article
sub_keywords | a list of sub-keywords separated by comma (e.g. subkeyword1,subkeyword2,subkeyword3).
sentence_variation | number of sentence variations. It can be either 1, 2, or 3. The default value is 1.
paragraph_variation | number of paragraph variations. It can be either 1, 2, or 3. The default value is 1.
shuffle_paragraphs | enable shuffle paragraphs or not. It can be either 0(disabled) or 1(enabled). The default value is 0.
length | the length of the article. It can be either ‘very_short’(approximately 50 words), ‘short’(approximately 200 words), ‘medium’(approximately 500 words), or ‘long’(approximately 750 words). The default value is ‘short’.
title | It can be either 0 or 1. If it is set to be 0, the article generated is without titles and headings. The default value is 0.
image | the probability of adding an image into the article. It should be a float number from 0.00 to 1.00. The default value is 0.00.
video | the probability of adding a video into the article. It should be a float number from 0.00 to 1.00. The default value is 0.00.
auto_links | replace specific keyword within the article with a designated link. You can choose whetherto replace just the first occurrence orall of them. The data structure should be an array following this pattern: [keyword1, url1, all_occurrence?, keyword2, url2, all_occurrence?,...] An example scenario would be: Replace ‘keyword1’ with ‘www.keyword1.com’ (Only first occurrence), Replace keyword2 with ‘www.keyword2.com’ (All occurrences)
auto_links shouldbeasfollows: ["keyword1","www.keyword1.com", false, "keyword2", "www.keyword2.com",true]

The following parameters are only available when your account is linked to valid WordAi API key. You can go to this URL (https://af.articleforge.com/users/edit) to update your WordAi API key. Overwrite any of these parameters without a valid WordAi API key linked to your account willget anerror.

Param | Description
------|------------
quality | the quality of article. It can be either 1(Regular), 2(Unique), 3(Very Unique), 4(Readable), or 5(Very Readable). The default value is 4.
regular_spinner | enable regular spinner or not. It can be either 0(disabled) or 1(enabled). The default value is 0.
turing_spinner | enable turing spinner or not. It can be either 0(disabled) or 1(enabled). The default value is 0. Note: if regular_spinner and turing_spinner are both set to 1, we will use turing spinner.
rewrite_sentence | enable sentences rewrite or not. It can be either 0(disabled) or 1(enabled). The default value is 0. Note: this will automatically enable regular spinner if both regular_spinner and turing_spinner are disabled.
rearrange_sentence | enable add/remove/rearrange sentences or not. It can be either 0(disabled) or 1(enabled). The default value is 0. Note: this will automatically enable regular spinner if both regular_spinner and turing_spinner are disabled.

### Initial article
Initiates an article with keyword **keyword** with given settings. Returns number format the **ref_key** which will be used in getApiProgress method metioned below.

```php
$params = [
  'keyword' => 'Starwars',
  'sub_keywords' => 'Master Yoda, Darth Vader'
];

$ref_key = $af->initialArticle($params);

// Example output
// $ref_key = 1234
```

**Params same as createArticle**


### Get status progress
Return progress generation new article

```php
$progress = $af->getApiProgress(1234);

// Example output
// $progress = 95
```

### Get Api Article Result
Returns the article id and the spintax of the article identified by ref_key

```php
$article = $af->getApiArticleResult(1234);

// Example output
// $article = 'Your Article Spintax'
```

### Get Last Error
In the case of methods of error returned FALSE, the error text can be found referring to the method getLastError. Return error message.

```php
$af = new viamarvin\ArticleForge\ArticleForge($apiKey);
$progress = $af->getApiProgress();

if (!$progress) {
  print $af->getLastError();
}

// Example output
// Parameter "ref_key" is required
```

### Todos
 - Write Tests