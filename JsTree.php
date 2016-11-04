<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\jstree;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class JsTree
 * @package xutl\jstree
 */
class JsTree extends Widget
{
    const TREE_TYPE_ADJACENCY = 'adjacency';
    const TREE_TYPE_NESTED_SET = 'nested-set';
    
    public $treeType = self::TREE_TYPE_ADJACENCY;

    /**
     * @var array Enabled jsTree plugins
     * @see http://www.jstree.com/plugins/
     */
    public $plugins = [
        'wholerow',
        'contextmenu',
        'dnd',
        'types',
        'state',
    ];

    /**
     * @var array Configuration for types plugin
     * @see http://www.jstree.com/api/#/?f=$.jstree.defaults.types
     */
    public $types = [
        'show' => [
            'icon' => 'fa fa-file-o',
        ],
        'list' => [
            'icon' => 'fa fa-list',
        ],
    ];

    public $clientOptions = [];

    /**
     * {@inheritDoc}
     * @see \yii\base\Object::init()
     */
    public function init()
    {
        parent::init();
        if (!isset ($this->options ['id'])) {
            $this->options ['id'] = $this->getId();
        }

        $this->clientOptions = array_merge([
            'core' => [
                'themes' => [
                    'responsive' => false,
                    'variant' => 'small',
                    'stripes' => true
                ],
            ],
        ], $this->clientOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::tag('div', '', ['id' => $this->options['id']]);
        $view = $this->getView();
        JsTreeAsset::register($view);
        $options = empty ($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
        $view->registerJs("jQuery(\"#{$this->options['id']}\").jstree({$options});");
    }
}