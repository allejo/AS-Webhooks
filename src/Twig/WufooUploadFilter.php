<?php

namespace allejo\DaPulser\Twig;

class WufooUploadFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('wufoo_upload', array($this, 'uploadParse'), array('is_safe' => array('html')))
        );
    }

    public function uploadParse ($string)
    {
        $matches = array();

        if (preg_match('/(.*)\s\((.*)\)/', $string, $matches))
        {
            return sprintf("<a href=\"%s\">%s</a>", $matches[2], $matches[1]);
        }

        return $string;
    }

    public function getName ()
    {
        return 'wufoo_upload';
    }
}