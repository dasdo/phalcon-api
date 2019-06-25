<?php

/**
 * Enabled providers. Order does matter.
 */

use Canvas\Providers\CacheDataProvider;
use Gewaer\Providers\ConfigProvider;
use Gewaer\Providers\DatabaseProvider;
use Gewaer\Providers\ErrorHandlerProvider;
use Canvas\Providers\LoggerProvider;
use Gewaer\Providers\ModelsMetadataProvider;
use Canvas\Providers\QueueProvider;
use Gewaer\Providers\MailProvider;
use Canvas\Providers\RedisProvider;
use Canvas\Providers\AclProvider;
use Canvas\Providers\AppProvider;
use Canvas\Providers\FileSystemProvider;
use Gewaer\Providers\EventsManagerProvider;
use Canvas\Providers\CliDispatcherProvider;
use Canvas\Providers\PusherProvider;

return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    CliDispatcherProvider::class,
    CacheDataProvider::class,
    QueueProvider::class,
    MailProvider::class,
    RedisProvider::class,
    PusherProvider::class,
    AclProvider::class,
    AppProvider::class,
    FileSystemProvider::class,
    EventsManagerProvider::class
];
