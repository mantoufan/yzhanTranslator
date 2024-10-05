<?php
namespace YZhanTranslator;
use YZhanGateway\YZhanGateway;

class YZhanTranslator {
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

  public function run(array $content, string $input, ?array $params = array()) {
    if (empty($params['prompt']) === false) {
      $content[0]['text'] .= '\n' . $params['prompt'];
    }

    $content[0]['text'] .= '\n' . $input;

    $params = array_merge(array(
      'method' => 'POST',
      'url' => $this->apiUrl . '/v1/chat/completions',
      'postFields' => array(
        'model' => 'gpt-4o-mini',
        'messages' => array(array('role' => 'user', "content" => $content)),
      ),
    ), $params);

    $cacheParams = $params['cache'] ?? array('type' => 'File', 'params' => array());
    $res = $this->yzhanGateway->cache($cacheParams['cache']['type'] ?? 'File', $cacheParams['params'] ?? array())->request($params);

    $content = null;

    if (empty($res[1]['body']) === false) {
      $body = json_decode($res[1]['body'], true);

      if (empty($body['choices'][0]['message']['content']) === false) {
        $content = $body['choices'][0]['message']['content'];
      }
    }

    return array($content, $params);
  }

  public function translate(string $input, string $language, ?array $params = array()) {
    if (isset($params['type'])) {
      if ($params['type'] === 'json') {
        list($content, $params) = $this->run(array(array("type" => "text", "text" => 'Translate the values of the ' . $params['type'] . ' below into [' . $language . ']. Keep the keys unchanged, and output only the JSON string, without any markdown formatting.')), $input, $params);
      } elseif ($params['type'] === 'images') {
        $images = json_decode($input, true);
        list($content, $params) = $this->run(array_merge(array(
          array("type" => "text", "text" => 'Please describe the image using [' . $language . ']. Return in the format { "' . $images[0] . '": {"description": ""}, ...}. Only include descriptions, without markdown formatting.'),
        ), array_map(fn($image): array=> array("type" => 'image_url', "image_url" => array('url' => $image, "detail" => "high")), $images)), '', $params);
      }
    } else {
      list($content, $params) = $this->run(array(array("type" => "text", "text" => 'Translate the content below into [' . $language . '].')), $input, $params);
    }

    if (empty($content) === false) {
      if (isset($params['type'])) {
        if ($params['type'] === 'json' || $params['type'] === 'images') {
          $res = json_decode($content, true);
          if (empty($res) === false) {
            return $res;
          }
        }
      } else {
        return $content;
      }
    }

    $this->yzhanGateway->getCache()->delete($this->yzhanGateway->getKey($params));
    return null;
  }

  public function detect(string $input, ?array $languages, ?array $params = array()) {
    list($content, $params) = $this->run(array(array("type" => "text", "text" => 'Which of the following languages is the content in: ' . implode(',', $languages) . ', etc.? Output only one language name.')), $input, $params);

    if (empty($content) === false) {
      return $content;
    }

    $this->yzhanGateway->getCache()->delete($this->yzhanGateway->getKey($params));
    return null;
  }

  public function getClient() {
    return $this->client;
  }
}
?>