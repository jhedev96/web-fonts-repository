<?php
// Application middleware

session_start();

$app->add(new \Slim\Csrf\Guard);


$app->get('/test', function ($req, $res, $args) {
    // CSRF token name and value
    $nameKey = $this->csrf->getTokenNameKey();
    $valueKey = $this->csrf->getTokenValueKey();
    $name = $req->getAttribute($nameKey);
    $value = $req->getAttribute($valueKey);

    // render a form
    $html = <<<EOT
<!DOCTYPE html>
<html>
<head><title>/test</title></head>
<body>
    <form method="post" action="/process">
        <input type="hidden" name="$nameKey" value="$name">
        <input type="hidden" name="$valueKey" value="$value">
        <input type="text" name="name" placeholder="name">
        <input type="submit" value="test">
    </form>
</body>
</html>
EOT;

    return $res->write($html);
});