<?php
namespace wii\materialize;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Nav renders a nav HTML component.
 *
 * For example:
 *
 * ```php
 * echo Nav::widget([
 *     'items' => [
 *         [
 *             'label' => 'Home',
 *             'url' => ['site/index'],
 *             'linkOptions' => [...],
 *         ],
 *         [
 *             'label' => 'Dropdown',
 *             'items' => [
 *                  ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
 *                  '<li class="divider"></li>',
 *                  '<li class="dropdown-header">Dropdown Header</li>',
 *                  ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
 *             ],
 *         ],
 *     ],
 *     'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
 * ]);
 * ```
 * @see http://materializecss.com/navbar.html
 * @author wiisoft <webdev@webmaker.today>
 * @since 1.2
 */
class Nav extends Widget
{
    /**
     * @var array list of items in the nav widget. Each array element represents a single
     * menu item which can be either a string or an array with the following structure:
     *
     * - label: string, required, the nav item label.
     * - url: optional, the item's URL. Defaults to "#".
     * - visible: boolean, optional, whether this menu item is visible. Defaults to true.
     * - linkOptions: array, optional, the HTML attributes of the item's link.
     * - options: array, optional, the HTML attributes of the item container (LI).
     * - active: boolean, optional, whether the item should be on active state or not.
     * - items: array|string, optional, the configuration array for creating a [[Dropdown]] widget,
     *   or a string representing the dropdown menu. Note that Bootstrap does not support sub-dropdown menus.
     *
     * If a menu item is a string, it will be rendered directly without HTML encoding.
     */
    public $items = [];
    /**
     * @var boolean whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive
     */
    public $activateItems = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     */
    public $activateParents = false;
    /**
     * @var string the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive
     */
    public $route;
    /**
     * @var array the parameters used to determine if a menu item is active or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isItemActive
     */
    public $params;
    /**
     * @var boolean if true, then its menu used as sidenav for mobile.
     */
    public $buttonCollapse = false;
    /**
     * @var string the text of the button collapse. Note that this is not HTML-encoded.
     */
    public $buttonCollapseLabel = '<i class="fa fa-bars"></i>';
    /**
     * @var array the HTML attributes of the button collapse.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $buttonCollapseOptions = [];

    /**
     * @var array list of items for Mobile menu.
     * Using:
     *
     *  'mobileItems'=>[
     *      [
     *          'label' => 'Home',
     *          'url' => ['site/index'],
     *          'linkOptions' => [],
     *      ],
     *      [
     *          'label' => 'Articles',
     *          'url' => ['service/articles']
     *      ],
     *      [
     *          'label' => 'Contacts',
     *          'url' => ['service/contacts']
     *      ]
     *   ]
     *
     *
     * - label: string, required, the nav item label.
     * - url: optional, the item's URL. Defaults to "#".
     * - visible: boolean, optional, whether this menu item is visible. Defaults to true.
     * - linkOptions: array, optional, the HTML attributes of the item's link.
     * - options: array, optional, the HTML attributes of the item container (LI).
     * - active: boolean, optional, whether the item should be on active state or not.
     * - items: array|string, optional, the configuration array for creating a [[Dropdown]] widget,
     *   or a string representing the dropdown menu. Note that Bootstrap does not support sub-dropdown menus.
     *
     * If a menu item is a string, it will be rendered directly without HTML encoding.
     */

    public $mobileItems =[];

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        if ($this->buttonCollapse) {
            Html::addCssClass($this->buttonCollapseOptions, 'button-collapse');
            $this->buttonCollapseOptions['id'] = $this->id . '-button-collapse';
            $this->buttonCollapseOptions['data-activates'] = $this->id . '-mobile';
            echo Html::a($this->buttonCollapseLabel, '#', $this->buttonCollapseOptions);
        }
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        Html::addCssClass($this->options, 'nav');
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        MaterializeAsset::register($this->getView());
        if ($this->buttonCollapse) {
            MaterializeAsset::register($this->getView());
            $this->getView()->registerJs('$("#' . $this->id . '-button-collapse").sideNav();');
        }
        return $this->renderItems(). $this->renderMobileMenuItems();
    }

    /**
     * Renders widget items.
     */
    public function renderItems($items=null)
    {

        $menuItems = (!empty($items))?$items:$this->items;
        $items = [];
        foreach ($menuItems as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item);
        }
        return Html::tag('ul', implode("\n", $items), $this->options);
    }

    /**
     * Render mobile menu items
     * @return string
     */
    public function  renderMobileMenuItems(){
        $this->options['class']='side-nav';
        $this->options['id']=$this->buttonCollapseOptions['data-activates'];
        if(!empty($this->mobileItems)){
            $mobileItems = $this->mobileItems;
            return $this->renderItems($mobileItems);
        }else{
            return $this->renderItems();
        }
    }

    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if ($items !== null) {
            $dropdownId = uniqid($this->id . '-dropdown_');
            Html::addCssClass($options, 'dropdown');
            Html::addCssClass($linkOptions, 'dropdown-button');
            $linkOptions['id'] = $dropdownId . '-btn';
            $linkOptions['data-activates'] = $dropdownId;
            $label .= ' ' . Icon::widget(['name' => 'fa-caret-down right']);
            if (is_array($items)) {
                if ($this->activateItems) {
                    $items = $this->isChildActive($items, $active);
                }
                $items = $this->renderDropdown($items, $item, $dropdownId);
            }
        }

        if ($this->activateItems && $active) {
            Html::addCssClass($options, 'active');
        }
        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }

    /**
     * Renders the given items as a dropdown.
     * This method is called to create sub-menus.
     * @param array $items the given items. Please refer to [[Dropdown::items]] for the array structure.
     * @param array $parentItem the parent item information. Please refer to [[items]] for the structure of this array.
     * @return string the rendering result.
     * @since 1.0.1
     */
    protected function renderDropdown($items, $parentItem, $dropdownId)
    {
        return Dropdown::widget([
            'id' => $dropdownId,
            'buttonLabel' => null,
            'items' => $items,
            'encodeLabels' => $this->encodeLabels,
            'clientOptions' => false,
            'view' => $this->getView(),
        ]);
    }

    /**
     * Check to see if a child item is active optionally activating the parent.
     * @param array $items @see items
     * @param boolean $active should the parent be active too
     * @return array @see items
     */
    protected function isChildActive($items, &$active)
    {
        foreach ($items as $i => $child) {
            if (ArrayHelper::remove($items[$i], 'active', false) || $this->isItemActive($child)) {
                Html::addCssClass($items[$i]['options'], 'active');
                if ($this->activateParents) {
                    $active = true;
                }
            }
        }
        return $items;
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                foreach (array_splice($item['url'], 1) as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
