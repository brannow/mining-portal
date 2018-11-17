<?php declare(strict_types=1);


namespace Src\Frontend\Api\Controller;

use Config\Config;
use Fuyukai\Userspace\Controller\AbstractController;
use Src\Frontend\Api\Service\RigService;
use Src\Frontend\Api\View\JsonView;

class ApiController extends AbstractController
{
    private const FUYUKAI_REQUEST = 'fuyukai-request';
    private const PAYLOAD = 'payload';
    private const SIGNATURE = 'signature';
    private const DATETIME = 'datetime';
    
    /**
     * @var string
     */
    protected $viewClass = JsonView::class;
    
    /**
     * @param string $templatePath
     */
    protected function initialize(string $templatePath = '')
    {
        // we don't need a template path for json
        parent::initialize();
        
        $key = $this->getRequest()->getHeaderData(static::FUYUKAI_REQUEST);
        $systemApiHeaderKey = Config::getConfigEntry(Config::FUYUKAI_REQUEST_HEADER);
        if (!($key && $key === $systemApiHeaderKey)) {
            die();
        }
    
        $data = $this->getRequest()->getPostData(static::PAYLOAD, true);
        $dateTime = $this->getRequest()->getPostData(static::DATETIME);
        $requestSignature = $this->getRequest()->getPostData(static::SIGNATURE);
        
        
        
        if ($requestSignature !== $this->generateSignature($data, $dateTime)) {
            die('invalid signature');
        }
    }
    
    /**
     * @param string $payload
     * @param string $datetime
     * @return string
     */
    private function generateSignature(string $payload, string $datetime): string
    {
        $systemApiHeaderKey = Config::getConfigEntry(Config::FUYUKAI_API_PRIVATE_KEY);
        return md5($payload . $datetime . $systemApiHeaderKey);
    }
    
    /**
     *
     */
    public function telemetry()
    {
        $data = $this->getRequest()->getPostData(static::PAYLOAD, true);
        $dateTimeString = $this->getRequest()->getPostData(static::DATETIME);
        $rigData = json_decode($data, true);
        $dateTime = \DateTime::createFromFormat('Y.m.d H:i:s', $dateTimeString);
        $errorCode = 99;
        
        // 0 means all fine! | code != 0 ... problem
        if($dateTime && $rigData) {
            if(RigService::saveRigTelemetry($rigData, $errorCode)) {
                $errorCode = 0;
            }
        }
        
        $this->assign('status', (int)$errorCode);
    }
}