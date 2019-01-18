<?php

namespace Gewaer\Cli\Tasks;

use Phalcon\Cli\Task as PhTask;
use Gewaer\Models\FileSystem;

/**
 * Class AclTask
 *
 * @package Gewaer\Cli\Tasks;
 *
 * @property \Gewaer\Acl\Manager $acl
 */
class FileSystemTask extends PhTask
{
    /**
     * Create the default roles of the system
     *
     * @return void
     */
    public function mainAction()
    {
        echo 'Main action for FileSystem Task';
    }

    /**
     * Default roles for the crm system
     *
     * @return void
     */
    public function purgeImagesAction(array $params):void
    {
        $fullDelete = $params[0];
        $detachedImages = FileSystem::find([
            'conditions' => 'users_id = 0 and is_deleted = 0'
        ]);

        if ($fullDelete == 0 && is_object($detachedImages)) {
            foreach ($detachedImages as $detachedImage) {
                $detachedImage->is_deleted = 1;

                if ($detachedImage->update()) {
                    $output = shell_exec(`rm $detachedImage->path`);
                    echo 'Image with id ' . $detachedImage->id . " has been soft deleted \n";
                }
            }
        } else {
            foreach ($detachedImages as $detachedImage) {
                echo 'Image with id ' . $detachedImage->id . " has been fully deleted \n";
                $detachedImage->delete();
                shell_exec(`rm $detachedImage->path`);
            }
        }
    }
}
