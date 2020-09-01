<?php

namespace Jtl\Connector\Example\Authentication;

use Jtl\Connector\Core\Authentication\TokenValidatorInterface;

class TokenValidator implements TokenValidatorInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * TokenValidator constructor.
     * @param string $token
     * @throws \Exception
     */
    public function __construct(string $token)
    {
        if ($token === '') {
            throw new \Exception("Token can not be an empty string");
        }

        $this->token = $token;
    }
    
    /**
     * @inheritDoc
     */
    public function validate(string $token) : bool
    {
        return $token === $this->token;
    }
}
