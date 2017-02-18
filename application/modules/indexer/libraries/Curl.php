<?php

class Curl {

    private $curlStartTime;
    private $curlEndTime;
    private $curlErrorCode = false;
    private $curlReturnCode;
    private $result = false;
    private $curlErrorMessage = '';
    private $curlHeader = '';
    private $recognizeMobileRequest = 0;
    private $isRequestToSolr = 1;

    public function __construct() {
        
    }

    public function getRecognizeMobileRequest() {
        return $this->recognizeMobileRequest;
    }

    public function setRecognizeMobileRequest($recognizeMobileRequest) {
        $this->recognizeMobileRequest = $recognizeMobileRequest;
    }

    public function getCurlHeader() {
        return $this->curlHeader;
    }

    public function setCurlHeader($curlHeader) {
        $this->curlHeader = $curlHeader;
    }

    public function getCurlStartTime() {
        return $this->curlStartTime;
    }

    public function getCurlEndTime() {
        return $this->curlEndTime;
    }

    public function getCurlErrorCode() {
        return $this->curlErrorCode;
    }

    public function getCurlReturnCode() {
        return $this->curlReturnCode;
    }

    public function getResult() {
        return $this->result;
    }

    public function setCurlStartTime($curlStartTime) {
        $this->curlStartTime = $curlStartTime;
    }

    public function setCurlEndTime($curlEndTime) {
        $this->curlEndTime = $curlEndTime;
    }

    public function setCurlErrorCode($curlErrorCode) {
        $this->curlErrorCode = $curlErrorCode;
    }

    public function setCurlReturnCode($curlReturnCode) {
        $this->curlReturnCode = $curlReturnCode;
    }

    public function setResult($result) {
        $this->result = $result;
    }

    public function getCurlErrorMessage() {
        return $this->curlErrorMessage;
    }

    public function setCurlErrorMessage($curlErrorMessage) {
        $this->curlErrorMessage = $curlErrorMessage;
    }

    function getIsRequestToSolr() {
        return $this->isRequestToSolr;
    }

    function setIsRequestToSolr($isRequestToSolr) {
        $this->isRequestToSolr = $isRequestToSolr;
    }

    public function curl($url, $content = '', $getCurlHeader = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->getRecognizeMobileRequest()) {
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3");
            curl_setopt($ch, CURLOPT_COOKIE, "ci_mobile=mobile");
        }
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        if ($this->isRequestToSolr) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded")); //solr 
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        }
        $curlHeader = 0;
        if ($getCurlHeader) {
            $curlHeader = 1;
        }
        curl_setopt($ch, CURLOPT_HEADER, $curlHeader);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        $curlObjectAfterExecution = $this->_executeCurlRequest($ch);
        return $curlObjectAfterExecution;
    }

    private function _executeCurlRequest($curlObject) {
        $curlStartTime = microtime(true);
        $result = curl_exec($curlObject);
        $curlEndTime = microtime(true);
        $this->setCurlStartTime($curlStartTime);
        $this->setCurlEndTime($curlEndTime);
        if (curl_errno($curlObject)) {
            $this->setCurlErrorCode(curl_errno($curlObject));
            $this->setCurlErrorMessage(curl_error($curlObject));
        } else {
            $curlReturnCode = curl_getinfo($curlObject, CURLINFO_HTTP_CODE);
            $this->setCurlReturnCode($curlReturnCode);
            $this->setResult($result);
        }
        curl_close($curlObject);
        return $this;
    }

}