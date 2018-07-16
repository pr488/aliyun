<?php

/*
 * This file is part of the pr488/aliyun.
 *
 * (c) pr488 <pr488@hotmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aliyun\Push\Push;

use Aliyun\Core\BaseClient;
use Aliyun\Core\ServiceContainer;

class Client extends BaseClient
{
    protected $version = '2016-08-01';

    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app);
        $this->accessKeyId     = $app['config']['push']['access_key_id'];
        $this->accessKeySecret = $app['config']['push']['access_key_secret'];
    }

    public function notice($notice, $type)
    {
        $data = [
            'Action'      => $type == 'ios' ? 'PushNoticeToiOS' : 'PushNoticeToAndroid',
            'AppKey'      => $this->app['config']['push']['app_key'][$type],
            'Target'      => $notice['target'],
            'TargetValue' => $notice['targetValue'],
            'Title'       => $notice['title'],
            'Body'        => $notice['body'],
        ];

        if ($type == 'ios') {
            $data['ApnsEnv'] = $this->app['config']['debug'] ? 'DEV' : 'PRODUCT';
        }

        return $this->httpPost('', $data);
    }
}
