<?php

declare(strict_types=1);

namespace IPS {

    trait ModuleLoader
    {
        private static $libraries = [];
        private static $modules = [];

        public static function libraryExists(string $LibraryID): bool
        {
            return isset(self::$libraries[$LibraryID]);
        }

        private static function checkLibrary(string $LibraryID): void
        {
            if (!self::libraryExists($LibraryID)) {
                throw new \Exception(sprintf('Library #%s does not exist', $LibraryID));
            }
        }

        public static function getLibrary(string $LibraryID): array
        {
            self::checkLibrary($LibraryID);

            return self::$libraries[$LibraryID];
        }

        public static function getLibraryList(): array
        {
            return array_keys(self::$libraries);
        }

        public static function getLibraryModules(string $LibraryID): array
        {
            $result = [];
            foreach (self::$modules as $module) {
                if ($module['LibraryID'] == $LibraryID) {
                    $result[] = $module['ModuleID'];
                }
            }

            return $result;
        }

        public static function moduleExists(string $ModuleID): bool
        {
            return isset(self::$modules[$ModuleID]);
        }

        private static function checkModule(string $ModuleID): void
        {
            if (!self::moduleExists($ModuleID)) {
                throw new \Exception(sprintf('Module #%s does not exist', $ModuleID));
            }
        }

        public static function getModule(string $ModuleID): array
        {
            self::checkModule($ModuleID);

            return self::$modules[$ModuleID];
        }

        public static function getModuleList(): array
        {
            return array_keys(self::$modules);
        }

        public static function getModuleListByType(int $ModuleType): array
        {
            $result = [];
            foreach (self::$modules as $module) {
                if ($module['ModuleType'] == $ModuleType) {
                    $result[] = $module['ModuleID'];
                }
            }

            return $result;
        }

        public static function loadLibrary(string $file): void
        {
            $library = json_decode(file_get_contents($file), true);
            self::$libraries[$library['id']] = [
                'LibraryID' => $library['id'],
                'Author'    => $library['author'],
                'URL'       => $library['url'],
                'Name'      => $library['name'],
                'Version'   => $library['version'],
                'Build'     => $library['build'],
                'Date'      => $library['date'],
            ];
            self::loadModules(dirname($file), $library['id']);
        }

        private static function loadModules(string $folder, string $libraryID): void
        {
            $modules = glob($folder . '/*', GLOB_ONLYDIR);
            $filter = ['libs', 'docs', 'imgs', 'tests'];
            foreach ($modules as $module) {
                if (!in_array(basename($module), $filter)) {
                    self::loadModule($module, $libraryID);
                }
            }
        }

        private static function loadModule(string $folder, string $libraryID): void
        {
            $module = json_decode(file_get_contents($folder . '/module.json'), true);
            self::$modules[$module['id']] = [
                'ModuleID'           => $module['id'],
                'ModuleName'         => $module['name'],
                'ModuleType'         => $module['type'],
                'Vendor'             => $module['vendor'],
                'Aliases'            => $module['aliases'],
                'ParentRequirements' => $module['parentRequirements'],
                'ChildRequirements'  => $module['childRequirements'],
                'Implemented'        => $module['implemented'],
                'LibraryID'          => $libraryID,
                'Prefix'             => $module['prefix'],
                'Class'              => str_replace(' ', '', $module['name'])
            ];

            //Include module class file
            require_once $folder . '/module.php';
        }
    }

    trait ObjectManager
    {
        private static $availableIDs = [];

        private static $objects = [];

        public static function registerObject(int $ObjectType): int
        {
            if (count(self::$objects) == 0) {
                throw new \Exception('Reset was not called on Kernel.');
            }

            //Initialize
            if (count(self::$availableIDs) == 0 && count(self::$objects) == 1) {
                for ($i = 10000; $i < 60000; $i++) {
                    self::$availableIDs[] = $i;
                }
                shuffle(self::$availableIDs);
            }

            //Check for availability
            if (count(self::$availableIDs) == 0) {
                throw new \Exception('No usable IDs left. Please contact support.');
            }

            //Fetch first. The array is already randomized
            $id = array_shift(self::$availableIDs);

            //Add object
            self::$objects[$id] = [
                'ObjectID'         => $id,
                'ObjectType'       => $ObjectType,
                'ObjectName'       => sprintf('Unnamed Object (ID: %d)', $id),
                'ObjectIcon'       => '',
                'ObjectInfo'       => '',
                'ObjectIdent'      => '',
                'ObjectSummary'    => '',
                'ObjectIsHidden'   => false,
                'ObjectIsDisabled' => false,
                'ObjectIsLocked'   => false,
                'ObjectIsReadOnly' => false,
                'ObjectPosition'   => 0,
                'ParentID'         => 0,
                'ChildrenIDs'      => [],
                'HasChildren'      => false
            ];

            //Add to root
            self::$objects[0]['ChildrendIDs'][] = $id;
            self::$objects[0]['HasChildren'] = true;

            return $id;
        }

        public static function unregisterObject(int $ID): void
        {
            self::checkObject($ID);

            if (self::hasChildren($ID)) {
                throw new \Exception('Cannot call UnregisterObject if a children is present');
            }

            //Delete ID from Children array
            $ParentID = self::$objects[$ID]['ParentID'];
            if (($key = array_search($ID, self::$objects[$ParentID]['ChildrenIDs'])) !== false) {
                unset(self::$objects[$ParentID]['ChildrenIDs'][$key]);
            }

            //Readd ID to available pool
            self::$availableIDs[] = $ID;
        }

        public static function setParent(int $ID, int $ParentID): void
        {
            self::checkRoot($ID);
            self::checkObject($ID);

            self::$objects[$ID]['ParentID'] = $ParentID;

            //FIXME: Update ChildrenIDs
        }

        public static function setIdent(int $ID, string $Ident): void
        {
            self::checkObject($ID);

            if (!preg_match('/[a-zA-Z0-9_]*/', $Ident)) {
                throw new \Exception('Ident may contain only letters and numbers');
            }

            if ($Ident != '') {
                $ParentID = self::$objects[$ID]['ParentID'];
                foreach (self::$objects[$ParentID]['ChildrenIDs'] as $ChildID) {
                    if (self::$objects[$ChildID]['ObjectIdent'] == $Ident) {
                        if ($ChildID != $ID) {
                            throw new \Exception('Ident must be unique for each category');
                        }
                    }
                }
            }

            self::$objects[$ID]['ObjectIdent'] = $Ident;
        }

        public static function setName(int $ID, string $Name): void
        {
            self::checkObject($ID);

            if ($Name == '') {
                $Name = sprintf('Unnamed Object (ID: %d)', $ID);
            }

            self::$objects[$ID]['ObjectName'] = $Name;
        }

        public static function setInfo(int $ID, string $Info): void
        {
            self::checkObject($ID);

            self::$objects[$ID]['ObjectInfo'] = $Info;
        }

        public static function setIcon(int $ID, string $Icon): void
        {
            self::checkObject($ID);

            self::$objects[$ID]['ObjectIcon'] = $Icon;
        }

        public static function setSummary(int $ID, bool $Summary): void
        {
            self::checkRoot($ID);

            self::$objects[$ID]['ObjectSummary'] = $Summary;
        }

        public static function setPosition(int $ID, int $Position): void
        {
            self::checkRoot($ID);
            self::checkObject($ID);

            self::$objects[$ID]['ObjectPosition'] = $Position;
        }

        public static function setReadOnly(int $ID, bool $ReadOnly): void
        {
            self::checkRoot($ID);
            self::checkObject($ID);

            self::$objects[$ID]['ObjectIsReadOnly'] = $ReadOnly;
        }

        public static function setHidden(int $ID, bool $Hidden): void
        {
            self::checkRoot($ID);
            self::checkObject($ID);

            self::$objects[$ID]['ObjectIsHidden'] = $Hidden;
        }

        public static function setDisabled(int $ID, bool $Disabled): void
        {
            self::checkRoot($ID);
            self::checkObject($ID);

            self::$objects[$ID]['ObjectIsDisabled'] = $Disabled;
        }

        public static function objectExists(int $ID): bool
        {
            return isset(self::$objects[$ID]);
        }

        private static function checkRoot(int $ID): void
        {
            if ($ID == 0) {
                throw new \Exception('Cannot change root');
            }
        }

        private static function checkObject(int $ID): void
        {
            if (!self::objectExists($ID)) {
                throw new \Exception(sprintf('Object #%d does not exist', $ID));
            }
        }

        public static function getObject(int $ID): array
        {
            self::checkObject($ID);

            return self::$objects[$ID];
        }

        public static function getObjectList(): array
        {
            return array_keys(self::$objects);
        }

        public static function getObjectIDByName(string $Name, int $ParentID): array
        {
            if ($Name == '') {
                throw new \Exception('Name cannot be empty');
            }

            self::checkObject($ParentID);
            foreach (self::$objects[$ParentID]['ChildrenIDs'] as $ChildID) {
                self::checkObject($ChildID);
                if (self::$objects[$ChildID]['ObjectName'] == $Name) {
                    return $ChildID;
                }
            }

            throw new \Exception(sprintf('Object with name %s could not be found', $Name));
        }

        public static function getObjectIDByNameEx(string $Name, int $ParentID, int $ObjectType): int
        {
            if ($Name == '') {
                throw new \Exception('Name cannot be empty');
            }

            self::checkObject($ParentID);
            foreach (self::$objects[$ParentID]['ChildrenIDs'] as $ChildID) {
                self::checkObject($ChildID);
                if (self::$objects[$ChildID]['ObjectType'] == $ObjectType) {
                    if (self::$objects[$ChildID]['ObjectName'] == $Name) {
                        return $ChildID;
                    }
                }
            }

            throw new \Exception(sprintf('Object with name %s could not be found', $Name));
        }

        public static function getObjectIDByIdent(string $Ident, int $ParentID): int
        {
            if ($Ident == '') {
                throw new \Exception('Ident cannot be empty');
            }

            self::checkObject($ParentID);
            foreach (self::$objects[$ParentID]['ChildrenIDs'] as $ChildID) {
                self::checkObject($ChildID);
                if (self::$objects[$ChildID]['ObjectIdent'] == $Ident) {
                    return $ChildID;
                }
            }

            throw new \Exception(sprintf('Object with ident %s could not be found', $Ident));
        }

        public static function hasChildren(int $ID): bool
        {
            return count(self::getChildrenIDs($ID)) > 0;
        }

        public static function isChild(int $ID, int $ParentID, bool $Recursive): bool
        {
            throw new \Exception('FIXME: Not implemented');
        }

        public static function getChildrenIDs(int $ID): array
        {
            self::checkObject($ID);

            return self::$objects[$ID]['ChildrenIDs'];
        }

        public static function getName(int $ID): string
        {
            return self::$objects[$ID]['ObjectName'];
        }

        public static function getParent(int $ID): int
        {
            return self::$objects[$ID]['ParentID'];
        }

        public static function getLocation(int $ID): string
        {
            $result = self::getName($ID);
            $parentID = self::getParent($ID);

            while ($parentID > 0) {
                $result = self::getName($parentID) . '\\' . $result;
                $parentID = self::getParent($parentID);
            }

            return $result;
        }
    }

    trait CategoryManager
    {
        private static $categories = [];

        public static function createCategory(int $CategoryID): void
        {
            self::$categories[$CategoryID] = [];
        }

        public static function deleteCategory(int $CategoryID): void
        {
            self::checkCategory($CategoryID);
            unset(self::$categories[$CategoryID]);
        }

        public static function categoryExists(int $CategoryID): bool
        {
            return isset(self::$categories[$CategoryID]);
        }

        private static function checkCategory(int $CategoryID): void
        {
            if (!self::categoryExists($CategoryID)) {
                throw new \Exception(sprintf('Category #%d does not exist', $CategoryID));
            }
        }

        public static function getCategory(int $CategoryID): array
        {
            self::checkCategory($CategoryID);

            return [];
        }

        public static function getCategoryList(): array
        {
            return array_keys(self::$categories);
        }

        public static function getCategoryIDByName(string $Name, int $ParentID): int
        {
            $id = 0;
            if ($id == 0) {
                throw new \Exception(sprintf('Category with name %s could not be found', $Name));
            }

            return $id;
        }
    }

    trait InstanceManager
    {
        private static $instances = [];
        private static $interfaces = [];

        public static function createInstance(int $InstanceID, array $Module): void
        {
            if (!class_exists($Module['Class'])) {
                throw new \Exception(sprintf('Cannot find class %s', $Module['Class']));
            }

            if (!in_array('IPSModule', class_parents($Module['Class']))) {
                throw new \Exception(sprintf('Class %s does not inherit from IPSModule', $Module['Class']));
            }

            self::$instances[$InstanceID] = [
                'InstanceID'      => $InstanceID,
                'ConnectionID'    => 0,
                'InstanceStatus'  => 100 /* IS_CREATING */,
                'InstanceChanged' => time(),
                'ModuleInfo'      => [
                    'ModuleID'   => $Module['ModuleID'],
                    'ModuleName' => $Module['ModuleName'],
                    'ModuleType' => $Module['ModuleType']
                ],
            ];

            $interface = new $Module['Class']($InstanceID);

            self::$interfaces[$InstanceID] = $interface;

            if ($interface instanceof \IPSModule) {
                $interface->Create();
                $interface->ApplyChanges();
            }
        }

        public static function deleteInstance(int $InstanceID): void
        {
            self::checkInstance($InstanceID);
            unset(self::$instances[$InstanceID]);
            unset(self::$interfaces[$InstanceID]);
        }

        public static function instanceExists(int $InstanceID): bool
        {
            return isset(self::$instances[$InstanceID]);
        }

        private static function checkInstance(int $InstanceID): void
        {
            if (!self::instanceExists($InstanceID)) {
                throw new \Exception(sprintf('Instance #%d does not exist', $InstanceID));
            }
        }

        public static function getInstance(int $InstanceID): array
        {
            self::checkInstance($InstanceID);

            return self::$instances[$InstanceID];
        }

        public static function getInstanceInterface(int $InstanceID): \IPSModule
        {
            self::checkInstance($InstanceID);

            return self::$interfaces[$InstanceID];
        }

        public static function getInstanceList(): array
        {
            return array_keys(self::$instances);
        }

        public static function getInstanceListByModuleType(int $ModuleType): array
        {
            $result = [];
            foreach (self::$instances as $instance) {
                if ($instance['ModuleInfo']['ModuleType'] == $ModuleType) {
                    $result[] = $instance['InstanceID'];
                }
            }

            return $result;
        }

        public static function getInstanceListByModuleID(string $ModuleID): array
        {
            $result = [];
            foreach (self::$instances as $instance) {
                if ($instance['ModuleInfo']['ModuleID'] == $ModuleID) {
                    $result[] = $instance['InstanceID'];
                }
            }

            return $result;
        }

        public static function setStatus($InstanceID, $Status): void
        {
            self::checkInstance($InstanceID);

            self::$instances[$InstanceID]['InstanceStatus'] = $Status;
        }

        public static function connectInstance(int $InstanceID, int $ParentID): void
        {
            self::checkInstance($InstanceID);
            self::$instances[$InstanceID]['ConnectionID'] = $ParentID;
        }

        public static function disconnectInstance(int $InstanceID): void
        {
            self::checkInstance($InstanceID);
            self::$instances[$InstanceID]['ConnectionID'] = 0;
        }
    }

    trait VariableManager
    {
        private static $variables = [];
    }

    trait ScriptManager
    {
        private static $scripts = [];
    }

    trait EventManager
    {
        private static $events = [];
    }

    trait MediaManager
    {
        private static $medias = [];
    }

    trait LinkManager
    {
        private static $links = [];
    }

    trait ProfileManager
    {
        private static $profiles = [];
    }

    trait DebugServer
    {
        private static $debug = [];

        public static function disableDebug(int $ID): void
        {
            self::$debug[$ID] = 0;
        }

        public static function enableDebug(int $ID, int $Duration): void
        {
            self::$debug[$ID] = time() + $Duration;
        }

        public static function sendDebug(int $SenderID, string $Message, string $Data, int $Format): void
        {
            if (!isset(self::$debug[$SenderID])) {
                return;
            }

            if (time() > self::$debug[$SenderID]) {
                return;
            }

            if ($Format == 1 /* Binary */) {
                $Data = bin2hex($Data);
            }

            echo 'DEBUG: ' . $Message . ' | ' . $Data;
        }
    }

    class Kernel
    {
        use ModuleLoader;
        use ObjectManager;
        use CategoryManager;
        use InstanceManager;
        use VariableManager;
        use ScriptManager;
        use EventManager;
        use MediaManager;
        use LinkManager;
        use ProfileManager;
        use DebugServer;

        public static function reset()
        {
            self::$libraries = [];
            self::$modules = [];
            self::$availableIDs = [];
            self::$objects = [
                0 => [
                    'ObjectID'         => 0,
                    'ObjectType'       => 0 /* Category */,
                    'ObjectName'       => 'IP-Symcon',
                    'ObjectIcon'       => '',
                    'ObjectInfo'       => '',
                    'ObjectIdent'      => '',
                    'ObjectSummary'    => '',
                    'ObjectIsHidden'   => false,
                    'ObjectIsDisabled' => false,
                    'ObjectIsLocked'   => false,
                    'ObjectIsReadOnly' => false,
                    'ObjectPosition'   => 0,
                    'ParentID'         => 0,
                    'ChildrenIDs'      => [],
                    'HasChildren'      => false
                ]
            ];
            self::$categories = [];
            self::$instances = [];
            self::$interfaces = [];
            self::$variables = [];
            self::$scripts = [];
            self::$events = [];
            self::$medias = [];
            self::$links = [];
            self::$profiles = [];
            self::$debug = [];
        }
    }
}
