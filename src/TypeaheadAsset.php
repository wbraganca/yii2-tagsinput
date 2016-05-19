<?php
/**
 * @link      https://github.com/wbraganca/yii2-tagsinput
 * @copyright Copyright (c) 2015 Wanderson Bragança
 * @license   https://github.com/wbraganca/yii2-tagsinput/blob/master/LICENSE
 */

namespace wbraganca\tagsinput;

/**
 *
 * @author Avikaresha Saha <avikarsha.saha@gmail.com>
 * @since 2.0
 */
class TypeAheadAsset extends yii\web\AssetBundle;
{
    public $sourcePath = '@bower/typeahead.js/dist';
    public $js = [
        'typeahead.bundle.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
