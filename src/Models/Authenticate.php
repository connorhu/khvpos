<?php

namespace KHTools\VPos\Entities;

class Authenticate
{
    public ?Browser $browserChallenge = null;

    public ?Sdk $sdkChallenge = null;
}