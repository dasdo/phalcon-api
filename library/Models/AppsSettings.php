<?php
declare(strict_types=1);

namespace Gewaer\Models;
use Gewaer\Models\Apps;

class AppsSettings extends AbstractModel
{
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
    public $value;

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

        $this->setSource('apps_settings');

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
        return 'apps_settings';
    }

    /**
     * Get default Apps Settings value by name
     * @param $name
     * @return string
     */
    public static function getDefaultAppsSettingsByName(string $name): string
    {
        return self::findFirst([
            'conditions'=>'apps_id = ?0 and name = ?1 and is_deleted = 0',
            'bind'=>[Apps::GEWAER_DEFAULT_APP_ID,$name]
        ])->value;
    }
}
