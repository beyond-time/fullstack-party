<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/logout', function (Request $request, Response $response) {
    if ($this->gitHubAuth->isAuthorized()) {
        $this->gitHubAuth->logout();
    }
    return $response->withRedirect('/', 302);
});

$app->get('/', function (Request $request, Response $response) {
    if ($this->gitHubAuth->isAuthorized()) {
        return $response->withRedirect('/pages/1', 302);
    } else {
        $response = $this->renderer->render($response, 'main.phtml', [
                'head' => $this->renderer->fetch('head.phtml', ['title' => ' - Login']),
                'content' => $this->renderer->fetch('login.phtml', ['auth_url' => $this->gitHubAuth->getAuthUrl()]),
            ]
        );
    }
    return $response;
});

$app->get('/pages/{index}', function (Request $request, Response $response, $args) {
    if ($this->gitHubAuth->isAuthorized()) {
        $index = (int)$args['index'];

        $issues_manager = new \GitHub\IssuesManager($this->gitHubAuth, $this->get('settings')->all());

        $open = $issues_manager->getOpenCount();
        $closed = $issues_manager->getClosedCount();
        $pages = $issues_manager->getPagesCount();
        $issues = $issues_manager->getIssuesPage($index);

        $response = $this->renderer->render($response, 'main.phtml', [
                'head' => $this->renderer->fetch('head.phtml', ['title' => ' - Issues']),
                'content' =>
                    $this->renderer->fetch('header.phtml') .
                    $this->renderer->fetch('issues.phtml', [
                            'index' => $index,
                            'issues' => $issues,
                            'open' => $open,
                            'closed' => $closed,
                            'pages' => $pages,
                        ]
                    ),
            ]
        );
    } else {
        return $response->withRedirect('/', 302);
    }

    return $response;
});

$app->get('/{user}/{repo}/issues/{id}', function (Request $request, Response $response, $args) {
    $id = (int)$args['id'];
    $repo = $args['repo'];

    if ($this->gitHubAuth->isAuthorized()) {
        $issues_manager = new \GitHub\IssuesManager($this->gitHubAuth, $this->get('settings')->all());
        $issue = $issues_manager->getIssue($id, $repo);

        $comments = $issue->comments ? $issues_manager->getIssueComments($id, $repo) : [];

        if ($referer = $request->getHeader('referer')[0]) {
            $parts = explode('/pages/', $referer);
            $back_url = '/pages/' . $parts[1];
        } else {
            $back_url = '/pages/1';
        }

        $response = $this->renderer->render($response, 'main.phtml', [
                'head' => $this->renderer->fetch('head.phtml', ['title' => ' - Issue #' . $id]),
                'content' =>
                    $this->renderer->fetch('header.phtml') .
                    $this->renderer->fetch('issue.phtml', [
                            'issue' => $issue,
                            'comments' => $comments,
                            'back_url' => $back_url,
                        ]
                    ),
            ]
        );
    } else {
        return $response->withRedirect('/', 302);
    }

    return $response;
});