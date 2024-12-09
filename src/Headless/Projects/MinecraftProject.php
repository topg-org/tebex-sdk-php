<?php

namespace Tebex\Headless\Projects;

class MinecraftProject extends TebexProject
{
    public function getUserIdentifierParameter(): string
    {
        return "username";
    }
}