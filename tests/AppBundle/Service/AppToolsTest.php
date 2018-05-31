<?php

namespace tests\Service;

use AppBundle\Service\AppTools;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AppToolsTest extends KernelTestCase
{
    /**
     * @var AppTools $appTools
     */
    private $appTools;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->appTools = $kernel->getContainer()->get('app.tools');
    }

    public function testSlugify()
    {
        $this->assertEquals("l-entremets-au-chocolat", $this->appTools->slugify("L'entremets au chôcolat"));
        $this->assertEquals("la-tarte-au-citron", $this->appTools->slugify("La Tarte au Citron"));
        $this->assertEquals("foret-noire", $this->appTools->slugify("Forêt Noire"));
        $this->assertEquals("saint-honore", $this->appTools->slugify("Saint-Honoré"));
        $this->assertEquals("mille-feuilles-vanille", $this->appTools->slugify("Mille-feuilles Vanille"));
    }
}