<?php

namespace riskyRegistries;

#[\AllowDynamicProperties] class RegistrySearcher
{
    function __construct()
    {
        $this->com_object = new \COM("WbemScripting.SWbemLocator");
        $this->registry_hives = [
            "HKEY_LOCAL_MACHINE" => "0x80000002",
            "HKEY_CURRENT_USER" => "0x80000001",
            "HKEY_USERS" => "0x80000003",
            "HKEY_CLASSES_ROOT" => "0x80000000",
            "HKEY_CURRENT_CONFIG" => "0x80000005",
        ];

    }

}