<?php

namespace botwebapi\resources;

interface iBotWebApiResource
{
    function getName();
    function getVersion();
    function getHomepage();
    function handleRequest();
}

?>
