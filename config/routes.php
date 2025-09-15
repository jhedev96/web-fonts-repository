<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;

/*
 * Routes
 *
 * Docs: https://www.slimframework.com/docs/objects/router.html
 */

// CSS icons generator
$app->get('/icon', \Src\Controllers\CSSIconGeneratorController::class)->setName('cssIconGenerator');

// CSS generator
$app->get('/css', \Src\Controllers\CSSGeneratorController::class)->setName('cssGenerator');

// CSS api generator
$app->get('/api/css', \Src\Controllers\CSSApiGeneratorController::class)->setName('cssApiGenerator');

// Index page
$app->get('/', function (RequestInterface $request, ResponseInterface $response) use ($app) {
    return $this->renderer->render($response, 'index.phtml', [
        'cssUrl' => $app->getContainer()->get('router')->pathFor('cssGenerator'),
        'cssApiUrl' => $app->getContainer()->get('router')->pathFor('cssApiGenerator'),
        'fontsList' => (new Container(['settings' => require __DIR__ . '/settings.php']))->get('settings')['fonts']
    ]);
});


$app->get('/'.(new Container(['settings' => require __DIR__ . '/settings.php']))->get('settings')['directory_alias']['fonts'].'/{path}/{filename}.{extension}', function(Request $request, Response $response, array $args) use ($app) {
    $fonts = "fonts/{$args['path']}/{$args['filename']}.{$args['extension']}";
    return download($fonts);
});

$app->get('/'.(new Container(['settings' => require __DIR__ . '/settings.php']))->get('settings')['directory_alias']['icons'].'/{path}/{filename}.{extension}', function(Request $request, Response $response, array $args) use ($app) {
    $icons = "icons/{$args['path']}/{$args['filename']}.{$args['extension']}";
    return download($icons);
});



/**
 * Downloads a file
 * Pretty self-explanatory.
 */
function download($file) {
    if (!file_exists($file)) {
        header("HTTP/1.1 400 Invalid Request");
        die("<h3>File ".basename($file)." Not Found</h3>");
        exit;
    }
    // Determine the mimetype
    $finfo = @finfo_open(FILEINFO_MIME);
    if (!$finfo) {
        return false;
    }
    $type = @finfo_file($finfo, $file);
    $type = @reset(explode(';', $type));
    @finfo_close($finfo);

    @ob_clean();
    header('Content-Type:' . $type);
    header('Content-Disposition: filename="' . basename($file) . '";');
    header('Content-Description: File Transfer');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    @flush();
    @readfile($file);
    @usleep(0);

    exit;
}