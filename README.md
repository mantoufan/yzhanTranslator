# YZhanTranslator

YZhanTranslator is a PHP library for translating content using OpenAI's language models with caching capabilities. It supports translating strings, JSON objects and images arrays, as well as language detection.  
YZhanTranslator can automatically cache (configurable) results to save costs.

## Features

- Translate strings and JSON objects
- Detect language of input text (including JSON content)
- Translate images by providing URLs and receiving descriptions
- Cache translation results to save costs
- Customizable prompts for fine-tuned translations
- Built on top of YZhanGateway for flexible API interactions

## Requirements

- PHP 5.4 or higher
- OpenAI API key
- Composer for dependency management

## Installation

Install YZhanTranslator using Composer:

```bash
composer require mantoufan/yzhantranslator
```

## Usage

### Basic Translation

```php
use YZhanTranslator\YZhanTranslator;

$translator = new YZhanTranslator([
    'client' => 'OpenAI',
    'apiKey' => 'your_openai_api_key',
    'apiUrl' => 'https://api.openai.com',
    'organization' => 'your_openai_organization_id',
]);

$result = $translator->translate('Hello, world!', 'zh-CN');
echo $result; // 你好，世界！
```

### JSON Translation

```php
$json = json_encode(['greeting' => 'Hello']);
$result = $translator->translate($json, 'zh-CN', ['type' => 'json']);
print_r($result); // ['greeting' => '你好']
```

### Language Detection

```php
$languages = ['en', 'zh-CN', 'zh-TW', 'jp'];
$detectedLang = $translator->detect('こんにちは', $languages);
echo $detectedLang; // jp
```

### Language JSON Detection

```php
$json = json_encode(['greeting' => 'こんにちは']);
$languages = ['en', 'zh-CN', 'zh-TW', 'jp'];
$detectedLang = $translator->detect($json, $languages);
echo $detectedLang; // jp
```

### Custom Prompts

```php
$json = json_encode(['k' => '你好']);
$prompt = 'If there is a key named \'k\', retain the original value, but add a new key \'k2\' at the same level, containing the translated value.';
$result = $translator->translate($json, 'en', [
    'type' => 'json',
    'prompt' => $prompt
]);
print_r($result); // ['k' => '你好', 'k2' => 'Hello']
```

### Images Descriptions

You can now translate images by providing an array of image URLs.  
The translator will return descriptions for each image in the specified language.

```php
use YZhanTranslator\YZhanTranslator;

$translator = new YZhanTranslator([
    'client' => 'OpenAI',
    'apiKey' => 'your_openai_api_key',
    'apiUrl' => 'https://api.openai.com',
    'organization' => 'your_openai_organization_id',
]);

$images = [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
];
$result = $translator->translate(json_encode($images), 'zh-CN', ['type' => 'images']);
print_r($result); // ['https://example.com/image1.jpg' => ['description' => ''], 'https://example.com/image2.jpg' => ['description' => '']]
```

## Configuration

YZhanTranslator uses environment variables for configuration. Create a `.env` file in your project root with the following contents:

```
OPENAI_APIKEY=your_openai_api_key
OPENAI_APIURL=https://api.openai.com
OPENAI_ORGANIZATION=your_openai_organization_id
```

## Parameters

The `translate()` and `detect()` methods accept an optional `$params` array for additional configuration:

- `prompt`: (string) Additional prompt to customize results, e.g., 'do not translate the href attribute'
- `cache`: (array) Caching options
  - `type`: (string) Cache type (default is 'File')
  - `params`: (array) Cache-specific parameters
    - `dir`: (string) Directory for file-based caching
  - `maxAge`: (int) Cache expiration time in seconds
- `timeout`: (int) Request timeout in seconds (default is 6 seconds)

Example usage with parameters:

```php
$result = $translator->translate('Hello, world!', 'zh-CN', [
    'prompt' => 'Translate in a formal tone',
    'cache' => [
        'type' => 'File',
        'params' => ['dir' => '/tmp/cache'],
        'maxAge' => 3600
    ],
    'timeout' => 10
]);
```

## Testing

Run the test suite using PHPUnit:

```bash
composer test
```

Generate a code coverage report:

```bash
composer coverage
```

## License

This project is open-sourced software licensed under the MIT license.

## Author

Shon Wu - [GitHub](https://github.com/mantoufan)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
