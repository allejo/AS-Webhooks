<?php

namespace allejo\DaPulser\Controller;

use allejo\DaPulse\PulseBoard;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MarketingController
{
    public function generalMarketingAction(Request $request, Application $app)
    {
        $entryId     = $request->get("EntryId");
        $eventTitle  = $request->get("Field1383");
        $contactName = $request->get("Field1") . " " . $request->get("Field2");
        $content     = $app['twig']->render("marketingrequest.html.twig", $request->request->all());
        $pulseTitle  = sprintf("#%d %s - %s", $entryId, $eventTitle, $contactName);

        $generalMarketingBoard = new PulseBoard(2670709);

        $newPulse = $generalMarketingBoard->createPulse($pulseTitle, 212350);
        $newPulse->addNote("Request Notes", $content);

        return $app->json(array(
            "pulse_id" => $newPulse->getId()
        ));
    }
}