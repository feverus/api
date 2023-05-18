<?php

//обновление версии базы
function updateVersion($name) {
    global $VERSIONS_FILE_NAME;
    $versions = json_decode(fileGetContents($VERSIONS_FILE_NAME));
    if ($versions===NULL) {
        $versions = json_decode(json_encode(array($name => 0)));
    }
    if (!property_exists($versions, $name)) {
        $versions->$name = 0;
    }
    $versions->$name++;
    filePutContents($VERSIONS_FILE_NAME, json_encode($versions));
}
