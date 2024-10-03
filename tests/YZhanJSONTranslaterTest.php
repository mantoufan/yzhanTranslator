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
      array(array('hello' => 'hello'), 'jp', array('hello' => 'こんにちは')),
      array(array('hello' => '你好'), 'en', array('hello' => 'hello')),
    );
  }
  /**
   * @depends testConstruct
   * @dataProvider dataProvider
   */
  public function testSet(array | object $json, string $language, array $translatedJson, &$yzhanJSONTranslater) {
    $this->assertEquals($yzhanJSONTranslater->translate($json, $language), $translatedJson);
  }
}
?>