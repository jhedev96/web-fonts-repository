<?php

namespace Src\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Src\Services\WebfontCSSApiGenerator\Exceptions\InvalidSettingsException;
use Src\Services\WebfontCSSApiGenerator\WebfontCSSApiGenerator;

/**
 * Class CSSApiGeneratorController
 *
 * The controller for generating webfonts CSS files.
 *
 * @author JheDev96
 * @package Src\Controllers
 */
class CSSApiGeneratorController
{
    /**
     * @var ContainerInterface Dependencies container
     */
    protected $container;

    /**
     * @param ContainerInterface $container Dependencies container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Runs the controller action.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Getting and checking the request data
        $requestParams = $request->getQueryParams();
        if (!isset($requestParams['family'])) {
            return $this->createErrorResponse('The `family` query parameter is not set');
        }

        try {
            $webfontCSSApiGenerator = $this->container->get('webfontCSSApiGenerator');
        } catch (InvalidSettingsException $error) {
            $this->container->get('logger')->error($error);
            return $this->createErrorResponse('The app settings are invalid: '.$error->getMessage(), 500);
        }
        try {
            $cssCode = $webfontCSSApiGenerator->buildCss(function($e) {
                //error_reporting(0);
            });
        } catch (\InvalidArgumentException $error) {
            return $this->createErrorResponse($error->getMessage());
        }

        // Sending the response
        $httpCacheTime = round($this->container->get('settings')['cssHttpCacheAge']);
        return $response
            ->withHeader('Content-Type', 'text/css; charset=UTF-8')
            ->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->withHeader('Cache-Control', $httpCacheTime > 0 ? 'max-age='.$httpCacheTime.', public' : 'no-cache')
            ->withHeader('Pragma', $httpCacheTime > 0 ? 'public' : 'no-cache')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->write($cssCode);
    }

    /**
     * Creates a response with the client side error message.
     *
     * @param string $message Error message for the client
     * @param int $status HTTP status code
     * @return ResponseInterface
     *
     * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes HTTP status codes
     */
    protected function createErrorResponse(string $message, int $status = 422): ResponseInterface
    {
        return $this->container->get('response')
            ->withStatus($status)
            ->write($message);
    }
}
