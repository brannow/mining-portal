<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Controller;


use Fuyukai\Core\Request;
use Fuyukai\Kernel;

abstract class CliController extends AbstractController
{
    public function __construct(string $methodName, ?Request $request = null, int $kernelMode = Kernel::MODE_DEFAULT)
    {
        if ($kernelMode !== Kernel::MODE_CLI) {
            die();
        }
        
        parent::__construct($methodName, $request, $kernelMode);
    }
    
    public function initialize(string $templatePath = '')
    {
    
    }
    
    public function initializeView(string $templatePath = '')
    {
    
    }
}