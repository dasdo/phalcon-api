<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Gewaer\Models\Apps;
use Gewaer\Models\AppsSettings;
use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Exception\ModelException;

/**
 * Trait ResponseTrait
 *
 * @package Gewaer\Traits
 *
 * @property Users $user
 * @property Config $config
 * @property Request $request
 * @property Auth $auth
 * @property \Phalcon\Di $di
 *
 */
trait AppsSettingsTrait
{
    /**
     * Get default Apps Settings value by name
     * @param $name
     * @return string
     */
    public function getDefaultAppsSettingsByName(string $name): string
    {
        return AppsSettings::findFirst([
            'conditions'=>'apps_id = ?0 and name = ?1 and is_deleted = 0',
            'bind'=>[Apps::GEWAER_DEFAULT_APP_ID,$name]
        ])->value;
    }
}