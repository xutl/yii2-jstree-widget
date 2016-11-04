<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\jstree;

use Yii;
use yii\web\AssetBundle;

/**
 * Class JsTreeAsset
 * @package xutl\jstree
 */
class JsTreeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/xutl/yii2-jstree-widget/assets';

    public $js = [
        'jstree.min.js',
    ];

    public $css = [
        'themes/default/style.min.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}