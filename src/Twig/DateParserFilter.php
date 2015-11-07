<?php

namespace allejo\DaPulser\Twig;

class DateParserFilter extends \Twig_Extension
{
    public function getFilters ()
    {
        return array(
            new \Twig_SimpleFilter('parse_date', array($this, 'parseDate'))
        );
    }

    public function parseDate ($string, $format)
    {
        return \DateTime::createFromFormat($format, $string);
    }

    public function getName ()
    {
        return "parse_date";
    }
}