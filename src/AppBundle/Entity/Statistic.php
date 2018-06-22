<?php

namespace AppBundle\Entity;

/**
 * Statistic
 */
class Statistic
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $bot;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Statistic
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Statistic
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set bot
     *
     * @param boolean $bot
     *
     * @return Statistic
     */
    public function setBot($bot)
    {
        $this->bot = $bot;

        return $this;
    }

    /**
     * Get bot
     *
     * @return bool
     */
    public function getBot()
    {
        return $this->bot;
    }
}

