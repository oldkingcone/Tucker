<?php

namespace lolbins;

use AllowDynamicProperties;
use FilesystemIterator;

#[AllowDynamicProperties] class LoLBinSearcher
{

    function __construct() {
        $this->dom_document = new \DOMDocument;
        $this->directories = [
            "C:\\Windows\\System32\\",
            "C:\\Windows\\SysWOW64\\",
            "C:\\Windows\\",
            "C:\\Program Files\\",
            "C:\\Program Files (x86)\\",
            "C:\\ProgramData\\",
            "C:\\Users\\"
        ];
        $this->found_bins = [];
    }

    private function executeRequest() {
        if (!function_exists('curl_init')) {
            return ["Error" => true, "Message" => "cURL is not installed or enabled."];
        }
        $cu = curl_init();
        curl_setopt($cu, CURLOPT_URL, "https://lolbas-project.github.io/");
        curl_setopt($cu, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:135.0) Gecko/20100101 Firefox/135.0");
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cu, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cu, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($cu);
        curl_close($cu);
        return ["Error" => false, "Data" => $response];
    }

    private function parseResponse() {
        libxml_use_internal_errors(true);
        $response = $this->executeRequest();
        if ($response["Error"]) {
            return ["status" => false, "data" => $response["Message"]];
        }
        $this->dom_document->loadHTML($response["Data"]);
        $xpath = new \DOMXPath($this->dom_document);
        $binaryNodes = $xpath->query("//a[@class='bin-name']");
        $binaryNames = [];
        foreach ($binaryNodes as $node) {
            $binaryNames[] = $node->nodeValue;
        }

        libxml_clear_errors();
        return $binaryNames;
    }



    function getBins() {
        $binaryNames = $this->parseResponse();
        if (isset($binaryNames["Error"]) && $binaryNames["Error"]) {
            return $binaryNames;
        }
        $binaryNames = array_map('strtolower', $binaryNames);
        foreach ($this->directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }
            $iterator = new \DirectoryIterator($directory);
            foreach ($iterator as $file) {
                if ($file->isFile() && ($file->getExtension() == "exe" || $file->getExtension() == "dll")) {
                    if (in_array(strtolower($file->getBasename()), $binaryNames)) {
                        $this->found_bins[] = $file->getPathname();
                    }
                }
            }
        }
        if (!empty($this->found_bins)) {
            return ["status" => "success", "data" => $this->found_bins];
        }
        return ["status" => "error", "data" => "No bins found in the directories."];
    }
}