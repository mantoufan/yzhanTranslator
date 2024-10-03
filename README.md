# yzhanJSONTranslater

JSON language translater using OpenAI with cache. 使用 AI 将 JSON 翻译成不同语言，缓存结果以节省费用

## Features

- **JSON Translation**: Translate values of JSON objects into multiple languages while keeping the keys unchanged.
- **OpenAI Integration**: Leverages OpenAI’s GPT-4 model for high-quality translations.
- **Configurable Caching**: Support for file-based caching, allowing translations to be cached to reduce API calls.
- **Request Timeout**: Allows users to configure request timeout settings to manage API response time.

## Installation

1. Install the library via Composer:

```bash
composer require mantoufan/yzhanjsontranslater
```

2. Install development dependencies for testing purposes:

```bash
composer install --dev
```

## Usage

### Basic Usage Example

Here’s how you can use **YZhanJSONTranslater** to translate a JSON object with caching and timeout settings:

```php
use YZhanJSONTranslater\YZhanJSONTranslater;

$translator = new YZhanJSONTranslater(array(
  'client' => 'OpenAI',
  'apiKey' => 'your-openai-api-key',
  'organization' => 'your-openai-organization',
  'apiUrl' => 'https://api.openai.com',
));

$json = array('hello' => 'hello');
$params = array(
  'type' => 'File',                     // Optional: Cache type ('File' by default)
  'params' => array('dir' => '/path/to/cache'), // Optional: Cache directory for file-based caching
  'cache' => array('maxAge' => 3600),   // Optional: Cache expiration in seconds
  'timeout' => 10,                      // Optional: Set request timeout in seconds (default: 6s)
);

$translatedJson = $translator->translate($json, 'zh-CN', $params); // Translate to Simplified Chinese
print_r($translatedJson);
```

### Parameters for `translate` Function

The `translate` function accepts the following arguments:

- **`$json`**: The JSON object to translate (array format).
- **`$language`**: Target language for translation (e.g., `'zh-CN'` for Simplified Chinese).
- **`$params`**: (Optional) Array of options for caching and request customization:
  - **`type`**: Cache type (default is `'File'`).
  - **`params`**: Cache-specific parameters (e.g., directory for file-based caching).
  - **`cache`**: Array containing cache configuration options:
    - **`maxAge`**: Cache expiration time in seconds.
  - **`timeout`**: Request timeout in seconds (default is 6 seconds).

### Example of Caching Configuration

Here’s an example that sets up file-based caching with a cache expiration of 1 hour:

```php
$params = array(
  'type' => 'File',                     // Cache type (File-based)
  'params' => array('dir' => '/path/to/cache'), // Directory for cache files
  'cache' => array('maxAge' => 3600),   // Cache expiration time: 1 hour
  'timeout' => 10,                      // API request timeout: 10 seconds
);

$translatedJson = $translator->translate($json, 'zh-CN', $params);
```

## Environment Variables

The library relies on the following environment variables to configure OpenAI:

- **`OPENAI_APIKEY`**: Your OpenAI API key.
- **`OPENAI_APIURL`**: OpenAI API base URL (default: `https://api.openai.com`).
- **`OPENAI_ORGANIZATION`**: Your OpenAI organization ID.

Use a `.env` file to provide these environment variables:

```
OPENAI_APIKEY=your-openai-api-key
OPENAI_APIURL=https://api.openai.com
OPENAI_ORGANIZATION=your-openai-organization
```

## Testing

The library uses PHPUnit for testing. To run the tests:

```bash
composer test
```

To generate a test coverage report:

```bash
composer coverage
```

### Example Test Case

```php
public function dataProvider(): array {
  return array(
    array(array('hello' => 'hello'), 'zh-CN', array('hello' => '你好')),
    array(array('hello' => 'hello'), 'zh-TW', array('hello' => '你好')),
    array(array('hello' => 'hello'), 'jp', array('hello' => 'こんにちは')),
    array(array('hello' => '你好'), 'en', array('hello' => 'hello')),
  );
}
```

## Requirements

- PHP >= 5.4
- OpenAI API key
- Composer

## License

This project is licensed under the MIT License. See the [LICENSE](https://opensource.org/licenses/MIT) file for details.

## Contribution

Feel free to contribute by submitting issues or pull requests.
