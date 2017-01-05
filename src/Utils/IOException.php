<?php


namespace TheCodingMachine\Discovery\Utils;


class IOException extends \RuntimeException
{
    private $path;

    public function __construct($message, $code = 0, \Exception $previous = null, $path = null)
    {
        $this->path = $path;

        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}