<?php

namespace allejo\DaPulser\Controller;

use allejo\DaPulse\Pulse;
use allejo\DaPulse\PulseBoard;
use allejo\DaPulse\Utilities\UrlQuery;
use allejo\DaPulser\Twig\DateParserFilter;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MainController
{
    protected $registeredHooks;
    protected $webRequestsId;
    protected $sportBoardId;
    protected $marketingId;
    protected $mindBoardId;
    protected $handshake;
    protected $kevinId;
    protected $joseId;
    protected $vladId;

    public function __construct()
    {
        $this->registeredHooks = array(
            'agendauploads' => array(
                'function'  => 'agendaUploadsPost',
                'form'      => 'kzprsz50049u2g'
            ),
            'discounts'     => array(
                'function'  => 'matadorDiscountPost',
                'form'      => 'rgzqffz1to7860'
            ),
            'genrequest'    => array(
                'function'  => 'marketingRequestPost',
                'form'      => 'zvmgvhj0lmd1so'
            ),
            'mindrequest'   => array(
                'function'  => 'mindRequestPost',
                'form'      => 's1ncm0q514cg77e'
            ),
            'sportrequest'  => array(
                'function'  => 'sportRequestPost',
                'form'      => 'm1dfel500zqgnzz'
            ),
            'webrequest'    => array(
                'function'  => 'webRequestPost',
                'form'      => 'x125g1x00q56u6k'
            )
        );
    }

    public function getRequestAction (Application $app, $hook, $id)
    {
        $this->localHostOnly($app);
        $json = $this->getWufooEntries($app, $this->registeredHooks[$hook]['form'], $id);

        return $app['twig']->render($hook . ".html.twig", $json["Entries"][0]);
    }

    public function sendRequestAction (Application $app, $hook, $id)
    {
        $this->readConfiguration($app);

        /**
         * @var Request $request
         */
        $request = $app["request_stack"]->getCurrentRequest();

        if ($request->get('key') !== $this->handshake)
        {
            $app->abort(403, "A handshake key is required for POST requests.");
        }

        $json     = $this->getWufooEntries($app, $this->registeredHooks[$hook]['form'], $id);
        $fields   = $json["Entries"][0];
        $content  = $app['twig']->render($hook . ".html.twig", $fields);
        $function = $this->registeredHooks[$hook]['function'];
        $pulse_id = $this->$function($content, $fields);

        return $app->json(array(
            "pulse_id" => $pulse_id
        ));
    }

    public function postRequestAction (Request $request, Application $app, $hook)
    {
        $this->readConfiguration($app);

        if ($request->get('HandshakeKey') !== $this->handshake)
        {
            $app->abort(403, "A handshake key is required for POST requests.");
        }

        $fields = $request->request->all();

        // Because of Wufoo's horrendous API, POST data and API data do not match the same structure; this loop will go
        // through all of the fields that have a "*-url" counterpart and convert it into the same format as the API
        // gives us so our Twig files have no issue handling them.
        foreach ($fields as $key => $value)
        {
            if (array_key_exists("$key-url", $fields))
            {
                $fieldUrl = $fields["$key-url"];

                if (!empty($fieldUrl))
                {
                    $fields[$key] = sprintf("%s (%s)", $value, $fieldUrl);
                }
            }
        }

        $content  = $app['twig']->render($hook . ".html.twig", $fields);
        $function = $this->registeredHooks[$hook]['function'];
        $pulse_id = $this->$function($content, $fields);

        return $app->json(array(
            "pulse_id" => $pulse_id
        ));
    }

    //
    // Convenience functions
    //

    private function getWufooEntries(Application $app, $formId, $entryId)
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

    private function localHostOnly(Application $app)
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

    private function readConfiguration (Application $app)
    {
        $this->marketingId   = $app['config']['dapulse']['boards']['marketing'];
        $this->webRequestsId = $app['config']['dapulse']['boards']['web'];
        $this->mindBoardId   = $app['config']['dapulse']['boards']['mind'];
        $this->sportBoardId  = $app['config']['dapulse']['boards']['sports'];
        $this->joseId        = $app['config']['dapulse']['users']['jose'];
        $this->vladId        = $app['config']['dapulse']['users']['vlad'];
        $this->kevinId       = $app['config']['dapulse']['users']['kevin'];
        $this->handshake     = $app['config']['wufoo']['handshake'];
    }

    //
    // DaPulse action functions
    //

    private function agendaUploadsPost ($content, $fields)
    {
        $entryId = $fields["EntryId"];

        $webProjectsBoard = new PulseBoard($this->webRequestsId);
        $newPulse = $webProjectsBoard->createPulse("Agenda Upload #" . $entryId, $this->vladId, "agenda_minutes_uploads");
        $newPulse->getPersonColumn("person")->updateValue($this->vladId);
        $newPulse->addNote("Agenda Upload Information", $content);

        return $newPulse->getId();
    }

    private function matadorDiscountPost ($content, $fields)
    {
        $entryId    = $fields["EntryId"];
        $pulseTitle = sprintf("#%d Matador Discount - %s", $entryId, $fields['Field1']);

        $webProjectsBoard = new PulseBoard($this->webRequestsId);
        $newPulse = $webProjectsBoard->createPulse($pulseTitle, $this->vladId, "matador_discounts");
        $newPulse->getPersonColumn("person")->updateValue($this->vladId);
        $newPulse->addNote("Matador Discount Details", $content);

        return $newPulse->getId();
    }

    private function marketingRequestPost ($content, $fields)
    {
        $entryId     = $fields["EntryId"];
        $eventTitle  = $fields["Field1383"];
        $contactName = $fields["Field1"] . " " . $fields["Field2"];
        $pulseTitle  = sprintf("#%d %s - %s", $entryId, $eventTitle, $contactName);

        $generalMarketingBoard = new PulseBoard($this->marketingId);
        $newPulse = $generalMarketingBoard->createPulse($pulseTitle, $this->kevinId);
        $newPulse->addNote("Request Notes", $content);

        return $newPulse->getId();
    }

    private function mindRequestPost ($content, $fields)
    {
        $pulseTitle = sprintf("#%d %s - %s %s", $fields['EntryId'], $fields['Field6'], $fields['Field4'], $fields['Field5']);

        $mindRequestBoard = new PulseBoard($this->mindBoardId);
        $newPulse = $mindRequestBoard->createPulse($pulseTitle, $this->joseId);
        $newPulse->addNote("MIND Request Notes", $content);

        return $newPulse->getId();
    }

    private function sportRequestPost ($content, $fields)
    {
        $entryId   = $fields["EntryId"];
        $pulseTile = sprintf("#%d %s %s", $entryId, $fields['Field5'], $fields['Field10']);
        $eventDate = DateParserFilter::guessDate($fields['Field116'], array('Ymd', 'Y-m-d'));

        $sportRequestBoard = new PulseBoard($this->sportBoardId);
        $newPulse = $sportRequestBoard->createPulse($pulseTile, $this->kevinId);
        $newPulse->getDateColumn('due_date')->updateValue($eventDate);
        $newPulse->addNote('Sport Club Request', $content);

        return $newPulse->getId();
    }

    private function webRequestPost ($content, $fields)
    {
        $entryId  = $fields["EntryId"];
        $priority = $fields["Field113"];

        $webProjectsBoard = new PulseBoard($this->webRequestsId);
        $newPulse = $webProjectsBoard->createPulse("Web Request #" . $entryId, $this->vladId, "web_requests");
        $newPulse->getTextColumn("text")->updateValue($priority);
        $newPulse->getPersonColumn("person")->updateValue($this->vladId);
        $newPulse->addNote("Web Request Details", $content);

        return $newPulse->getId();
    }
}