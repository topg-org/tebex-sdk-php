<?php

namespace Tebex\Common;

class PlatformTypes
{
    public const MC_JAVA_EDITION = 1;
    public const MC_BEDROCK = 2;
    public const MC_OFFLINE_GEYSER = 3;
    public const UNTURNED = 4;
    public const RUST = 5;
    public const SEVEN_DAYS_TO_DIE = 6;
    public const GMOD = 7;
    public const CSGO = 8;
    public const TF2 = 9;
    public const HURTWORLD = 10;
    public const ARK_SURVIVAL_EVOLVED = 11;
    public const SPACE_ENGINEERS = 12;
    public const ATLAS = 13;
    public const GTA_V = 14;
    public const FIVEM = 15;
    public const ONSET = 16;
    public const DISCORD = 17;
    public const UNIVERSAL_NO_AUTH = 18;
    public const REDM = 19;
    public const CREY = 20;
    public const CREY_STAGING = 21;
    public const LEAP = 22;
    public const CURSEFORGE = 23;
    public const OVERWOLF = 24;
    public const CONAN_EXILES = 25;

    private const PLATFORM_NAMES_MAP = [
        "Minecraft: Java Edition" => self::MC_JAVA_EDITION,
        "Minecraft (Bedrock)" => self::MC_BEDROCK,
        "Minecraft (Offline/Geyser)" => self::MC_OFFLINE_GEYSER,
        "Unturned" => self::UNTURNED,
        "Rust" => self::RUST,
        "7 Days to Die" => self::SEVEN_DAYS_TO_DIE,
        "Garry's Mod" => self::GMOD,
        "Counter-Strike: Global Offensive" => self::CSGO,
        "Team Fortress 2" => self::TF2,
        "Hurtworld" => self::HURTWORLD,
        "ARK: Survival Evolved" => self::ARK_SURVIVAL_EVOLVED,
        "Space Engineers" => self::SPACE_ENGINEERS,
        "ATLAS" => self::ATLAS,
        "GTA V" => self::GTA_V,
        "FiveM" => self::FIVEM,
        "Onset" => self::ONSET,
        "Discord" => self::DISCORD,
        "Universal (No Auth)" => self::UNIVERSAL_NO_AUTH,
        "RedM" => self::REDM,
        "CREY" => self::CREY,
        "CREY Staging" => self::CREY_STAGING,
        "LEAP" => self::LEAP,
        "CurseForge" => self::CURSEFORGE,
        "Overwolf" => self::OVERWOLF,
        "Conan Exiles" => self::CONAN_EXILES,
    ];

    public function fromName(string $name): int
    {
        try {
            return self::PLATFORM_NAMES_MAP[$name];
        } catch(\Exception $e){
            throw new \InvalidArgumentException("Platform type not found for name: $name");
        }
    }
}