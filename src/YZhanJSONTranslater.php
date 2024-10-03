<?php
namespace YZhanJSONTranslater;
use YZhanGateway\YZhanGateway;

class YZhanJSONTranslater {
  private $yzhanGateway;
  private $apiUrl;
  private $client;

  public function __construct(?array $params = null) {
    $this->client = $params['client'] ?? 'OpenAI';
    $this->yzhanGateway = new YZhanGateway($this->client, array(
      'apiKey' => $params['apiKey'],
      'organization' => $params['organization'],
    ));
    $this->apiUrl = $params['apiUrl'];
  }

  public function translate(array | object $json, string $language): array | null {
    $res = $this->yzhanGateway->cache()->request(array(
      'method' => 'POST',
      'url' => $this->apiUrl . '/v1/chat/completions',
      'postFields' => array(
        'model' => 'gpt-4o',
        'messages' => array(array('role' => 'system', "content" => 'Translate the values of the JSON object below into [' . $language . '], keep the keys unchanged, and output only the JSON string.\n' . json_encode($json))),
      ),
    ));
    if (!$res[1]['body']) {
      return null;
    }

    $body = json_decode($res[1]['body'], true);
    return json_decode($body['choices'][0]['message']['content'], true);
  }

  public function getClient() {
    return $this->client;
  }
}
?>