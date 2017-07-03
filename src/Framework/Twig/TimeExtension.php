<?php

namespace Framework\Twig;

class TimeExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('time_tag', [$this, 'timeTag'], ['is_safe' => ['html']])
        ];
    }

    public function timeTag(string $date)
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date, new \DateTimeZone(date_default_timezone_get()));

        return
            '<span class="timeago" datetime="' .
            $date->format(\DateTime::ISO8601) .
            '">' .
            $date->format('d/m/Y H:i') .
            '</span>';
    }
}
