<?php

namespace Villermen\Minecraft\Service;

use Symfony\Component\Yaml\Yaml;

class AppConfig implements \ArrayAccess
{
    public const PROJECT_ROOT = __DIR__ . '/../..';

    /** @var array */
    protected $config;

    public function __construct()
    {
        $config = Yaml::parseFile(self::PROJECT_ROOT . '/config/app.yml')['app'];
        $config['project_root'] = self::PROJECT_ROOT;

        $this->config = $config;
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \LogicException(sprintf('Config key "%s" does not exist.', $offset));
        }

        return $this->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Cannot change config values.');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('Cannot unset config values.');
    }
}
