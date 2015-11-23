<?php

namespace allejo\DaPulser\Controller;

use allejo\DaPulse\Utilities\UrlQuery;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class BaseController
{
    protected $marketingId;
    protected $webRequestsId;
    protected $handshake;
    protected $vladId;
    protected $kevinId;

    protected function configureIncomingRequest (Request $request, Application $app)
    {
        $this->marketingId   = $app['config']['dapulse']['boards']['marketing'];
        $this->webRequestsId = $app['config']['dapulse']['boards']['web'];
        $this->vladId        = $app['config']['dapulse']['users']['vlad'];
        $this->kevinId       = $app['config']['dapulse']['users']['kevin'];
        $this->handshake     = $app['config']['wufoo']['handshake'];

        if ($request->get('HandshakeKey') === $this->handshake)
        {
            $app->abort(403, "A handshake key is required for POST requests.");
        }
    }

    protected function localHostOnly(Application $app)
    {
        /**
         * @var Request $request
         */
        $request = $app["request_stack"]->getCurrentRequest();
        $localhost = array("localhost", "127.0.0.1", "::1");

        if (!in_array($request->getClientIp(), $localhost))
        {
            $app->abort(403, "This function is only intended for development environments.");
        }
    }

    protected function getWufooEntries(Application $app, $formId, $entryId)
    {
        $wufooApiEndpoint = "https://%s.wufoo.com/api/v3/forms/%s/entries.json";
        $url = sprintf($wufooApiEndpoint, $app["config"]["wufoo"]["subdomain"], $formId);
        $params = array(
            "sort" => "EntryId",
            "sortDirection" => "DESC",
            "Filter1" => "EntryId Is_equal_to " . $entryId
        );

        $urlJob = new UrlQuery($url, $params);

        // Wufoo doesn't use a password, so let's use bacon
        $urlJob->setAuthentication($app["config"]["wufoo"]["apikey"], "bacon");

        return $urlJob->sendGet();
    }
}