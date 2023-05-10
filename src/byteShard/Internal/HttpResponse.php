<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\Enum;
use byteShard\Enum\HttpResponseType;

/**
 * Class HttpResponse
 * @package byteShard\Internal
 */
class HttpResponse
{
    private string $expires;
    private string $responseCacheLimiter = 'nocache';
    private string $responseCharset      = 'utf-8';
    private string $responseText;
    private string $responseType         = 'text/plain';


    public function __construct(HttpResponseType $respType = HttpResponseType::JSON, string $value = '', int $expireTimestamp = 0)
    {
        $this->setResponseType($respType);
        $this->setResponseContent($value);
        $this->setResponseExpiration($expireTimestamp);
    }

    public function setResponseType(HttpResponseType $respType): void
    {
        $this->responseType = match ($respType) {
            HttpResponseType::JSON => 'application/json',
            HttpResponseType::TXT  => 'text/plain',
            HttpResponseType::XML  => (stripos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false) ? 'application/xhtml+xml' : 'text/xml'
        };
    }

    public function setResponseContent(mixed $value): void
    {
        if ($this->responseType === 'application/json') {
            $this->responseText = json_encode($value);
        } else {
            $this->responseText = $value;
        }
    }

    public function setResponseExpiration(int $expireTimestamp): void
    {
        $this->expires = gmdate('r', $expireTimestamp);
    }

    public function setResponseCharset(string $charset): void
    {
        $this->responseCharset = $charset;
    }

    private function getContentType(): string
    {
        $result[] = $this->responseType;
        if (!empty($this->responseCharset)) {
            $result[] = 'charset='.$this->responseCharset;
        }
        return implode('; ', $result);
    }

    public function setResponseCacheLimiter(Enum\CacheLimiter $cacheLimiter): void
    {
        $this->responseCacheLimiter = $cacheLimiter->value;
    }

    public function printHTTPResponse(): never
    {
        header('Cache-Control: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0, s-maxage=0');
        header('Pragma: no-cache');
        header('Expires: '.$this->expires);
        header('Content-type: '.$this->getContentType());
        // TODO: setting the session_cache_limiter can't be done here since session_start has already been called in Environment.php
        // move the setResponseCacheLimiter function to an appropriate location
        // session_cache_limiter($this->responseCacheLimiter);

        // ob_start is called in the error handler, this way we can catch any output which was not meant to be sent to the client
        $GLOBALS['output_buffer'] = ob_get_clean();
        print $this->responseText;
        exit;
    }
}
