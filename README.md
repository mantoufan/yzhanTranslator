# yzhanJSONTranslater

JSON language translater using OpenAI with cache. 使用 AI 将 JSON 翻译成不同语言，缓存结果以节省费用

## Features

- **JSON Translation**: Translates JSON object values into multiple languages, while keeping the keys unchanged.
- **OpenAI Integration**: Utilizes OpenAI's API to provide high-quality translations.
- **Configurable Caching**: Supports setting cache types, expiration, and storage options to optimize API usage.
- **Customizable**: Allows configuration of the API client, endpoints, and cache settings.

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

Here’s how you can use **YZhanJSONTranslater** to translate a JSON object with caching settings:

```php
use YZhanJSONTranslater\YZhanJSONTranslater;

$translator = new YZhanJSONTranslater(array(
  'client' => 'OpenAI',
  'apiKey' => 'your-openai-api-key',
  'organization' => 'your-openai-organization',
  'apiUrl' => 'https://api.openai.com',
));

$json = array('hello' => 'hello');
$cache = array(
  'type' => 'File',            // Cache type (e.g., File)
  'params' => array('dir' => '/path/to/cache'), // Cache directory for 'File' type
  'maxAge' => 3600             // Cache expiration time in seconds (1 hour)
);

$translatedJson = $translator->translate($json, 'zh-CN', $cache); // Translates to Simplified Chinese
print_r($translatedJson);
```

### Cache Parameters

The `cache` parameter allows you to control the caching behavior:

- **`type`**: Specifies the cache type. For example, `File` indicates file-based caching.
- **`params`**: An associative array with cache-specific parameters. For `File` caching, this includes the `dir` key, which specifies the directory for storing cached data.
- **`maxAge`**: Defines the cache expiration time in seconds. Once this time passes, the cached data will expire and a new API request will be made.

### Example Cache Configuration

```php
$cache = array(
  'type' => 'File',           // Cache type can be 'File', etc.
  'params' => array('dir' => '/path/to/cache'), // Cache directory
  'maxAge' => 7200            // Cache expiration in seconds (2 hours)
);
```

## Environment Variables

The following environment variables are required to configure OpenAI:

- `OPENAI_APIKEY`: Your OpenAI API key.
- `OPENAI_APIURL`: The base URL for the OpenAI API (default: `https://api.openai.com`).
- `OPENAI_ORGANIZATION`: Your OpenAI organization ID.

Use a `.env` file to load these variables:

```
OPENAI_APIKEY=your-openai-api-key
OPENAI_APIURL=https://api.openai.com
OPENAI_ORGANIZATION=your-openai-organization
```

## Testing

You can run unit tests using PHPUnit:

```bash
composer test
```

To generate a test coverage report:

```bash
composer coverage
```

### Example Test Case

The following test ensures that the translation functionality works as expected:

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
