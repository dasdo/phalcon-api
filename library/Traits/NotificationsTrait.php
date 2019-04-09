<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Gewaer\Models\Users;
use Gewaer\Models\SystemModules;
use Gewaer\Models\Notifications;
use Phalcon\Di;

/**
 * Trait ResponseTrait
 *
 * @package Gewaer\Traits
 *
 * @property Users $user
 * @property AppsPlans $appPlan
 * @property CompanyBranches $branches
 * @property Companies $company
 * @property UserCompanyApps $app
 * @property \Phalcon\Di $di
 *
 */
trait NotificationsTrait
{
    /**
     * Create a new notification
     * @param Users $user
     * @param string $content
     * @param int notification_type_id
     * @param string systemModule;
     * @return void
     */
    public static function create(Users $user, string $content, int $notificationTypeId,string $systemModule): void
    {
        $notification =  new Notifications();
        $notification->users_id = $user->getId();
        $notification->companies_id = Di::getDefault()->getUserData()->currentCompanyId();
        $notification->apps_id = Di::getDefault()->getApp()->getId();
        $notification->notification_type_id = $notificationTypeId;
        $notification->system_module_id = SystemModules::getSystemModuleByModelName($systemModule)->id;
        $notification->entity_id = $user->getId();
        $notification->content = $content;
        $notification->created_at = date('Y-m-d H:i:s');

        if(!$notification->save()){
            Di::getDefault()->getLog()->error((string)current($notification->getMessages()));
        }
    }
}