<?php
return [
    // 选择与上面步骤创建 project 所属区域匹配的 Endpoint
    'endpoint'    => env('SLS_ENDPOINT'),
    // 使用你的阿里云访问秘钥 AccessKeyId
    'accessKeyId' => env('ALIYUN_ACCESS_KEY_ID'),
    // 使用你的阿里云访问秘钥 AccessKeySecret
    'accessKey'   => env('ALIYUN_ACCESS_KEY_SECRET'),
    // 上面步骤创建的项目名称
    'project'     => env('SLS_PROJECT'),
    // 上面步骤创建的日志库名称
    'logstore'    => env('SLS_STORE'),
];