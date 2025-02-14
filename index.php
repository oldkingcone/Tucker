<?php
require_once 'lib/classes.inc.php';

use users\UserSearcher;
use ServicePathsOfInterest\ServicePathSearcher;
use riskyRegistries\RegistrySearcher;
use RiskyAutoRuns\AutoRunSearcher;
use lolbins\LoLBinSearcher;
use loldrivers\LoLDriverSearcher;

// defines
const userSearcher = new UserSearcher();
const servicePathSearcher = new ServicePathSearcher();
const registrySearcher = new RegistrySearcher();
const autoRunSearcher = new AutoRunSearcher();
const lolBinSearcher = new LoLBinSearcher();
const lolDriverSearcher = new LoLDriverSearcher();
// end defines


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $actions = [
            'searchLocalUsers' => function () {
                return userSearcher->searchUsers();
            },
            'fetchLolBins' => function () {
                return lolBinSearcher->getBins();
            },
            'fetchLolDrivers' => function () {
                return lolDriverSearcher->getDrivers();
            },
            'fetchservicePaths' => function () {
                return servicePathSearcher->search();
            },
        ];

        $action = $_POST['action'] ?? null;
        if (isset($actions[$action])) {
            $result = $actions[$action]();
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid action specified.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    die();
}

$domain = userSearcher->searchForDomain();
$c = get_current_user();
echo <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="lib/javaScript/main.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .top-bar {
            width: 100%;
            background-color: #1c1c1c;
            color: #fff;
            padding: 15px 0;
            text-align: center;
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-tab {
            padding: 10px 20px;
            font-size: 1rem;
            color: #fff;
            border-radius: 4px;
            text-align: center;
            background-color: #383838;
            text-transform: capitalize;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .nav-tab:hover {
            background-color: #555;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 100px;
            max-width: 1200px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        .box {
            background: #3a3a3a;
            border: 1px solid #555;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            padding: 20px;
            text-align: center;
        }

        .box h2 {
            font-size: 20px;
            color: #fff;
            margin-bottom: 15px;
        }

        .scrollable-output {
            flex-grow: 1;
            background: #1c1c1c;
            border: 1px solid #444;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            height: 100px;
            overflow-y: auto;
            font-family: monospace;
        }

        .box button {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .box button:hover {
            background: #0056b3;
        }

        @media screen and (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .top-bar {
                flex-direction: column;
                padding: 15px 0;
            }
        }
        li.user-entry {
            padding: 5px 10px;
            cursor: pointer;
        }
    
        li.user-entry:hover {
            background-color: #e4e4e4;
        }
        .site-footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px 10px;
        }
        .page-content {
            flex: 1;
        }

    </style>
</head>
<body>
<div class="top-bar">
    <div class="nav-tab">Current User: <b id="currentUser">$c</b></div>
    <div class="nav-tab">Current Domain: <b id="currentDomain"></b>$domain</div>
    <div class="nav-tab"><a href="https://github.com/oldkingcone/Tucker">Tucker</a></div>
</div>

<div class="container">
    <div class="box">
        <h2>Local Users</h2>
        <div class="scrollable-output" id="localUsers">
            No data available yet.
        </div>
        <button onclick="fireAction('Local Users', 'localUsers')">Fire</button>
    </div>

    <div class="box">
        <h2>LDAP Users</h2>
        <div class="scrollable-output" id="ldapUsers">
            No data available yet.
        </div>
        <button onclick="fireAction('LDAP Users', 'ldapUsers')">Fire</button>
    </div>

    <div class="box">
        <h2>Risky Registries</h2>
        <div class="scrollable-output" id="riskyRegistries">
            No data available yet.
        </div>
        <button onclick="fireAction('Risky Registries','riskyRegistries')">Fire</button>
    </div>

    <div class="box">
        <h2>Service Paths</h2>
        <div class="scrollable-output" id="servicePaths">
            No data available yet.
        </div>
        <button onclick="fireAction('Service Paths', 'servicePaths')">Fire</button>
    </div>

    <div class="box">
        <h2>LOLBins</h2>
        <div class="scrollable-output" id="lolBins">
            No data available yet.
        </div>
        <button onclick="fireAction('LOLBins', 'lolBins')">Fire</button>
    </div>

    <div class="box">
        <h2>LOLDrivers</h2>
        <div class="scrollable-output" id="lolDrivers">
            No data available yet.
        </div>
        <button onclick="fireAction('LOLDrivers', 'lolDrivers')">Fire</button>
    </div>

    <div class="box">
        <h2>Risky AutoRuns</h2>
        <div class="scrollable-output" id="riskyAutoRuns">
            No data available yet.
        </div>
        <button onclick="fireAction('Risky AutoRuns', 'riskyAutoRuns')">Fire</button>
    </div>
</div>
</body>
<footer class="site-footer">
      <p>&copy; 2023 Tucker - Created by <a href="https://github.com/oldkingcone">Oldkingcone</a>. All Rights Reserved.</p>
      <p>This software is distributed as-is and for educational value.</p>
    </footer>
</html>
EOF;

