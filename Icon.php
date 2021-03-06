<?php
namespace wii\materialize;

use yii\helpers\Html;

/**
 * Icon renders a materialize icon.
 *
 * For example,
 *
 * ```php
 * echo Icon::widget([
 *     'name' => 'editor-mode-edit',
 *     'options' => ['class' => 'large'],
 * ]);
 * ```
 * @see http://materializecss.com/icons.html
 * @author wiisoft <webdev@webmaker.today>
 * @since 1.1
 */
class Icon extends Widget
{
    /**
     * @var string the tag to use to render the icon
     */
    public $tagName = 'i';
    /**
     * @var string the icon name
     */
    public $name;

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        $this->clientOptions = false;
        Html::addCssClass($this->options, 'fa ' . $this->name);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        return Html::tag($this->tagName, '', $this->options);
    }
}
