<?php
namespace Coa;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Plugin extends \App\Plugin\Iface
{

    /**
     * A helper method to get the Plugin instance globally
     *
     * @return static|\Tk\Plugin\Iface
     * @throws \Exception
     */
    static function getInstance()
    {
        return \App\Config::getInstance()->getPluginFactory()->getPlugin('plg-coa');
    }

    /**
     * Init the plugin
     *
     * This is called when the session first registers the plugin to the queue
     * So it is the first called method after the constructor...
     */
    function doInit()
    {
        include dirname(__FILE__) . '/config.php';


        // Register the plugin for the different client areas if they are to be enabled/disabled/configured by those roles.
        //$this->getPluginFactory()->registerZonePlugin($this, self::ZONE_INSTITUTION);
        $this->getPluginFactory()->registerZonePlugin($this, self::ZONE_COURSE);
        //$this->getPluginFactory()->registerZonePlugin($this, self::ZONE_SUBJECT);
        \App\Config::getInstance()->getEventDispatcher()->addSubscriber(new \Coa\Listener\SetupHandler());
    }

    /**
     * Activate the plugin, essentially
     * installing any DB and settings required to run
     * Will only be called when activating the plugin in the
     * plugin control panel
     *
     * @throws \Exception
     */
    function doActivate()
    {
        // Init Plugin Settings
        $db = $this->getConfig()->getDb();

        $migrate = new \Tk\Util\SqlMigrate($db);
        $migrate->setTempPath($this->getConfig()->getTempPath());
        $migrate->migrate(dirname(__FILE__) . '/sql');

    }

    /**
     * Deactivate the plugin removing any DB data and settings
     * Will only be called when deactivating the plugin in the
     * plugin control panel
     */
    function doDeactivate()
    {
        // TODO: Implement doDeactivate() method.
        $db = $this->getConfig()->getDb();

        // Keep all data at this point

        // Clear the data table of all plugin data
//        $sql = sprintf('DELETE FROM %s WHERE %s LIKE %s', $db->quoteParameter(\Tk\Db\Data::$DB_TABLE), $db->quoteParameter('fkey'),
//            $db->quote($this->getName().'%'));
//        $db->query($sql);

        // Delete all tables.
//        $tables = array('skill', 'skill_bundle', 'skill_bundle_has_placement', 'skill_group', 'skill_score', 'skill_set');
//        foreach ($tables as $name) {
//            $db->dropTable($name);
//        }

        // Remove migration track
//        $sql = sprintf('DELETE FROM %s WHERE %s LIKE %s', $db->quoteParameter(\Tk\Util\SqlMigrate::$DB_TABLE), $db->quoteParameter('path'),
//            $db->quote('/plugin/' . $this->getName().'/%'));
//        $db->query($sql);
        
        // Delete any setting in the DB
//        $data = \Tk\Db\Data::create($this->getName());
//        $data->clear();
//        $data->save();
    }



}