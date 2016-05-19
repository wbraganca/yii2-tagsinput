<?php
/**
 * @link      https://github.com/wbraganca/yii2-tagsinput
 * @copyright Copyright (c) 2015 Wanderson Bragança
 * @license   https://github.com/wbraganca/yii2-tagsinput/blob/master/LICENSE
 */

namespace wbraganca\tagsinput;

/**
 * Asset bundle for tagsinput Widget
 *
 * @author Wanderson Bragança <wanderson.wbc@gmail.com>
 */
class TagsinputAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/bootstrap-tagsinput/dist';

    public $css = [
        'bootstrap-tagsinput.css',
    ];

    public $js = [
        'bootstrap-tagsinput.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'wbraganca\tagsinput\TypeaheadAsset'
    ];
}
