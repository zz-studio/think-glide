<?php
/**
 * +----------------------------------------------------------------------
 * | think-glide [thinkphp6]
 * +----------------------------------------------------------------------
 *  .--,       .--,             | FILE: Response.php
 * ( (  \.---./  ) )            | AUTHOR: byron
 *  '.__/o   o\__.'             | EMAIL: xiaobo.sun@qq.com
 *     {=  ^  =}                | QQ: 150093589
 *     /       \                | DATETIME: 2019/11/6 14:33
 *    //       \\               |
 *   //|   .   |\\              |
 *   "'\       /'"_.-~^`'-.     |
 *      \  _  /--'         `    |
 *    ___)( )(___               |-----------------------------------------
 *   (((__) (__)))              | 高山仰止,景行行止.虽不能至,心向往之。
 * +----------------------------------------------------------------------
 * | Copyright (c) 2019 http://www.zzstudio.net All rights reserved.
 * +----------------------------------------------------------------------
 */
declare(strict_types=1);

namespace think\glide;

use League\Glide\Signatures\SignatureInterface;

class UrlBuilderFactory
{
    /**
     * The base URL.
     * @var string
     */
    protected $baseUrl;

    /**
     * Whether the base URL is a relative domain.
     * @var bool
     */
    protected $isRelativeDomain = false;

    /**
     * The HTTP signature used to sign URLs.
     * @var SignatureInterface
     */
    protected $signature;

    /**
     * Create UrlBuilder instance.
     * @param string                  $baseUrl   The base URL.
     * @param SignatureInterface|null $signature The HTTP signature used to sign URLs.
     */
    public function __construct($baseUrl = '', SignatureInterface $signature = null)
    {
        $this->setBaseUrl($baseUrl);
        $this->setSignature($signature);
    }

    /**
     * Set the base URL.
     * @param string $baseUrl The base URL.
     */
    public function setBaseUrl($baseUrl)
    {
        if (substr($baseUrl, 0, 2) === '//') {
            $baseUrl = 'http:'.$baseUrl;
            $this->isRelativeDomain = true;
        }

        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Set the HTTP signature.
     * @param SignatureInterface|null $signature The HTTP signature used to sign URLs.
     */
    public function setSignature(SignatureInterface $signature = null)
    {
        $this->signature = $signature;
    }

    /**
     * Get the URL.
     * @param  string $path   The resource path.
     * @param  array  $params The manipulation parameters.
     * @return string The URL.
     */
    public function getUrl($path, array $params = [])
    {
        $parts = parse_url($this->baseUrl.'/'.trim($path, '/'));

        if ($parts === false) {
            throw new \InvalidArgumentException('Not a valid path.');
        }

        $parts['path'] = '/'.trim($parts['path'], '/');

        if ($this->signature) {
            $params = $this->signature->addSignature($parts['path'], $params);
        }

        return $this->buildUrl($parts, $params);
    }

    /**
     * Build the URL.
     * @param  array  $parts  The URL parts.
     * @param  array  $params The manipulation parameters.
     * @return string The built URL.
     */
    protected function buildUrl($parts, $params)
    {
        $url = '';

        if (isset($parts['host'])) {
            if ($this->isRelativeDomain) {
                $url .= '//'.$parts['host'];
            } else {
                $url .= $parts['scheme'].'://'.$parts['host'];
            }

            if (isset($parts['port'])) {
                $url .= ':'.$parts['port'];
            }
        }

        $url .= $parts['path'];

        if (count($params)) {
            $url .= '?'.http_build_query($params);
        }

        return $url;
    }

    /**
     * Create UrlBuilder instance.
     * @param  string      $baseUrl URL prefixed to generated URL.
     * @param  string|null $signKey Secret key used to secure URLs.
     * @return UrlBuilderFactory  The UrlBuilder instance.
     */
    public static function create($baseUrl, $signKey = null)
    {
        $httpSignature = null;

        if (!is_null($signKey)) {
            $httpSignature = SignatureFactory::create($signKey);
        }

        return new self($baseUrl, $httpSignature);
    }
}