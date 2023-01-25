<?php

//обновление версии базы
function updateVersion($name) {
    $versionsFileName = 'base/_versions.txt';
    $versions = json_decode(file_get_contents($versionsFileName));
    if ($versions===NULL) {
        $versions = json_decode(json_encode(array($name => 0)));
    }
    if (!property_exists($versions, $name)) {
        $versions->$name = 0;
    }
    $versions->$name++;
    file_put_contents($versionsFileName, json_encode($versions));

    return $versions->$name;
}
