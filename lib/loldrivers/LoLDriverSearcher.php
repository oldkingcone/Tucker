<?php

namespace loldrivers;

use AllowDynamicProperties;

#[AllowDynamicProperties] class LoLDriverSearcher
{
    private $dom_document;

    function __construct() {
        $this->xpath = '//tbody/tr';
        $this->dom_document = new \DOMDocument;
        $this->driver_paths = [
            "C:\\Windows\\System32\\drivers",
            "C:\\Windows\\System32\\DriverStore\\FileRepository\\",
            "C:\\Program Files\\",
            "C:\\Program Files (x86)\\",
            sys_get_temp_dir()
        ];
        $this->found_drivers = [];
    }

    private function executeRequest() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.loldrivers.io");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:135.0) Gecko/20100101 Firefox/135.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    private function parseResponse() {
        libxml_use_internal_errors(true);
        $d = $this->executeRequest();
        if (empty($d)) {
            return [];
        }
        $this->dom_document->loadHTML($d);
        $xpath = new \DOMXPath($this->dom_document);
        $elements = $xpath->query('//*[@id="chartTable"]');

        if (empty($elements) || $elements->length === 0) {
            libxml_clear_errors();
            return [];
        }

        $structured_data = [];
        foreach ($elements as $row) {
            $driver_name = "";
            $driver_hash = "";
            $purpose = "";
            $date_added = "";
            $row_data = [];

            foreach ($row->getElementsByTagName('td') as $cell) {
                $holder = trim($cell->nodeValue);

                if (str_ends_with($holder, "sys")) {
                    $driver_name = $holder;
                }
                if (preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", $holder, $date_added_matches)) {
                    $date_added = $date_added_matches[0];
                }
                if (preg_match("/\b[a-fA-F0-9]{64}\b/", $holder, $driver_hash_matches)) {
                    $driver_hash = $driver_hash_matches[0];
                }
                if (preg_match("/\b(Vulnerable|Malicious)\b/i", $holder, $purpose_matches)) {
                    $purpose = $purpose_matches[0];
                }
                $row_data[$driver_name] = [
                    "driver_name" => $driver_name,
                    "hash" => $driver_hash ?? "N/A",
                    "purpose" => $purpose,
                    "added" => $date_added,
                ];
            }
            $structured_data[] = $row_data;
        }

        libxml_clear_errors();
        return $structured_data;
    }

    function getDrivers() {
        $driverNames = $this->parseResponse();
        if (!empty($driverNames)) {
            $driverNamesFirstRow = $driverNames[0];
            foreach ($this->driver_paths as $path) {
                if (!is_dir($path)) {
                    continue;
                }
                $iterator = new \DirectoryIterator($path);
                foreach ($iterator as $file) {
                    if (!$file->isFile()) {
                        continue;
                    }

                    $fileName = $file->getBasename();
                    $fileNameLower = strtolower($fileName);
                    if (isset($driverNamesFirstRow[$fileNameLower])) {
                        $driverData = $driverNamesFirstRow[$fileNameLower];
                        if (hash_file("sha256", $file->getPathname()) === $driverData["hash"]) {
                            $this->found_drivers[$fileName] = [
                                "purpose" => $driverData['purpose'],
                                "path_on_disk" => $file->getPathname(),
                                "discovery_date" => $driverData['added'],
                                "computed_hash_on_disk" => hash_file("sha256", $file->getPathname())
                            ];
                        }
                    }
                }
            }
        }
        if (!empty($this->found_drivers)) {
            return ["status" => "success", "data" => $this->found_drivers];
        } else {
            return ["status" => "error", "data" => "No drivers found in the directories."];
        }
    }


}