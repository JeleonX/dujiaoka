<?php
/**
 * The file was created by Assimon.
 *
 * @author    assimon<ashang@utf8.hk>
 * @copyright assimon<ashang@utf8.hk>
 * @link      http://utf8.hk/
 */

return [
    'labels' => [
        'SystemSetting' => 'System Settings',
        'system_setting' => 'System Settings',
        'base_setting' => 'Basic Settings',
        'mail_setting' => 'Mail Service',
        'order_push_setting' => 'Order Push Configuration',
        'geetest' => 'GeeTest Verification',
    ],

    'fields' => [
        'title' => 'Website Title',
        'text_logo' => 'Text LOGO',
        'img_logo' => 'Image LOGO',
        'keywords' => 'Website Keywords',
        'description' => 'Website Description',
        'notice' => 'Site Notice',
        'footer' => 'Footer Custom Code',
        'manage_email' => 'Admin Email',
        'is_open_anti_red' => 'Enable WeChat/QQ Anti-Redirection',
        'is_open_img_code' => 'Enable Image Verification Code',
        'is_open_search_pwd' => 'Enable Search Password',
        'is_open_google_translate' => 'Enable Google Translate',

        'is_open_server_jiang' => 'Enable ServerChan',
        'server_jiang_token' => 'ServerChan Communication Token',
        'is_open_telegram_push' => 'Enable Telegram Push',
        'telegram_userid' => 'Telegram User ID',
        'telegram_bot_token' => 'Telegram Communication Token',
		'is_open_bark_push' => 'Enable Bark Push',
		'is_open_bark_push_url' => 'Push Order URL with Bark',
		'bark_server' => 'Bark Server',
		'bark_token' => 'Bark Communication Token',
		'is_open_qywxbot_push' => 'Enable WeChat Work Bot Push',
		'qywxbot_key' => 'WeChat Work Bot Communication Key',

        'template' => 'Site Template',
        'language' => 'Site Language',
        'order_expire_time' => 'Order Expiration Time (Minutes)',

        'driver' => 'Mail Driver',
        'host' => 'SMTP Server Address',
        'port' => 'Port',
        'username' => 'Username',
        'password' => 'Password',
        'encryption' => 'Encryption',
        'from_address' => 'Sender Address',
        'from_name' => 'Sender Name',

        'geetest_id' => 'GeeTest ID',
        'geetest_key' => 'GeeTest Key',
        'is_open_geetest' => 'Enable GeeTest',
    ],
    'options' => [
    ],
    'rule_messages' => [
        'save_system_setting_success' => 'System settings saved successfully!',
        'change_reboot_php_worker' => 'Some configuration changes require restarting [supervisor] or PHP process management tools to take effect, such as mail service, ServerChan, etc.'
    ]
];
