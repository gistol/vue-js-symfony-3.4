<?php

namespace AppBundle\Service;

use AppBundle\Entity\Statistic;

class TraficSaver
{
    /**
     * @var MetaService
     */
    protected $metaService;

    public function __construct(MetaService $metaService)
    {
        $this->metaService = $metaService;
    }

    public function registerUrl($url)
    {
        $this->metaService->persistAndFlush([
            (new Statistic())->setUrl($url)->setDate(new \DateTime())->setBot($this->checkIfBot())
        ]);
    }

    /**
     * @return bool
     */
    private function checkIfBot()
    {
        return;
    }

    public function getStatistics($start, $end)
    {

    }
}