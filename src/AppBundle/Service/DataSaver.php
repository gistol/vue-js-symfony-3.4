<?php

namespace AppBundle\Service;

use AppBundle\Entity\Statistic;

/**
 * Class TraficSaver
 * @package AppBundle\Service
 */
class DataSaver
{
    /**
     * @var MetaService
     */
    protected $metaService;

    /**
     * TraficSaver constructor.
     * @param MetaService $metaService
     */
    public function __construct(MetaService $metaService)
    {
        $this->metaService = $metaService;
    }

    /**
     * @param $data
     */
    public function registerData($data, $type)
    {
        $this->metaService->persistAndFlush([
            (new Statistic())
                ->setData($data)
                ->setDate(new \DateTime())
                ->setBot($this->checkIfBot())
                ->setType($type)
        ]);
    }

    /**
     * @return bool
     */
    private function checkIfBot()
    {
        $array = explode(" ", $this->metaService->getRequest()->server->get("HTTP_USER_AGENT"));

        foreach ($array as $value) {
            if (preg_match("/YandexBot|YandexImages|GigablastOpenSource|Mail.RU_Bot|Pinterestbot|MJ12bot|ScoutJet|Snarfer|SimplePie|AdsBot-Google-Mobile|sogou|BLEXBot|Sitebot|zspider|Charlotte|Gigabot|msnbot|okhttp|ips-agent|SiteLockSpider|MSRbot|mxbot|Googlebot|bingbot|AdsBot-Google-Mobile|Googlebot-Mobile|Slurp|DuckDuckBot|Baiduspider|facebookexternalhit|Exabot|Konqueror|Trident|facebot|ia_archiver/", $value)) {
                return true;
            }
        }

        return false;
    }

    public function getStatistics($start, $end)
    {

    }
}