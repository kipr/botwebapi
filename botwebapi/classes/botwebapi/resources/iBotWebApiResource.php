<?php

namespace botwebapi\resources;

interface iBotWebApiResource
{
    function getName();
    function getHomepage();
    function handleRequest();
}

?>
