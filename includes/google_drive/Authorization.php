<?php
require_once dirname(__FILE__).'/apiclient/vendor/autoload.php';
require_once dirname(__FILE__).'/decrypt.php';
class Google_Auth {
    private $client, $service;
    public function create_client() {
        global $gd_clientid, $gd_clientsecret, $gd_redirect_uris;
        $this->client = new Google_Client();
        //$this->client->setAuthConfig("client_secret.json"); //cleartext
        $this->client->setClientId($gd_clientid);
        $this->client->setClientSecret($gd_clientsecret);
        $this->client->setRedirectUri($gd_redirect_uris[0]);
        $this->client->setAccessType('offline');
        $this->client->addScope(implode(' ', array(Google_Service_Drive::DRIVE)));
    }
    public function create_service() {
        $this->service = new Google_Service_Drive($this->client);
        return $this->service;
    }
    public function get_authorization($authcode) {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authcode);
        return $accessToken;
    }
    public function set_authorization($accessToken) {
        $this->client->setAccessToken($accessToken);
    }
    public function get_authorizationURL() {
        if ($this->client != null) {
            return $this->client->createAuthUrl();
        }
    }
    public function check_authorization() {
        if ($this->client->isAccessTokenExpired()) {
            $this->refresh_authorization();
            return true;
        } else {
            return false;
        }
    }
    public function refresh_authorization() {
        $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
        return $this->client->getAccessToken();
    }
}

?>