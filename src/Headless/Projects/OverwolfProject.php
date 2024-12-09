<?php

namespace Tebex\Headless\Projects;

class OverwolfProject extends TebexProject
{
    public function getUserIdentifierParameter(): string
    {
        return "username";
    }
}