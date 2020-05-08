<?php
/**
 * Created by PhpStorm.
 * User: yudsuzuk
 * Date: 2020/05/08
 * Time: 14:35
 */
$url = getenv("TARGET_URL");
$homepage = file_get_contents($url);

if (file_exists('index.html')) {
    $before_hash = hash_file('md5', 'index.html');

    if ($before_hash === md5($homepage)) {
        return;
    }
    send_to_slack();
}
file_put_contents('index.html', $homepage);


function send_to_slack() {
    $webhook_url = getenv("SLACK_WEBHOOK_URL");
    $content = [
        'channel' => getenv("SLACK_CHANNEL"),
        'text' => "指定のページに変更がありました。 => " . getenv("TARGET_URL"),
    ];
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($content),
        ]
    ];
    $response = file_get_contents($webhook_url, false, stream_context_create($options));
    return $response === 'ok';
}
