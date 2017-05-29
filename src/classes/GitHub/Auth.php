<?php

namespace GitHub;

use \GuzzleHttp\Exception\ClientException as ClientException;
use \GitHub\Helpers\Response as ResponseHelper;

class Auth
{
    public $httpClient;

    private $code;
    public $accessToken;

    private $user;
    private $isAuthorized = false;

    private $client_id;
    private $client_secret;

    function __construct(array $settings)
    {
        $this->httpClient = new \GuzzleHttp\Client();

        $this->client_id = $settings['id'];
        $this->client_secret = $settings['secret'];

        if (isset($_SESSION['code'])) {
            $this->code = $_SESSION['code'];
        } elseif (isset($_GET['code']) && strlen($_GET['code']) == 20 && ctype_xdigit($_GET['code'])) {
            $this->code = $_GET['code'];
            $_SESSION['code'] = $this->code;
        }

        if ($this->code) {
            if (isset($_SESSION['access_token'])) {
                $this->accessToken = $_SESSION['access_token'];
            } elseif ($this->code) {
                $this->accessToken = $this->getAccessToken();
                $_SESSION['access_token'] = $this->accessToken;
            }
        }

        if ($this->code && $this->accessToken) {
            try {
                $this->user = $this->getUser();
            } catch (ClientException $e) {
            }

            $this->isAuthorized = !isset($e);
        }
    }

    public function getAuthUrl()
    {
        return 'https://github.com/login/oauth/authorize?scope=repo&client_id=' . $this->client_id;
    }

    public function getAccessToken()
    {
        $response = $this->httpClient->request('POST', 'https://github.com/login/oauth/access_token', [
            'headers' => ['Accept' => 'application/json'],
            'query' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $this->code,
            ]
        ]);

        $access_token = ResponseHelper::getData($response)->access_token;

        return $access_token;
    }

    public function getUser()
    {
        $response = $this->httpClient->request('GET', 'https://api.github.com/user', [
            'headers' => [
                'Authorization' => 'token ' . $this->accessToken,
                'Accept' => 'application/json',
            ]
        ]);

        return ResponseHelper::getData($response);
    }

    public function isAuthorized()
    {
        return $this->isAuthorized;
    }

    public function getUsername()
    {
        return $this->user->login;
    }

    public function logout()
    {
        unset(
            $_SESSION['code'],
            $_SESSION['access_token']
        );
        $this->isAuthorized = false;
    }
}