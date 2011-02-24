<?php

/**
 * Base class for content plugins
 *
 * You must add a constructor taking an array of the plugin's data. This array
 * corresponds to what is return by Form_View::getValues() when user
 * has submitted data after editing plugin. The constructor should initialize
 * object data based on the input.
 */
class Content_Type implements Zikula_Translatable
{
    /**
     * Translation domain.
     *
     * @var string|null
     */
    public $domain = null;
    /**
     * Module name
     * 
     * @var string|'Content'
     */
    public $modname = 'Content';
    /**
     * Instance of Zikula_View.
     *
     * @var Zikula_View
     */
    public $view;

    /**
     * Plugin name
     * 
     * @var string|null 
     */
    public $pluginname = null;
    /**
     * Constructor
     */
	public function __construct()
	{
        $parts = explode('_', get_class($this));
        $this->modname = $parts[0];
        $this->pluginname = array_pop($parts);
        $this->domain = ZLanguage::getModuleDomain($this->modname);
        if (!($this->view instanceof Zikula_View)) {
            $this->view = Zikula_View::getInstance($this->modname);
        }
    }

    /**
     * Translate.
     *
     * @param string $msgid String to be translated.
     *
     * @return string The $msgid translated by gettext.
     */
    public function __($msgid)
    {
        return __($msgid, $this->domain);
    }

    /**
     * Translate with sprintf().
     *
     * @param string       $msgid  String to be translated.
     * @param string|array $params Args for sprintf().
     *
     * @return string The $msgid translated by gettext.
     */
    public function __f($msgid, $params)
    {
        return __f($msgid, $params, $this->domain);
    }

    /**
     * Translate plural string.
     *
     * @param string $singular Singular instance.
     * @param string $plural   Plural instance.
     * @param string $count    Object count.
     *
     * @return string Translated string.
     */
    public function _n($singular, $plural, $count)
    {
        return _n($singular, $plural, $count, $this->domain);
    }

    /**
     * Translate plural string with sprintf().
     *
     * @param string       $sin    Singular instance.
     * @param string       $plu    Plural instance.
     * @param string       $n      Object count.
     * @param string|array $params Sprintf() arguments.
     *
     * @return string The $sin or $plu translated by gettext, based on $n.
     */
    public function _fn($sin, $plu, $n, $params)
    {
        return _fn($sin, $plu, $n, $params, $this->domain);
    }
	
    /**
     * Get module name
     * @return string
     */
    public function getModule()
    {
        return $this->modname;
    }

    /**
     * Get plugin name
     * @return string
     */
    public function getName()
    {
        return $this->pluginname;
    }

    /**
     * Get displayed title
     * @return string
     */
    public function getTitle()
    {
        return '- no title defined -';
    }

    /**
     * Get displayed description
     * @return string
     */
    public function getDescription()
    {
        return '';
    }
}
