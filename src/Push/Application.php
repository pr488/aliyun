<?php

/*
 * This file is part of the pr488/aliyun.
 *
 * (c) pr488 <pr488@hotmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aliyun\Push;

use Aliyun\Core\ServiceContainer;

/**
 * Class Application
 *
 * @property Push\Client $push
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Push\ServiceProvider::class,
    ];
}
