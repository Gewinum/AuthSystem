<?php

namespace authsystem;

use authsystem\data\providers\base\DataProvider;
use authsystem\data\providers\ConfigDataProvider;
use mysql_xdevapi\Exception;

class DataProviders
{
    private array $providers;

    private ?DataProvider $currentProvider;

    public function __construct()
    {
        $this->providers = [
            new ConfigDataProvider
        ];

        $this->currentProvider = null;
    }

    public function getProviderByName(string $providerName) : ?DataProvider
    {
        foreach($this->providers as $provider) {
            if($provider->getName() === $providerName) {
                return $provider;
            }
        }

        return null;
    }

    public function getCurrent() : ?DataProvider
    {
        return $this->currentProvider;
    }

    public function setCurrent(DataProvider $provider)
    {
        if($this->getCurrent() !== null) {
            throw new Exception("You can't set current provider twice and more.");
        }

        $this->currentProvider = $provider;

        $this->currentProvider->load();
    }
}