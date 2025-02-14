<?php

namespace users\ldap;

use AllowDynamicProperties;

#[AllowDynamicProperties] class ldapSearch
{
    function __construct(string $ldap_server, string $ldap_user, string $ldap_pass, string $domain) {
        $this->ldap_server = $ldap_server;
        $this->ldap_user = $ldap_user;
        $this->ldap_pass = $ldap_pass;
        $this->domain = $domain;
    }
/*
LDAP server and credentials
$ldap_server = "ldap://your.domain.com";
$ldap_user = "CN=YourAdminUser,CN=Users,DC=your,DC=domain,DC=com"; // Admin Bind User
$ldap_password = "YourPassword";
$base_dn = "DC=your,DC=domain,DC=com"; // Base DN for search

 Connect to the LDAP server
$ldap_conn = ldap_connect($ldap_server);
if (!$ldap_conn) {
    die("Failed to connect to LDAP server.");
}

 Set LDAP options
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

 Bind to the LDAP server
if (!ldap_bind($ldap_conn, $ldap_user, $ldap_password)) {
    die("LDAP bind failed: " . ldap_error($ldap_conn));
}

 Search for all users
$filter = "(objectClass=user)"; // You can refine the filter further
$attributes = ["cn", "sAMAccountName", "memberOf", "userAccountControl"];
$result = ldap_search($ldap_conn, $base_dn, $filter, $attributes);
if (!$result) {
    die("LDAP search failed: " . ldap_error($ldap_conn));
}

 Parse results
$entries = ldap_get_entries($ldap_conn, $result);

$ldap_users = [];
foreach ($entries as $entry) {
    if (isset($entry["samaccountname"][0])) {
        // Determine if the user is an admin or service account
        $is_admin = false;
        $is_service = false;

        if (isset($entry["memberof"])) {
            foreach ($entry["memberof"] as $group) {
                if (stripos($group, "Administrators") !== false) {
                    $is_admin = true;
                }
                if (stripos($group, "Service Accounts") !== false) {
                    $is_service = true;
                }
            }
        }

        $ldap_users[] = [
            "Username" => $entry["samaccountname"][0],
            "Common Name" => $entry["cn"][0],
            "Is Admin" => $is_admin,
            "Is Service Account" => $is_service,
        ];
    }
}
*/
}