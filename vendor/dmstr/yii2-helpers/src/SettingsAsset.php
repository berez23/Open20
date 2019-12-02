<?php
/**
 *
 */

namespace dmstr\helpers;


class SettingsAsset
{
    static public function register($view)
    {
        $bundles = explode(
            "\n",
            \Yii::$app->settings->getOrSet('settingsAssetList', 'app\\assets\\AppAsset', 'app.assets', 'string')
        );

        foreach ($bundles as $bundle) {
            $bundle = trim($bundle);
            // ignore empty lines
            if ($bundle === '') {
                continue;
            }
            if (class_exists($bundle)) {
                $bundle::register($view);
            } else {
                \Yii::warning("Asset bundle '{$bundle}' from settings does not exist");
            }
        }
    }
}