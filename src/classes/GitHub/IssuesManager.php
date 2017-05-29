<?php

namespace GitHub;

use \GitHub\Helpers\Response as ResponseHelper;

class IssuesManager
{
    private $auth;
    private $perPage;
    private $username;

    private $_apiBaseUri = 'https://api.github.com/';

    function __construct(\GitHub\Auth $auth, array $settings)
    {
        $this->auth = $auth;
        $this->perPage = $settings['issues']['perPage'];
        $this->username = $settings['testMode'] ? 'octocat' : $this->auth->getUsername();
    }

    public function getOpenCount()
    {
        // Guzzle filters out '+' char so we need to use direct URL.
        $response = $this->auth->httpClient->request(
            'GET',
            $this->_apiBaseUri . 'search/issues?q=user:' . $this->username . '+state:open&per_page=' . $this->perPage,
            [
                'headers' => ['Authorization' => 'token ' . $this->auth->accessToken]
            ]
        );

        return ResponseHelper::getData($response)->total_count;
    }

    public function getClosedCount()
    {
        $response = $this->auth->httpClient->request(
            'GET',
            $this->_apiBaseUri . 'search/issues?q=user:' . $this->username . '+state:closed&per_page=' . $this->perPage,
            [
                'headers' => ['Authorization' => 'token ' . $this->auth->accessToken]
            ]
        );

        return ResponseHelper::getData($response)->total_count;
    }

    public function getPagesCount()
    {
        $response = $this->auth->httpClient->request(
            'HEAD',
            $this->_apiBaseUri . 'search/issues?q=user:' . $this->username . '+state:open+state:closed&per_page=' . $this->perPage,
            [
                'headers' => ['Authorization' => 'token ' . $this->auth->accessToken]
            ]
        );

        if ($header = $response->getHeader('Link')[0]) {
            preg_match('/page=(\d+)>; rel="last"$/', $header, $matches);
            return $matches[1];
        }

        return 1;
    }

    public function getIssuesPage($page)
    {
        $response = $this->auth->httpClient->request(
            'GET',
            $this->_apiBaseUri . 'search/issues?q=user:' . $this->username . '+state:closed+state:open&page=' . $page . '&per_page=' . $this->perPage,
            [
                'headers' => ['Authorization' => 'token ' . $this->auth->accessToken]
            ]
        );
        return ResponseHelper::getData($response)->items;
    }

    public function getIssue($id, $repo)
    {
        $response = $this->auth->httpClient->request(
            'GET',
            $this->_apiBaseUri . 'repos/' . $this->username . '/' . $repo . '/issues/' . $id,
            [
                'headers' => ['Authorization' => 'token ' . $this->auth->accessToken]
            ]
        );
        return ResponseHelper::getData($response);
    }

    public function getIssueComments($id, $repo)
    {
        $response = $this->auth->httpClient->request(
            'GET',
            $this->_apiBaseUri . 'repos/' . $this->username . '/' . $repo . '/issues/' . $id . '/comments',
            [
                'headers' => ['Authorization' => 'token ' . $this->auth->accessToken]
            ]
        );

        return ResponseHelper::getData($response);
    }
}
