<?php

return [
    'phone_rule' => '/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|166|(17[0,3,5-8])|(18[0-9])|(19[1,3,5-9]))\\d{8}$/',
    'tel_rule' => '/^0\d{2,3}-\d{7,8}/',
    'domain_rule' => '/^([a-z0-9-.]*)\.([a-z]{2,8})$/i',
    'username_rule' => '/^[-a-zA-Z0-9_]+$/u',//不含中文
    'nickname_rule' => '/^[-a-zA-Z0-9_\x{4e00}-\x{9fa5}\.@]+$/u',//可以有中文
];
