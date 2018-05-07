<?php

namespace AppBundle\Service;

class AppTools
{
    public static function slugify($slug)
    {
        $slug = preg_replace('/\s|\'/', '-', $slug);
        $slug = str_replace(["à", "â", "ä"], 'a', $slug);
        $slug = str_replace(["ö", "ô"], 'o', $slug);
        $slug = str_replace(["é", "è", "ê", "ë"], 'e', $slug);
        $slug = str_replace(["û", "ü", "ù"], 'u', $slug);
        $slug = str_replace(["ï", "î"], 'i', $slug);

        return htmlspecialchars(strtolower($slug));
    }
}