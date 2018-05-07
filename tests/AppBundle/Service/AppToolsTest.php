<?php

namespace tests\Service;

use AppBundle\Service\AppTools;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AppToolsTest extends KernelTestCase
{
    public function testSlugify()
    {
        $this->assertEquals("l-entremets-au-chocolat", AppTools::slugify("L'entremets au chôcolat"));
        $this->assertEquals("la-tarte-au-citron", AppTools::slugify("La Tarte au Citron"));
        $this->assertEquals("foret-noire", AppTools::slugify("Forêt Noire"));
        $this->assertEquals("saint-honore", AppTools::slugify("Saint-Honoré"));
        $this->assertEquals("mille-feuilles-vanille", AppTools::slugify("Mille-feuilles Vanille"));
    }
}