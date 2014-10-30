<?php

namespace botwebapi\resources\api\projects;

interface iProjectProvider
{
    function getProjectNames();
    function containsProject($project_name);
    function getProjectResource($project_name, $resource_uri);
}

?>
