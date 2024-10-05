<?php
use PHPUnit\Framework\TestCase;
use YZhanTranslator\YZhanTranslator;

class YZhanTranslatorTest extends TestCase {
  private $languages = array('en', 'zh-CN', 'zh-TW', 'jp');

  public function testConstruct() {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    $yzhanTranslator = new YZhanTranslator(array(
      'client' => 'OpenAI',
      'apiKey' => $_ENV['OPENAI_APIKEY'],
      'apiUrl' => $_ENV['OPENAI_APIURL'],
      'organization' => $_ENV['OPENAI_ORGANIZATION'],
    ));
    $this->assertEquals($yzhanTranslator->getClient(), 'OpenAI');
    return $yzhanTranslator;
  }

  public function dataProviderString(): array {
    return array(
      array('hello', 'zh-CN', '你好', 'en'),
      array('你好', 'en', 'Hello', 'zh-CN'),
    );
  }

  /**
   * @depends testConstruct
   * @dataProvider dataProviderString
   */
  public function testTranslateString(string $string, string $language, string $translatedString, string $_, &$yzhanTranslator) {
    $this->assertEquals($yzhanTranslator->translate($string, $language), $translatedString);
  }

  /**
   * @depends testConstruct
   * @dataProvider dataProviderString
   */
  public function testDetectString(string $string, string $_, string $__, string $detectedString, &$yzhanTranslator) {
    $this->assertEquals($yzhanTranslator->detect($string, $this->languages), $detectedString);
  }

  public function dataProviderJson(): array {
    return array(
      array(array('hello' => 'hello'), 'zh-CN', array('hello' => '你好'), 'en'),
      array(array('hello' => 'hello'), 'zh-TW', array('hello' => '你好'), 'en'),
      array(array('hello' => '你好'), 'en', array('hello' => 'Hello'), 'zh-CN'),
    );
  }

  /**
   * @depends testConstruct
   * @dataProvider dataProviderJson
   */
  public function testTranslateJson(array $ar, string $language, array $translatedJson, string $_, &$yzhanTranslator) {
    $this->assertEquals($yzhanTranslator->translate(json_encode($ar, JSON_UNESCAPED_UNICODE), $language, array('type' => 'json')), $translatedJson);
  }

  /**
   * @depends testConstruct
   * @dataProvider dataProviderJson
   */
  public function testDetectJson(array $ar, string $_, array $__, string $detectedString, &$yzhanTranslator) {
    $this->assertEquals($yzhanTranslator->detect(json_encode($ar, JSON_UNESCAPED_UNICODE), $this->languages), $detectedString);
  }

  public function dataProviderJsonAndPrompt(): array {
    return array(
      array(array('k' => '你好'), 'en', array('k' => '你好', 'k2' => 'Hello'), 'zh-CN'),
    );
  }

  /**
   * @depends testConstruct
   * @dataProvider dataProviderJsonAndPrompt
   */
  public function testTranslateJsonAndPrompt(array $ar, string $language, array $translatedJson, string $_, &$yzhanTranslator) {
    $prompt = 'If there is a key named \'k\', retain the original value, but add a new key \'k2\' at the same level, containing the translated value. ';
    $this->assertEquals($yzhanTranslator->translate(json_encode($ar, JSON_UNESCAPED_UNICODE), $language, array('type' => 'json', 'prompt' => $prompt)), $translatedJson);
  }

  /**
   * @depends testConstruct
   * @dataProvider dataProviderJsonAndPrompt
   */
  public function testDetectJsonAndPrompt(array $ar, string $_, array $__, string $detectedString, &$yzhanTranslator) {
    $prompt = 'If there is a key named \'k\', retain the original value, but add a new key \'k2\' at the same level, containing the translated value. ';
    $this->assertEquals($yzhanTranslator->detect(json_encode($ar, JSON_UNESCAPED_UNICODE), $this->languages, array('prompt' => $prompt)), $detectedString);
  }
}
?>