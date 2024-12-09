<?php

namespace Tebex\Headless\Projects;

class SteamProject extends TebexProject
{
    public function getUserIdentifierParameter(): string
    {
        return "user_id";
    }
}