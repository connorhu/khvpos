<?php

namespace KHTools\VPos\Models;

class Authenticate
{
    public ?Browser $browserChallenge = null;

    public ?Sdk $sdkChallenge = null;
}
