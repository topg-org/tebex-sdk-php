<?php

namespace Tebex\Headless\Projects;

class DiscordProject extends TebexProject
{
    public function getUserIdentifierParameter(): string
    {
        return "discord_id";
    }
}