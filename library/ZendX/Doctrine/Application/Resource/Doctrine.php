<?php
class ZendX_Doctrine_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Doctrine_Manager
     */
    protected $_manager = null;

    /**
     * @var array
     */
    protected $_paths = array();

    /**
     * @var array
     */
    protected $_profilers = array();

    /**
     * Initialize Doctrine
     * 
     * @return bool
     */
    public function init()
    {
        Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
        $manager = $this->getManager();

        if ($options = $this->getOptions()) {
            if (isset($options['paths'])) {
                $this->getPaths($options['paths']);
            }

            if (isset($options['attributes'])) {
                $this->getAttributes($options['attributes']);
            }

            if (isset($options['connections'])) {
                $this->getConnections($options['connections']);
            }

            if (isset($options['debug'])) {
                $this->getDebug($options['debug']);
            }
        }

        $registryData = array(
            'paths'     => $this->_paths,
            'profilers' => $this->_profilers,
        );
        Zend_Registry::set('doctrine', $registryData);

        return $manager;
    }

    /**
     * Retrieve manager instance
     * 
     * @return Doctrine_Manager
     */
    public function getManager()
    {
        if (null === $this->_manager) {
            try {
                $this->_manager = Doctrine_Manager::getInstance();
            } catch (Doctrine_Exception $e) {
                /** @see Zend_Application_Resource_ResourceAbstract */
                require_once 'ZendX/Doctrine/Application/Resource/Exception.php';

                throw new 
                    ZendX_Doctrine_Application_Resource_Exception('Unable to 
                    retrieve Doctrine_Manager instance.');
            }
        }

        return $this->_manager;
    }

    /**
     * Set global attributes
     * 
     * @param array $attributes
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     */
    public function getAttributes(array $attributes = array())
    {
        foreach ($attributes as $key => $value) {
            $this->_manager->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Lazy load connections
     * 
     * @param array $connections
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     *
     * @todo Handle event listeners
     */
    public function getConnections(array $connections = array())
    {
        foreach ($connections as $name => $params) {
            if (!isset($params['dsn'])) {
                /** @see Zend_Application_Resource_ResourceAbstract */
                require_once 'ZendX/Doctrine/Application/Resource/Exception.php';

                throw new 
                    ZendX_Doctrine_Application_Resource_Exception('Doctrine 
                    resource dsn not present.');
            }

            $dsn = null;
            if (is_string($params['dsn'])) {
                $dsn = $params['dsn'];
            } elseif (is_array($params['dsn'])) {
                $dsn = $this->_buildConnectionString($params['dsn']);
            } else {
                /** @see Zend_Application_Resource_ResourceAbstract */
                require_once 'ZendX/Doctrine/Application/Resource/Exception.php';

                throw new 
                    ZendX_Doctrine_Application_Resource_Exception("Invalid 
                    Doctrine resource dsn format.");
            }

            try {
                $conn = Doctrine_Manager::connection($dsn, $name);

                if (isset($params['attributes'])) {
                    foreach ($params['attributes'] as $key => $value) {
                        $conn->setAttribute($key, $value);
                    }
                }

                if (isset($params['listeners']['connection'])) {
                    foreach ($params['listeners']['connection'] as $alias => $class) {
                        $listener = new $class();
                        $conn->addListener($listener, $alias);

                        if ($listener instanceof Doctrine_Connection_Profiler) {
                            $this->_profilers[] = $listener;
                        }
                    }
                }

                if (isset($params['listeners']['record'])) {
                    foreach ($params['listeners']['record'] as $alias => $class) {
                        $listener = new $class();
                        $conn->addRecordListener($listener, $alias);
                    }
                }
            } catch(Doctrine_Exception $e) {
                /** @see Zend_Application_Resource_ResourceAbstract */
                require_once 'ZendX/Doctrine/Application/Resource/Exception.php';

                throw new ZendX_Doctrine_Application_Resource_Exception("Unable 
                    to establish connection named $name to $dsn.");
            }
        }
        
        return $this;
    }

    /**
     * Set the debug status
     * 
     * @param  bool $debug
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     */
    public function getDebug($debug = 0)
    {
        Doctrine::debug($debug);
        return $this;
    }

    /**
     * Set the paths array
     * 
     * @param  array $paths
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     */
    public function getPaths(array $paths = array())
    {
        $this->_paths = $paths;
        return $this;
    }

    /**
     * Build connection string
     * 
     * @param  array $dsnData
     * @return string
     */
    private function _buildConnectionString(array $dsnData = array())
    {
        $connectionOptions = null;
        if ((isset($dsnData['options'])) || (!empty($dsnData['options']))) {
            $connectionOptions = 
                $this->_buildConnectionOptionsString($dsnData['options']);
        }

        return sprintf('%s://%s:%s@%s/%s?%s',
            $dsnData['adapter'],
            $dsnData['user'],
            $dsnData['pass'],
            $dsnData['hostspec'],
            $dsnData['database'],
            $connectionOptions);
    }

    /**
     * Build connection options string
     * 
     * @param  array $optionsData
     * @return string
     */
    private function _buildConnectionOptionsString(array $optionsData = array())
    {
        $i = 0;
        $count = count($optionsData);
        $options  = null;

        foreach ($optionsData as $key => $value) {
            if ($i == $count) {
                $options .= "$key=$value";
            } else {
                $options .= "$key=$value&";
            }

            $i++;
        }

        return $options;
    }
}