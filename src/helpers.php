<?php

function getPostVars ($fields, $app)
{
    $twigVars = array();

    foreach ($fields as $field)
    {
        $twigVars[$field] = $app->get($field);
    }

    return $twigVars;
}