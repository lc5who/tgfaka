<?php

return [
    'basic' => [
        'siteName' => '',      // 站点名称
        'customerQQ' => '1231213',    // 客服QQ
    ],
    'payment' => [
        'apiUrl' => '',        // 支付接口地址
        'pid' => '',           // 商户ID
        'key' => '',           // 商户密钥
    ],
    'email' => [
        'smtpServer' => '',    // SMTP服务器地址
        'port' => '',          // 端口号
        'senderEmail' => '',   // 发件人邮箱
        'senderPassword' => '', // 发件人密码
        'encryption' => '',    // 加密方式：ssl, tls, none
    ]
];
