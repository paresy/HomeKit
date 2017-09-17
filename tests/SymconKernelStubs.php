<?php

declare(strict_types=1);

namespace IPS {

    trait ModuleLoader
    {
        private static $libraries = [];
        private static $modules = [];

        public static function libraryExists(string $LibraryID)
        {
            return isset(self::$libraries[$LibraryID]);
        }

        private static function checkLibrary(string $LibraryID): void
        {
            if (!self::libraryExists($LibraryID)) {
                throw new \Exception(sprintf('Library #%s does not exist', $LibraryID));
            }
        }

        public static function getLibrary(string $LibraryID)
        {
            self::checkLibrary($LibraryID);

            return self::$libraries[$LibraryID];
        }

        public static function getLibraryList()
        {
            return array_keys(self::$libraries);
        }

        public static function getLibraryModules(string $LibraryID)
        {
            $result = [];
            foreach(self::$modules as $module) {
                if($module['LibraryID'] == $LibraryID) {
                    $result[] = $module['ModuleID'];
                }
            }

            return $result;
        }

        public static function moduleExists(string $ModuleID)
        {
            return isset(self::$modules[$ModuleID]);
        }

        private static function checkModule(string $ModuleID): void
        {
            if (!self::libraryExists($ModuleID)) {
                throw new \Exception(sprintf('Module #%s does not exist', $ModuleID));
            }
        }

        public static function getModule(string $ModuleID)
        {
            self::checkModule($ModuleID);

            return self::$modules[$ModuleID];
        }

        public static function getModuleList()
        {
            return array_keys(self::$modules);
        }

        public static function getModuleListByType(int $ModuleType)
        {
            $result = [];
            foreach(self::$modules as $module) {
                if($module['ModuleType'] == $ModuleType) {
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
                    self::loadModule($module . '/module.json', $libraryID);
                }
            }
        }

        private static function loadModule(string $file, string $libraryID): void
        {
            $module = json_decode(file_get_contents($file), true);
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
                'Class'              => str_replace(" ", "", $module['name'])
            ];

        }
    }

    trait ObjectManager
    {
        private static $availableIDs = [];

        private static $objects = [
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

        public static function registerObject(int $ObjectType): int
        {
            //Initialize
            if (sizeof(self::$availableIDs) == 0 && sizeof(self::$objects) == 1) {
                for ($i = 10000; $i < 60000; $i++) {
                    self::$availableIDs[] = $i;
                }
                shuffle(self::$availableIDs);
            }

            //Check for availability
            if(sizeof(self::$availableIDs) == 0) {
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

    }

    trait CategoryManager
    {
        private static $categories = [];

        public static function createCategory(int $CategoryID): void
        {
            self::$categories[$CategoryID] = [];
        }

        public static function deleteCategory(int $CategoryID): bool
        {
            self::checkCategory($CategoryID);
            unset(self::$categories[$CategoryID]);

            return true;
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

    trait FlowHandler
    {
        private static $flows = [];

    }

    trait ProfileManager
    {
        private static $profiles = [];

    }

    trait DebugServer
    {
        private static $debug = [];

        public static function disableDebug(int $ID)
        {
            self::$debug[$ID] = 0;
        }

        public static function enableDebug(int $ID, int $Duration)
        {
            self::$debug[$ID] = time() + $Duration;
        }

        public static function sendDebug(int $SenderID, string $Message, string $Data, int $Format)
        {
            if(!isset(self::$debug[$SenderID])) {
                return;
            }

            if(time() > self::$debug[$SenderID]) {
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
        use FlowHandler;
        use ProfileManager;
        use DebugServer;
    }
}
