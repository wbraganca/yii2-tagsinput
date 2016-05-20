<?php
/**
 * @link      https://github.com/wbraganca/yii2-tagsinput
 * @copyright Copyright (c) 2015 Wanderson Bragança
 * @license   https://github.com/wbraganca/yii2-tagsinput/blob/master/LICENSE
 */
namespace wbraganca\tagsinput;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * The yii2-tagsinput is a Yii 2 wrapper for bootstrap-tagsinput.
 * See more: https://github.com/timschlechter/bootstrap-tagsinput
 *
 * @author Wanderson Bragança <wanderson.wbc@gmail.com>
 */
class TagsinputWidget extends \yii\widgets\InputWidget
{
    /**
     * The name of the jQuery plugin to use for this widget.
     */
    const PLUGIN_NAME = 'tagsinput';
    /**
     * @var array the JQuery plugin options for the bootstrap-tagsinput plugin.
     * @see http://timschlechter.github.io/bootstrap-tagsinput/examples/#options
     */
    public $clientOptions = [];

    /**
     * @var array the JQuery plugin options for the typeahead.
     * @see http://twitter.github.com/typeahead.js/examples
     */
    public $typeaheadOptions = [];
    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var string the hashed variable to store the pluginOptions
     */
    protected $_hashVar;

    /**
     * @var array dataset an object that defines a set of data that hydrates suggestions.
     * For TypeaheadBasic, this is a single dimensional array consisting of following settings. 
     * For Typeahead, this is a multi-dimensional array, with each array item being an array that 
     * consists of the following settings.
     * - source: The backing data source for suggestions. Expected to be a function with the 
     *   signature `(query, syncResults, asyncResults)`. This can also be a Bloodhound instance.
     *   If not set, this will be automatically generated based on the bloodhound specific
     *   properties in the next section below.
     * - display: string the key used to access the value of the datum in the datum
     *   object. Defaults to 'value'.
     * - async: boolean, lets the dataset know if async suggestions should be expected. Defaults to `true`.     
     * - limit: integer the max number of suggestions from the dataset to display for
     *   a given query. Defaults to 5.
     * - templates: array the templates used to render suggestions.
     * The following properties are bloodhound specific data configuration properties and not applicable
     * for TypeaheadBasic. Its only applied for Typeahead.
     * - local: array configuration for the [[local]] list of datums. You must set one of
     *   [[local]], [[prefetch]], or [[remote]].
     * - prefetch: array configuration for the [[prefetch]] options object.
     * - remote: array configuration for the [[remote]] options object.
     * - initialize: true,
     * - identify: defaults to _.stringify,
     * - datumTokenizer: defaults to null,
     * - queryTokenizer: defaults null,
     * - sufficient: 5,
     * - sorter: null,
     */
    public $dataset = [];

    /**
     * @var string the generated Bloodhound script
     */
    protected $_bloodhound;


    /**
     * @var string the generated HashPluginOptions script
     */
    protected $_hashPluginOptions;

    /**
     * @var string the generated Json encoded Dataset script
     */
    protected $_dataset;

    /**
     * @var bool whether default suggestions are enabled
     */
    protected $_defaultSuggest = false;
    
    /**
     * @var array the bloodhound settings variables
     */
    protected static $_bhSettings = [
        'datumTokenizer',
        'queryTokenizer',
        'initalize',
        'sufficient',
        'sorter',
        'identify',
        'local',
        'prefetch',
        'remote'
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        
        if(isset($this->dataset)) {
            if (empty($this->dataset) || !is_array($this->dataset)) {
                throw new InvalidConfigException("You must define the 'dataset' property for Typeahead which must be an array.");
            }
            if (!is_array(current($this->dataset))) {
                throw new InvalidConfigException("The 'dataset' array must contain an array of datums. Invalid data found.");
            }
            $this->validateConfig();
            $this->initDataset();
        }

        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }
    }

    /**
     * @return void Validate if configuration is valid
     * @throws \yii\base\InvalidConfigException
     */
    protected function validateConfig()
    {
        foreach ($this->dataset as $datum) {
            if (empty($datum['local']) && empty($datum['prefetch']) && empty($datum['remote'])) {
                throw new InvalidConfigException("No data source found for the Typeahead. The 'dataset' array must have one of 'local', 'prefetch', or 'remote' settings enabled.");
            }
        }
    }

    /**
     * Generates a hashed variable to store the plugin `clientOptions`. Helps in reusing the variable for similar
     * options passed for other widgets on the same page. The following special data attribute will also be
     * setup for the input widget, that can be accessed through javascript:
     *
     * - 'data-plugin-tagsinput' will store the hashed variable storing the plugin options.
     *
     * @param View $view the view instance
     */
    protected function hashPluginOptions($view)
    {
        if(isset($this->typeaheadOptions)){
            $this->clientOptions['typeaheadjs'][] =  $this->typeaheadOptions;
        }

        if(isset($this->_dataset)){
            $this->clientOptions['typeaheadjs'][] =  $this->_dataset;
        }

        $encOptions = empty($this->clientOptions) ? '{}' : Json::encode($this->clientOptions);
        $this->_hashVar = self::PLUGIN_NAME . '_' . hash('crc32', $encOptions);
        $this->options['data-plugin-' . self::PLUGIN_NAME] = $this->_hashVar;
        $this->_hashPluginOptions = "var {$this->_hashVar} = {$encOptions};";
        // $view->registerJs("var {$this->_hashVar} = {$encOptions};\n", View::POS_END);
    }

    /**
     * Initialize the data set
     */
    protected function initDataset()
    {
        $index = 1;
        $this->_bloodhound = '';
        $this->_dataset = '';
        $dataset = [];
        foreach ($this->dataset as $datum) {
            $dataVar = strtr(strtolower($this->options['id'] . '_data_' . $index), ['-' => '_']);
            $this->_bloodhound .= $this->parseSource($dataVar, $datum) . "\n";
            $d = $datum;
            $d['name'] = $dataVar;
            if (empty($d['source'])) {
                if ($this->_defaultSuggest) {
                    $sug = Json::encode($this->defaultSuggestions);
                    $sugVar = 'kvTypData_' . hash('crc32', $sug);
                    $this->getView()->registerJs("var {$sugVar} = {$sug};", View::POS_HEAD);
                    $source = "function(q,s){if(q===''){s({$dataVar}.get({$sugVar}));}else{{$dataVar}.search(q,s);}}";
                } else {
                    $source = "{$dataVar}.ttAdapter()";
                }
                $d['source'] = new JsExpression($source);
            }
            $dataset[] = $d;
            $index++;
        }
        $this->_dataset = $dataset;
    }

    /**
     * Parses a variable and force converts it to JsExpression
     * @param mixed $expr
     * @return JsExpression
     */
    protected static function parseJsExpr($expr)
    {
        return ($expr instanceof JsExpression) ? $expr : new JsExpression($expr);
    }
    
    /**
     * Parses the data source array and prepares the bloodhound configuration
     *
     * @param string $dataVar the variable to store the Bloodhound instance
     * @param array $source the source data
     * @return string the prepared bloodhound configuration
     */
    protected function parseSource($dataVar, &$source)
    {
        $out = [];
        $defaultToken = new JsExpression("Bloodhound.tokenizers.whitespace");
        foreach (self::$_bhSettings as $key) {
            if ($key === 'datumTokenizer' || $key === 'queryTokenizer') {
                $out[$key] = self::parseJsExpr(ArrayHelper::remove($source, $key, $defaultToken));
            }
            if (isset($source[$key])) {
                $out[$key] = $source[$key];
                if ($key === 'local') {
                    $local = Json::encode($source[$key]);
                    $localVar = 'kvTypData_' . hash('crc32', $local);
                    $this->getView()->registerJs("var {$localVar} = {$local};", View::POS_HEAD);
                    $out[$key] = new JsExpression($localVar);
                } elseif ($key === 'prefetch') {
                    $prefetch = $source[$key];
                    if (!is_array($prefetch)) {
                        $prefetch = ['url' => $prefetch];
                    }
                    $out[$key] = $prefetch;
                }
                unset($source[$key]);
            }
        }
        return "var {$dataVar} = new Bloodhound(" . Json::encode($out) . ");";
    }

    /**
     * Registers the needed client script and options.
     */
    public function registerClientScript()
    {
        $js = '';
        $view = $this->getView();
        $this->hashPluginOptions($view);
        $id = $this->options['id'];
        $view->registerJs("{$this->_bloodhound}\n", View::POS_END);
        $view->registerJs("{$this->_hashPluginOptions}\n", View::POS_END);
        $js .= '$("#' . $id . '").' . self::PLUGIN_NAME . "(" . $this->_hashVar . ");\n";
        TagsinputAsset::register($view);
        $view->registerJs($js);
    }
}
