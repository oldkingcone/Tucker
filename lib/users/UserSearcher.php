<?php

namespace users;

use AllowDynamicProperties;

require_once "config/UserConfig.php";

#[AllowDynamicProperties] class UserSearcher
{

    function __construct()
    {
        $this->com = new \COM("winmgmts://") or die("Unable to initialize WMI");
    }

    /**
     * @throws \Exception
     */
    function searchUsers() {
        $users = $this->com->ExecQuery("SELECT * FROM Win32_UserAccount WHERE LocalAccount = TRUE");
        foreach ($users as $user) {
            $local[] = [
                'Name' => $user->Name,
                'FullName' => $user->FullName,
                'SID' => $user->SID,
                'Description' => $user->Description,
                'Disabled' => $user->Disabled,
                'PasswordChangeable' => $user->PasswordChangeable,
                'PasswordExpires' => $user->PasswordExpires,
            ];
        }
        return $local;
    }

    function searchForDomain()
    {
        $computer_data = $this->com->ExecQuery("SELECT * FROM Win32_ComputerSystem");
        foreach ($computer_data as $computer) {
            $domain = $computer->Domain;
        }
        return $domain;
    }
}