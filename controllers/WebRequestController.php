<?php

namespace allejo\DaPulser\Controller;

use allejo\DaPulse\PulseBoard;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class WebController extends BaseController
{
    public function twigAgendaRequestAction (Application $app, $id)
    {
        $this->localHostOnly($app);
        $json = $this->getWufooEntries($app, "kzprsz50049u2g", $id);

        return $app['twig']->render("agendaupload.html.twig", $json["Entries"][0]);
    }

    public function postAgendaRequestAction (Request $request, Application $app)
    {
        $this->configureIncomingRequest($request, $app);

        $entryId = $request->get("EntryId");
        $content = $app['twig']->render("agendaupload.html.twig", $request->request->all());

        $webProjectsBoard = new PulseBoard($this->webRequestsId);

        $newPulse = $webProjectsBoard->createPulse("Agenda Upload #" . $entryId, $this->kevinId, "agenda_minutes_uploads");
        $newPulse->getPersonColumn("person")->updateValue($this->vladId);
        $newPulse->addNote("Agenda Upload Information", $content);
    }

    public function twigWebRequestAction (Application $app, $id)
    {
        $this->localHostOnly($app);
        $json = $this->getWufooEntries($app, "x125g1x00q56u6k", $id);

        return $app['twig']->render("webrequest.html.twig", $json["Entries"][0]);
    }

    public function postWebRequestAction (Request $request, Application $app)
    {
        $this->configureIncomingRequest($request, $app);

        $entryId  = $request->get("EntryId");
        $priority = $request->get("Field113");
        $content  = $app['twig']->render("webrequest.html.twig", $request->request->all());

        $webProjectsBoard = new PulseBoard($this->webRequestsId);

        $newPulse = $webProjectsBoard->createPulse("Web Request #" . $entryId, $this->kevinId, "web_requests");
        $newPulse->getTextColumn("text")->updateValue($priority);
        $newPulse->getPersonColumn("person")->updateValue($this->vladId);
        $newPulse->addNote("Web Request Details", $content);

        return $app->json(array(
            "pulse_id" => $newPulse->getId()
        ));
    }
}