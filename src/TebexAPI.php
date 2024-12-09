<?php

namespace Tebex;

/**
 * Base class for a Tebex API, containing our references for various keys
 */
abstract class TebexAPI {
    protected static string $_projectId = "";
    protected static string $_privateKey = "";
    protected static string $_publicToken = "";
    protected static bool $_areApiKeysSet = false;

    public static function getPublicToken() : string {
        return static::$_publicToken;
    }
}