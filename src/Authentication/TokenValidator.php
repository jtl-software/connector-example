<?php

namespace Jtl\Connector\Example\Authentication;

use Jtl\Connector\Core\Authentication\TokenValidatorInterface;

class TokenValidator implements TokenValidatorInterface
{
    protected $checkToken;
    
    public function __construct(string $checkToken)
    {
        $this->checkToken = $checkToken;
    }
    
    /**
     * @inheritDoc
     */
    public function validate(string $token) : bool
    {
        return $token === $this->checkToken;
    }
}