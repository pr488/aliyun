<?php

/*
 * This file is part of the pr488/aliyun.
 *
 * (c) pr488 <pr488@hotmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aliyun;

/**
 * Class Factory.
 *
 * @method static Push\Application push(array $config)
 */
class Factory
{
    /**
     * @param string $namespace
     * @param array  $config
     *
     * @return mixed
     */
    public static function make($namespace, array $config)
    {
        $application = "\\Aliyun\\{$namespace}\\Application";

        return new $application($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
}
