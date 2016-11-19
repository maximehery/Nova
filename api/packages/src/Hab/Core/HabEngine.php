<?php

namespace Hab\Core;

use Hab\Database\DatabaseManager;

/**
 * Class HabEngine
 * @package Hab\Core
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class HabEngine
{
    /**
     * HabClient API Settings
     *
     * @var object
     */
    private $apiSettings = null;

    /**
     * HabClient Engine Settings
     *
     * @var object
     */
    private $engineSettings = null;

    /**
     * Requested URI Query String
     *
     * @var array
     */
    private $queryString = [];

    /**
     * Used Token in this Authentication
     *
     * @var string
     */
    private $tokenAuth = '';

    /**
     * Get the Current Instance of the Engine Class
     *
     * Singleton Method
     *
     * @return HabEngine
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            /** @var HabEngine $instance */
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Prepares the HabClient Engine
     *
     * @param string $apiSettings
     * @param string $engineSettings
     */
    public function prepare($apiSettings, $engineSettings)
    {
        // Decodes into Objects
        $this->apiSettings = json_decode($apiSettings);
        $this->engineSettings = json_decode($engineSettings);

        // Set Database Credentials
        DatabaseManager::getInstance()->setCredentials($this->engineSettings->database);
    }

    /**
     * Create Response for the Requested Page
     *
     * @return string
     */
    public function createResponse()
    {
        return (new HabTemplate($this->routeEngine()))->getResponse();
    }

    /**
     * Returns the Requested Page
     *
     * @return string
     */
    public function routeEngine()
    {
        // Check if Query String exists. If exists, continue.
        if (array_key_exists('QUERY_STRING', $_SERVER)) {
            // Parse Query String into Array by Key=Value
            parse_str($_SERVER['QUERY_STRING'], $this->queryString);

            // Check if Token Entry Exists
            if (array_key_exists('Token', $this->queryString)) {
                $this->tokenAuth = $this->queryString['Token'];
            }

            // Check if Page Entry exists
            if (array_key_exists('Page', $this->queryString)) {
                return $this->queryString['Page'];
            }
        }

        return 'Home';
    }

    /**
     * Get Engine Settings
     *
     * @return object
     */
    public function getEngineSettings()
    {
        return $this->engineSettings;
    }

    /**
     * Get the API Settings
     *
     * @return object
     */
    public function getApiSettings()
    {
        return $this->apiSettings;
    }

    /**
     * Get the Query String
     *
     * @return array
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Get Used Token in the Current Communication
     *
     * @return string
     */
    public function getTokenAuth()
    {
        return $this->tokenAuth;
    }
}
