<?php

namespace Jtl\Connector\Example\Authentication;

use Jtl\Connector\Core\Authentication\TokenValidatorInterface;
use Noodlehaus\ConfigInterface;

class TokenValidator implements TokenValidatorInterface
{
    protected $checkToken;
    
    public function __construct(ConfigInterface $config)
    {
        $this->checkToken = $config->get("token");
    }
    
    /**
     * @inheritDoc
     */
    public function validate(string $token) : bool
    {
        return $token === $this->checkToken;
    }
}