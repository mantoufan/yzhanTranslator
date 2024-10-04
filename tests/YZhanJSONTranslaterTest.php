<?php
use PHPUnit\Framework\TestCase;
use YZhanJSONTranslater\YZhanJSONTranslater;

class YZhanJSONTranslaterTest extends TestCase {
  public function testConstruct() {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    $yzhanJSONTranslater = new YZhanJSONTranslater(array(
      'client' => 'OpenAI',
      'apiKey' => $_ENV['OPENAI_APIKEY'],
      'apiUrl' => $_ENV['OPENAI_APIURL'],
      'organization' => $_ENV['OPENAI_ORGANIZATION'],
    ));
    $this->assertEquals($yzhanJSONTranslater->getClient(), 'OpenAI');
    return $yzhanJSONTranslater;
  }
  public function dataProvider(): array {
    return array(
      array(array('hello' => 'hello'), 'zh-CN', array('hello' => '你好')),
      array(array('hello' => 'hello'), 'zh-TW', array('hello' => '你好')),
      array(array('hello' => '你好'), 'en', array('hello' => 'Hello')),
    );
  }
  public function dataProviderWithPrompt(): array {
    return array(
      array(array('k' => '你好'), 'en', array('k' => '你好', 'k2' => 'Hello')),
      array(array(array('k' => '你好')), 'en', array(array('k' => '你好', 'k2' => 'Hello'))),
    );
  }
  /**
   * @depends testConstruct
   * @dataProvider dataProvider
   */
  public function testSet(array $json, string $language, array $translatedJson, &$yzhanJSONTranslater) {
    $this->assertEquals($yzhanJSONTranslater->translate($json, $language), $translatedJson);
  }

  /**
   * @depends testConstruct
   * @dataProvider dataProvider
   */
  public function testSetPrompt(array $json, string $language, array $translatedJson, &$yzhanJSONTranslater) {
    $prompt = 'If there is a key named \'k\', retain the original value, but add a new key \'k2\' at the same level, containing the translated value. ';
    $this->assertEquals($yzhanJSONTranslater->translate($json, $language, array('prompt' => $prompt)), $translatedJson);
  }
}
?>