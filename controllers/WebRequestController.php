<?php

namespace allejo\DaPulser\Controller;

use allejo\DaPulse\PulseBoard;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class WebController
{
    public function webRequestAction(Request $request, Application $app)
    {
        $entryId  = $request->get("EntryId");
        $priority = $request->get("Field113");
        $content  = $app['twig']->render("webrequest.html.twig", $request->request->all());

        $webProjectsBoard = new PulseBoard(2404828);

        $newPulse = $webProjectsBoard->createPulse("Web Request #" . $entryId, 212350, "web_requests");
        $newPulse->getColumn("text")->updateValue($priority);
        $newPulse->getColumn("person")->updateValue(217784);
        $newPulse->addNote("Web Request Details", $content);

        return $app->json(array(
            "pulse_id" => $newPulse->getId()
        ));
    }
}