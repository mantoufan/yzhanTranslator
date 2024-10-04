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

  public function translate(array $json, string $language, ?array $params = array()) {
    $content = 'Translate the values of the JSON object below into [' . $language . ']. Keep the keys unchanged, and output only the JSON string, without any markdown formatting. ';
    if (empty($params['prompt']) === false) {
      $content .= $params['prompt'];
    }
    $content .= json_encode($json, JSON_UNESCAPED_UNICODE);

    $params = array_merge(array(
      'method' => 'POST',
      'url' => $this->apiUrl . '/v1/chat/completions',
      'postFields' => array(
        'model' => 'gpt-4o-mini',
        'messages' => array(array('role' => 'system', "content" => $content)),
      ),
    ), $params);

    $res = $this->yzhanGateway->cache($params['type'] ?? 'File', $params['params'] ?? array())->request($params);

    if (empty($res[1]['body']) === false) {
      $body = json_decode($res[1]['body'], true);

      if (empty($body['choices'][0]['message']['content']) === false) {
        $res = json_decode($body['choices'][0]['message']['content'], true);

        if (empty($res) === false) {
          return $res;
        }
      }
    }

    $this->yzhanGateway->getCache()->delete($this->yzhanGateway->getKey($params));
    return array();
  }

  public function getClient() {
    return $this->client;
  }
}
?>