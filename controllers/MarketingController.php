<?php

namespace allejo\DaPulser\Controller;

use allejo\DaPulse\PulseBoard;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MarketingController extends BaseController
{
    public function twigGeneralMarketingAction (Application $app, $id)
    {
        $this->localHostOnly($app);
        $json = $this->getWufooEntries($app, "zvmgvhj0lmd1so", $id);

        return $app['twig']->render("marketingrequest.html.twig", $json["Entries"][0]);
    }

    public function postGeneralMarketingAction (Request $request, Application $app)
    {
        $this->configureUniqueIds($app);

        $entryId     = $request->get("EntryId");
        $eventTitle  = $request->get("Field1383");
        $contactName = $request->get("Field1") . " " . $request->get("Field2");
        $content     = $app['twig']->render("marketingrequest.html.twig", $request->request->all());
        $pulseTitle  = sprintf("#%d %s - %s", $entryId, $eventTitle, $contactName);

        $generalMarketingBoard = new PulseBoard($this->marketingId);

        $newPulse = $generalMarketingBoard->createPulse($pulseTitle, $this->kevinId);
        $newPulse->addNote("Request Notes", $content);

        return $app->json(array(
            "pulse_id" => $newPulse->getId()
        ));
    }
}