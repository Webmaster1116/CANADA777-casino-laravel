<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Plugin {

    private $_plugins = null;
    private $_activePlugins = null;

    /**
     *  Get plugin dir
     */

    public static function getPluginDir() {
        return FCPATH . WP_PLUGIN_DIR;
    }

    public function __construct() {
        $plugins = $this->getPlugins();
        foreach ($plugins as $key => $plugin) {
            list($path) = explode('/', $key);
            $path .= '/plugins_included_before.php';
            if (file_exists(FCPATH . WP_PLUGIN_DIR . $path)) {
                include FCPATH . WP_PLUGIN_DIR . $path;
            }
        }
    }

    /**
     *  Get plugin url
     *
     * @param string $plugin
     * @return string
     */
    public function pluginUrl($plugin) {
        $path = str_replace(FCPATH, '', $plugin);
        $pathInfo = pathinfo($path);
        $dirParts = explode(DIRECTORY_SEPARATOR, $pathInfo['dirname']);
        $pluginUrl = base_url() . implode('/', array_splice($dirParts, 0, 3));
        return $pluginUrl;
    }

    /**
     *  Get plugin dir
     *
     * @param string $plugin
     * @return string
     */
    public function pluginDir($plugin) {
        $pathInfo = pathinfo(str_replace(FCPATH, '', $plugin));
        $dirParts = explode(DIRECTORY_SEPARATOR, $pathInfo['dirname']);
        $pluginDir = FCPATH . implode(DIRECTORY_SEPARATOR, array_splice($dirParts, 0, 2)) . DIRECTORY_SEPARATOR;
        return $pluginDir;
    }

    /**
     *  Get plugin name
     *
     * @param string $plugin
     * @return string
     */
    public function pluginName($plugin) {
        $pathInfo = pathinfo(ltrim($plugin, FCPATH));
        $dirParts = explode(DIRECTORY_SEPARATOR, $pathInfo['dirname']);
        $pluginName = isset($dirParts[1]) ? $dirParts[1] : '';
        return $pluginName;
    }

    /**
     *  Get installed plugins list
     *
     *  @return array
     */

    public function getPlugins() {
        if (is_null($this->_plugins)) {
            $this->_plugins = $this->_scanPlugins();
        }
        return $this->_plugins;
    }

    /**
     *  Check if plugin is active
     *
     *  @param  string  $plugin
     *  @return boolean
     */
    public function isPluginActive($plugin) {
        return in_array($plugin, $this->getActivePlugins());
    }

    /**
     *  Get list of active plugins
     *
     *  @return array
     */
    public function getActivePlugins() {
        if (is_null($this->_activePlugins)) {
            $activePlugins = get_option('active_plugins');
            $this->_activePlugins = $activePlugins ? $activePlugins : array();
        }
        return $this->_activePlugins;
    }

    /**
     *  Activate plugin
     *
     *  @param  string  $plugin
     *  @return boolean
     */
    public function activatePlugin($plugin) {
        $activePlugins = $this->getActivePlugins();
        if ( ! in_array($plugin, $activePlugins)) {
            $activePlugins[] = $plugin;
            $this->_updateActivePlugins($activePlugins);
        }
        return true;
    }

    /**
     *  Deactivate plugin
     *
     *  @param  string  $plugin
     *  @return boolean
     */
    public function deactivatePlugin($plugin) {
        $activePlugins = $this->getActivePlugins();
        foreach ($activePlugins as $key => $_plugin) {
            if ($plugin == $_plugin) {
                unset($activePlugins[$key]);
            }
        }
        $this->_updateActivePlugins($activePlugins);
        return true;
    }

    /**
     *  Find installed plugins
     *
     *  @return array
     */
    private function _scanPlugins() {
        $path = FCPATH . WP_PLUGIN_DIR;
        $plugins = array();
        $pluginDirs = glob($path . '*' , GLOB_ONLYDIR);
        if ($pluginDirs)
        foreach ($pluginDirs as $dir) {
            $dirName = basename($dir);
            $fileName = $dirName . '.php';
            $filePath = $dir . '/' . $fileName;
            if (file_exists($filePath)) {
                $plugin = array();
                $fileContent = file_get_contents($filePath);
                $fileContent = strstr($fileContent, '*/', true);
                foreach (explode("\n", $fileContent) as $line) {
                    $parts = explode(': ', $line);
                    if (count($parts) == 2) {
                        switch (trim(strtolower(str_replace('*', '', $parts[0])))) {
                            case 'plugin name' : $key = 'Name'; break;
                            case 'plugin uri' : $key = 'PluginURI'; break;
                            case 'description' : $key = 'Description'; break;
                            case 'author' : $key = 'Author'; break;
                            case 'version' : $key = 'Version'; break;
                            case 'author uri' : $key = 'AuthorURI'; break;
                            default: $key = str_replace(' ', '', trim($parts[0])); break;
                        }
                        $plugin[$key] = trim($parts[1]);
                    }
                }
                if (isset($plugin['Name']) && isset($plugin['Version'])) {
                    $plugin['Network'] = false;
                    $plugin['Title'] = $plugin['Name'];
                    $plugin['AuthorName'] = $plugin['Author'];
                    $plugins[$dirName . '/' . $fileName] = $plugin;
                }
            }
        }
		return $plugins;
    }

    /**
     *  Update active plugins
     *
     *  @param  array   $plugins
     */
    private function _updateActivePlugins($plugins) {
        $this->_activePlugins = $plugins;
        update_option('active_plugins', $plugins);
    }


    /**
     *  Update plugin
     *
     *  @param  string  $plugin
     *  @return boolean
     */

    public function updatePlugin($plugin) {

        $rslb = new RevSliderLoadBalancer();
        $updateUrl = $rslb->get_url('updates');

        $url = "$updateUrl/revslider-js-addon/addons/{$plugin}/{$plugin}.zip";
        $file = self::getPluginDir() . $plugin . '.zip';

        $result = false;

        if ($response = wp_remote_post($url, array('timeout' => 45))) {
            wp_mkdir_p(dirname($file));
            if (@file_put_contents($file, $response['body'])) {
                if (unzip_file($file, self::getPluginDir())) {
                    $result = true;
                }
                @unlink($file);
            }
        }

        return $result;
    }

}