<?php

namespace BaseApp;

/**
 * @param $request
 */
function homepageScreen($request) {


    $teng = $request->appInit->getTeng();

    $db = $request->appInit->getDb();

    $db->begin();

    $res = $db->execute("SELECT NOW() as adt");

    $row = $res->fetch_object();

    $db->commit();

    $teng->addFragment(Null, "test", array("name" => "Martin", "surname" => "King", "dateTime" => $row->adt));

    return \Leolos\Status\Status::OK($teng->generatePage("homepage.html", $request->appInit->getLanguage()));
}

