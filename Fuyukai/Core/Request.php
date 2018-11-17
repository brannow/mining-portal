<?php


namespace Fuyukai\Core;


class Request
{
    public const HTTPS = 'https';
    public const HTTP = 'http';
    public const HTTPS_ON = 'on';
    
    public const ENV_PROD = 'prod';
    public const ENV_DEV = 'dev';
    
    public const METHOD_POST = 'POST';
    public const METHOD_GET = 'GET';
    
    /**
     * @var string
     */
    private $scheme = '';
    
    /**
     * @var string
     */
    private $host = '';
    
    /**
     * @var string
     */
    private $path = '';
    
    /**
     * @var array
     */
    private $queries = [];
    
    /**
     * @var string
     */
    private $requestMethod = self::METHOD_GET;
    
    /**
     * @var
     */
    private $env = self::ENV_PROD;
    
    /**
     * Request constructor.
     */
    public function __construct()
    {
        if($this->hasServerValue('SERVER_NAME')) {
            
            if ($this->hasServerValue('HTTPS')) {
                $this->setScheme(static::HTTPS);
            } else {
                $this->setScheme(static::HTTP);
            }
            
            $this->setHost($this->getServerValue('SERVER_NAME'));
            $pathQuery = parse_url($this->getServerValue('REQUEST_URI'));
            if (isset($pathQuery['path'])) {
                $this->setPath($pathQuery['path']);
            }
            if (isset($pathQuery['query'])) {
                $q = array();
                parse_str($pathQuery['query'], $q);
                $this->setQueries($q);
            }

            if ($this->getServerValue('REQUEST_METHOD') === static::METHOD_POST) {
                $this->requestMethod = static::METHOD_POST;
            }
        }
    
        if ($this->hasServerValue('FUYUKAI_ENV')) {
            $this->env = (strtolower($this->getServerValue('FUYUKAI_ENV'))===self::ENV_DEV)?self::ENV_DEV:self::ENV_PROD;
        }
    }
    
    public function getClientIPHash(): string
    {
        return md5(
            $this->getServerValue('HTTP_X_FORWARDED_FOR') .
            $this->getServerValue('REMOTE_ADDR') .
            $this->getServerValue('HTTP_USER_AGENT')
        );
    }
    
    /**
     * @param string $key
     * @return string
     */
    private function getServerValue(string $key): string
    {
        return (string)filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_STRING);
    }
    
    /**
     * @param string $key
     * @return bool
     */
    private function hasServerValue(string $key): bool
    {
        return (bool)filter_has_var(INPUT_SERVER, $key);
    }
    
    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }
    
    /**
     * @param string $scheme
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }
    
    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }
    
    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }
    
    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    
    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }
    
    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
    
    /**
     * @param array $queries
     */
    public function setQueries(array $queries): void
    {
        $this->queries = $queries;
    }
    
    /**
     * @return string
     */
    public function getEnv(): string {
        return $this->env;
    }
    
    /**
     * @param string $key
     * @return string
     */
    public function getHeaderData(string $key): string
    {
        $key = strtoupper($key);
        $key = str_replace('HTTP_', '', $key);
        $key = str_replace('-', '_', $key);
        return $this->getServerValue('HTTP_' . $key);
    }
    
    /**
     * @param string $key
     * @param bool $raw
     * @return string
     */
    public function getPostData(string $key, bool $raw = false): string
    {
        if ($raw) {
            return (string)filter_input(INPUT_POST, $key);
        }
        
        return htmlentities(trim((string)filter_input(INPUT_POST, $key)));
    }
    
    /**
     * @param string $key
     * @param bool $raw
     * @return string
     */
    public function getQueryData(string $key,  bool $raw = false): string
    {
        if ($raw) {
            return (string)filter_input(INPUT_GET, $key);
        }
        
        return htmlentities(trim((string)filter_input(INPUT_GET, $key)));
    }
    
    /**
     * @return bool
     */
    public function isPostRequest(): bool
    {
        return ($this->requestMethod === static::METHOD_POST);
    }
}