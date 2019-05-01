<?php

/**
 * Enabled handlers. Order does matter
 */

use Canvas\Handlers\PushNotifications;
use Canvas\Handlers\EmailNotifications;

return [
    new PushNotifications(),
    new EmailNotifications()
];
