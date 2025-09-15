<?php

namespace Src\Services\WebfontCSSApiGenerator;

class WebfontCSSApiGenerator
{

    const BASE_PATH = 'css';

    protected $url;

    protected $query;

    public $request_headers = [];

    public $request_header_whitelist = [
        'HTTP_ACCEPT',
        'HTTP_USER_AGENT',
        'HTTP_CONNECTION',
        'CONTENT_TYPE',
    ];

    public $request_fonts_query = [
        'family' => null,
        'display' => null,
        //'subset' => null,
    ];

    public function __construct( array $query = [] )
    {
        $this->url = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'] . ( $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? '' :  ':' . $_SERVER['SERVER_PORT'] ) . DIRECTORY_SEPARATOR . self::BASE_PATH;

        $this->query = array_intersect_key( $_GET, $query ?? $this->request_fonts_query );

        // Set the request headers
        foreach ( $_SERVER as $param_key => $param_value ) {
            if ( in_array( $param_key, $this->request_header_whitelist ) ) {
                $this->request_headers[] = sprintf( '%s: %s', str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $param_key, 5 ) ) ) ) ), $param_value );
            }
        }
    }

    public function buildCss( \Closure $callback = null ) : string
    {
        $remote_css = $this->getRemoteCss( $this->url . '?' . http_build_query( $this->query ) );

        if ( $callback instanceof \Closure ) {
            $callback( new \Src\Services\WebfontCSSApiGenerator\WebfontCSSApiGenerator( $this->query ) );
        }

        if ( ! $remote_css ) {
            return false;
        }

        return preg_replace_callback( "/url\('(.+)'\).+\((.+)\)/i", [
            $this,
            'inlineRemoteCss'
        ], $remote_css );
    }

    private function inlineRemoteCss( array $matches ) : string
    {
        $font_css = $this->getRemoteCss( $matches[1] );

        // This is probably IE so we don't care
        if ( ! $font_css || empty( $matches[2] ) ) {
            return $matches[0];
        }

        return str_replace( $matches[1], sprintf( 'data:%s;charset=utf-8;base64,%s', str_replace( [
            "'",
            "embedded-opentype",
            "opentype",
            "svg",
            "truetype",
            "woff",
            "woff2"
        ],
        [
            "",
            "application/vnd.ms-fontobject",
            "application/vnd.ms-opentype",
            "image/svg+xml",
            "font/sfnt",
            "application/font-woff",
            "application/x-font-woff2"
        ], $matches[2] ), base64_encode( $font_css ) ), $matches[0] );
    }

    private function getRemoteCss( string $url ) : string
    {
        $context = stream_context_create( [
            'http' => [
                'method' => 'GET',
                'header' => $this->request_headers,
            ]
        ] );

        return self::fetch( $url, $context );
    }


    private function fetch( string $host = null, $context, string $compression = 'gzip', string $proxy = null, $cookies = true, string $cookie = 'cookie.txt' ) : string
    {
        // check curl extension
        if ( ! extension_loaded( 'curl' ) && function_exists( 'allow_url_fopen' ) ) {
            throw new \InvalidArgumentException(sprintf(
                    'This %s need PHP cURL extension and allow_url_fopen.',
                    class_exists( __CLASS__ ) ? 'class ' . __METHOD__ : 'function ' . __FUNCTION__
                ));
        } else if ( function_exists( 'curl_init' ) ) {
            $cookie = explode( '.', $cookie );
            $cookie = $cookie[0] . DIRECTORY_SEPARATOR . md5( $cookie[0] . '_' . date( 'YMd' ) ) . '.' . $cookie[1];
            if ( $cookies == true ) {
                if ( file_exists( $cookie ) ) {
                    $cookie_file = $cookie;
                } else {
                    $fp = @fopen( $cookie, 'w' ) or die( 'The cookie file could not be opened. Make sure this directory has the correct permissions' );
                    @fwrite( $fp, strtoupper( str_shuffle( md5( $cookie[0] ) ) ) );
                    $cookie_file = $cookie;
                    @fclose( $fp );
                }
            }

            // use cURL to fetch data
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $host );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->request_headers );
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 3 );
            curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
            curl_setopt( $ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS );
            curl_setopt( $ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS );
            curl_setopt( $ch, CURLOPT_USERAGENT, $this->request_headers[ 'HTTP_USER_AGENT' ] );
            curl_setopt( $ch, CURLOPT_ENCODING, $compression );
            if ( $proxy ) curl_setopt( $ch, CURLOPT_PROXY, $proxy );
            if ( $cookies == true ) {
                curl_setopt( $ch, CURLOPT_COOKIEFILE, $cookie_file );
                curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie_file );
            }
            $response = curl_exec( $ch );
            curl_close ( $ch );
        } else if ( ini_get( 'allow_url_fopen' ) ) {
            // fall back to fopen()
            $response = @file_get_contents( $host, false, $context );
        } else {
            trigger_error ( 'Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini', E_USER_ERROR );
            return false;
        }

        return $response;
    }

}

