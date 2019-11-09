<?php

namespace Evo\iBlue\Authentication;

class AccessToken
{

    protected $accessTokenValue;

    protected $expiresAt;

    public function __construct(string $accessToken, int $expiresAt = 0)
    {
        $this->accessTokenValue = $accessToken;
        if ($expiresAt) {
            $this->setExpiresAtFromTimestamp($expiresAt);
        }
    }

    public function getValue()
    {
        return $this->accessTokenValue;
    }

    protected function setExpiresAtFromTimestamp(int $timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        $this->expiresAt = $date;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function isExpired()
    {
        
        if($this->getExpiresAt() instanceof \DateTime){
            return $this->getExpiresAt()->getTimestamp() < time();
        }

        return null;
    }
}
