<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Exception\ModelException;
use Phalcon\Di;

class AppsPlans extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $stripe_id;

    /**
     *
     * @var string
     */
    public $stripe_plan;

    /**
     *
     * @var double
     */
    public $pricing;

    /**
     *
     * @var integer
     */
    public $currency_id;

    /**
     *
     * @var integer
     */
    public $free_trial_dates;

    /**
     *
     * @var integer
     */
    public $is_default;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var integer
     */
    public $is_deleted;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('apps_plans');

        $this->belongsTo(
            'apps_id',
            'Gewaer\Models\Apps',
            'id',
            ['alias' => 'app']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'apps_plans';
    }

    /**
     * Just a preatty function that returns the same object for
     *
     * $app->settings()->set(key, value);
     * $app->settings()->get(key);
     * $app->settings()->get(key)->delete();
     *
     * @return AppsPlans
     */
    public function settings(): AppsPlans
    {
        return $this;
    }

    /**
     * Get the default plan for this given app
     *
     * @return AppsPlans
     */
    public static function getDefaultPlan(): AppsPlans
    {
        return AppsPlans::findFirst([
            'conditions' => 'apps_id = ?0 and is_default = 1',
            'bind' => [Di::getDefault()->getApp()->getId()]
        ]);
    }

    /**
     * Get the value of the settins by it key
     *
     * @param string $key
     * @param string $value
     */
    public function get(string $key) : string
    {
        $setting = AppsPlansSettings::findFirst([
            'conditions' => 'apps_plans_id = ?0 and apps_id = ?1 and key = ?2',
            'bind' => [$this->getId(), $this->apps_id, $key]
        ]);

        if (is_object($setting)) {
            return $setting->value;
        }

        throw new ServerErrorHttpException(_('No settings found with for ' . $key . ' at this app ' . $this->apps_id));
    }

    /**
     * Set a setting for the given app
     *
     * @param string $key
     * @param string $value
     */
    public function set(string $key, $value) : bool
    {
        $setting = AppsPlansSettings::findFirst([
            'conditions' => 'apps_plans_id = ?0 and apps_id = ?1 and key = ?2',
            'bind' => [$this->getId(), $this->apps_id, $key]
        ]);

        if (!is_object($setting)) {
            $setting = new AppsPlansSettings();
            $setting->apps_plans_id = $this->getId();
            $setting->apps_id = $this->getId();
            $setting->key = $key;
        }

        $setting->value = $value;

        if (!$setting->save()) {
            throw new ModelException((string) current($setting->getMessages()));
        }

        return true;
    }
}
