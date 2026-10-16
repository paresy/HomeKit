<?php

declare(strict_types=1);

class HomeKitManager
{
    public const classPrefix = 'HAPAccessory';
    public const configurationClassPrefix = 'HAPAccessoryConfiguration';
    public const propertyPrefix = 'Accessory';

    //A bridge must not expose more than 150 HAP accessory objects, the bridge itself included (HAP R17, 2.5.3.2)
    public const maxAccessories = 149;

    private static $supportedAccessories = [];

    private $registerProperty = null;
    private $registerReference = null;
    private $onAccessoriesExported = null;
    private $instanceID = 0;
    private $accessoryCache = null;

    public function __construct(int $instanceID, callable $registerProperty, callable $registerReference, ?callable $onAccessoriesExported = null)
    {
        $this->registerProperty = $registerProperty;
        $this->registerReference = $registerReference;
        $this->onAccessoriesExported = $onAccessoriesExported;
        $this->instanceID = $instanceID;
    }

    //Hashes the structure of the attribute database, which has to bump the configuration number (c#) on changes.
    //Values are excluded, as they change all the time. Only the values of the accessory information service
    //are included, as they hold the names and the firmware revision
    public static function getConfigurationHash(array $export): string
    {
        foreach ($export as &$accessory) {
            foreach ($accessory['services'] as &$service) {
                if ($service['type'] == '3E' /* AccessoryInformation */) {
                    continue;
                }
                foreach ($service['characteristics'] as &$characteristic) {
                    unset($characteristic['value']);
                }
            }
        }

        return md5(json_encode($export));
    }

    public static function registerAccessory(string $accessory): void
    {

        //Check if the same service was already registered
        if (in_array($accessory, self::$supportedAccessories)) {
            throw new Exception('Cannot register accessory! ' . $accessory . ' is already registered.');
        }
        //Add to our static array
        self::$supportedAccessories[] = $accessory;
    }

    public function registerProperties(): void
    {

        //All accessories live inside one unified list property
        ($this->registerProperty)('Accessories', '[]');
    }

    //Transforms the persistence of previous module versions:
    // - merges the legacy per-type Accessory<Type> properties into the unified 'Accessories' property
    // - rewrites the stored IDs, so that aid = ID + 1 and the increment generator of the list can start at 1
    // - runs the per-type doMigrate over all rows
    // - moves the ConfigurationNumber property into its attribute
    //Returns the new persistence or null if nothing changed
    public static function migratePersistence(array $persistence): ?array
    {
        $changed = false;

        $configuration = $persistence['configuration'] ?? [];
        $attributes = $persistence['attributes'] ?? [];

        $accessories = json_decode($configuration['Accessories'] ?? '[]', true);
        if (!is_array($accessories)) {
            $accessories = [];
        }

        //Merge the legacy per-type properties into the unified list
        foreach (self::$supportedAccessories as $accessory) {
            $legacyProperty = self::propertyPrefix . $accessory;
            if (!isset($configuration[$legacyProperty])) {
                continue;
            }

            $datas = json_decode($configuration[$legacyProperty], true);
            if (is_array($datas)) {
                foreach ($datas as $data) {
                    $data['Type'] = $accessory;
                    $data['ID'] = intval($data['ID'] ?? 0) - 1;
                    $accessories[] = $data;
                }
            }

            unset($configuration[$legacyProperty]);
            $changed = true;
        }

        //Assign IDs to rows which never received one (legacy rows with ID zero turned into -1 above)
        $highestID = 0;
        foreach ($accessories as $data) {
            if (intval($data['ID'] ?? 0) > $highestID) {
                $highestID = intval($data['ID']);
            }
        }
        foreach ($accessories as &$data) {
            if (intval($data['ID'] ?? 0) <= 0) {
                $data['ID'] = ++$highestID;
                $changed = true;
            }
        }
        unset($data);

        //Run the per-type migrations over all rows
        foreach ($accessories as &$data) {
            $configurationClass = self::configurationClassPrefix . ($data['Type'] ?? '');
            if (method_exists($configurationClass, 'doMigrate')) {
                if (call_user_func_array($configurationClass . '::doMigrate', [&$data])) {
                    $changed = true;
                }
            }
        }
        unset($data);

        if ($changed) {
            $configuration['Accessories'] = json_encode($accessories);
        }

        //The configuration number lives inside an attribute now
        if (isset($configuration['ConfigurationNumber'])) {
            $attributes['ConfigurationNumber'] = intval($configuration['ConfigurationNumber']);
            unset($configuration['ConfigurationNumber']);
            $changed = true;
        }

        //The hash is recomputed into its attribute on the next ApplyChanges
        if (isset($configuration['ConfigurationHash'])) {
            unset($configuration['ConfigurationHash']);
            $changed = true;
        }

        if (!$changed) {
            return null;
        }

        return [
            'attributes'    => $attributes,
            'configuration' => $configuration
        ];
    }

    public function getAccessories(): array
    {
        $accessories = [(new HAPAccessoryBridge([
            'Name' => IPS_GetProperty($this->instanceID, 'BridgeName')
        ]))->doExport(1)];

        foreach ($this->getExportedAccessoryObjects() as $aid => $object) {
            $accessories[] = $object->doExport($aid);
        }

        return $accessories;
    }

    //Used when serving the attribute database to a controller. Allows to detect structure changes,
    //which happened without ApplyChanges, e.g. an accessory disappearing because its variable was deleted
    public function exportAccessories(): array
    {
        $accessories = $this->getAccessories();

        if ($this->onAccessoriesExported !== null) {
            ($this->onAccessoriesExported)($accessories);
        }

        return $accessories;
    }

    //With $updatesOnly only those variables, whose characteristics notify on every update instead of only on changes
    public function getNotifyVariableIDs(bool $updatesOnly = false): array
    {
        $ids = [];
        foreach ($this->getExportedAccessoryObjects() as $object) {
            $ids = array_merge($ids, $object->getNotifyVariableIDs($updatesOnly));
        }

        return array_values(array_unique(array_filter($ids, function ($id)
        {
            return IPS_VariableExists($id);
        })));
    }

    public function updateAccessories(): void
    {
        $ids = [];

        foreach ($this->getAccessoriesData() as $data) {

            //The list generates unique IDs. This is just a safety net
            if (in_array($data['ID'], $ids)) {
                throw new Exception('AccessoryID has to be unique for all accessories');
            }
            $ids[] = $data['ID'];

            //Register all object references
            $configurationClass = self::configurationClassPrefix . ($data['Type'] ?? '');
            if (method_exists($configurationClass, 'getObjectIDs')) {
                foreach (call_user_func_array($configurationClass . '::getObjectIDs', [$data]) as $id) {
                    if ($id > 0) {
                        ($this->registerReference)($id);
                    }
                }
            }
        }
    }

    public function getConfigurationForm(): array
    {
        $translations = [
            'de' => [
                'Accessories'      => 'Geräte',
                'Type'             => 'Typ',
                'Unsupported type' => 'Unbekannter Typ',
                'Limit exceeded'   => 'Limit überschritten',
                'HomeKit supports at most 149 accessories per bridge. Accessories with the status "Limit exceeded" are not available in HomeKit.' => 'HomeKit unterstützt maximal 149 Geräte pro Bridge. Geräte mit dem Status "Limit überschritten" sind in HomeKit nicht verfügbar.'
            ]
        ];

        $sortedAccessories = self::$supportedAccessories;
        usort($sortedAccessories, function ($a, $b)
        {
            $posA = call_user_func(self::configurationClassPrefix . $a . '::getPosition');
            $posB = call_user_func(self::configurationClassPrefix . $b . '::getPosition');

            if ($posA != $posB) {
                return ($posA < $posB) ? -1 : 1;
            }

            $posA = call_user_func(self::configurationClassPrefix . $a . '::getCaption');
            $posB = call_user_func(self::configurationClassPrefix . $b . '::getCaption');

            //This is not very nice, but our largest user-base is german
            $dePosA = call_user_func(self::configurationClassPrefix . $a . '::getTranslations')['de'][$posA];
            $dePosB = call_user_func(self::configurationClassPrefix . $b . '::getTranslations')['de'][$posB];

            return strcmp($dePosA, $dePosB);
        });

        $typeColumnOptions = [];
        $typeFormOptions = [];
        $unionColumns = [];

        foreach ($sortedAccessories as $accessory) {
            $caption = call_user_func(self::configurationClassPrefix . $accessory . '::getCaption');

            //The fields of this type, which are only shown inside the edit dialog while this type is selected
            $items = [];
            foreach (call_user_func(self::configurationClassPrefix . $accessory . '::getColumns') as $column) {
                $fieldCaption = $column['caption'] ?? $column['name'];

                //Every dialog field requires a backing column with a default value,
                //so adding a new row properly resets the field inside the reused dialog
                if (!isset($unionColumns[$column['name']])) {
                    $unionColumns[$column['name']] = [
                        'caption' => $fieldCaption,
                        'name'    => $column['name'],
                        'width'   => '100px',
                        'add'     => $column['add'],
                        'save'    => true,
                        'visible' => false
                    ];
                }

                $items[] = array_merge($column['edit'] ?? ['type' => 'ValidationTextBox'], [
                    'name'    => $column['name'],
                    'caption' => $fieldCaption
                ]);
            }

            $typeColumnOptions[] = [
                'caption' => $caption,
                'value'   => $accessory
            ];

            $typeFormOptions[] = [
                'caption' => $caption,
                'value'   => $accessory,
                'items'   => $items
            ];

            $translations = $this->mergeTranslations($translations, call_user_func(self::configurationClassPrefix . $accessory . '::getTranslations'));
        }

        $columns = [
            [
                'caption' => 'ID',
                'name'    => 'ID',
                'width'   => '35px',
                'add'     => [
                    'strategy' => 'increment',
                    'step'     => 1
                ],
                'save' => true
            ],
            [
                'caption'     => 'Name',
                'name'        => 'Name',
                'width'       => 'auto',
                'add'         => '',
                'quickFilter' => true,
                'edit'        => [
                    'type' => 'ValidationTextBox'
                ]
            ],
            [
                'caption' => 'Type',
                'name'    => 'Type',
                'width'   => '200px',
                'add'     => $sortedAccessories[0] ?? '',
                'save'    => true,

                //The edit parameter is never used (the dialog is defined via form),
                //but it makes the list render the translated caption of the type
                'edit' => [
                    'type'    => 'Select',
                    'options' => $typeColumnOptions
                ]
            ],
            [
                'caption' => 'Status',
                'name'    => 'Status',
                'width'   => '150px',
                'add'     => '-'
            ]
        ];
        $columns = array_merge($columns, array_values($unionColumns));

        $exportedIDs = array_keys($this->getExportedAccessoryObjects());
        $limitExceeded = false;

        $values = [];
        foreach ($this->getAccessoriesData() as $data) {
            if (in_array($data['Type'] ?? '', self::$supportedAccessories)) {
                $status = call_user_func(self::configurationClassPrefix . $data['Type'] . '::getStatus', $data);

                //Valid accessories above the limit are not exposed
                if (($status == 'OK') && !in_array(intval($data['ID']) + 1, $exportedIDs)) {
                    $status = 'Limit exceeded';
                    $limitExceeded = true;
                }

                $values[] = [
                    'Status' => $status
                ];
            } else {
                $values[] = [
                    'Status' => 'Unsupported type'
                ];
            }
        }

        $elements = [];

        if ($limitExceeded) {
            $elements[] = [
                'type'    => 'Label',
                'bold'    => true,
                'caption' => 'HomeKit supports at most 149 accessories per bridge. Accessories with the status "Limit exceeded" are not available in HomeKit.'
            ];
        }

        $elements[] = [
            'type'    => 'List',
            'name'    => 'Accessories',
            'caption' => 'Accessories',
            'add'     => true,
            'delete'  => true,
            'sort'    => [
                'column'    => 'Name',
                'direction' => 'ascending'
            ],
            'columns' => $columns,
            'values'  => $values,

            //One dialog for all types: the fields of each type are defined as items
            //of its Select option and are only shown while that type is selected
            'form' => [
                [
                    'type'    => 'ValidationTextBox',
                    'name'    => 'Name',
                    'caption' => 'Name'
                ],
                [
                    'type'    => 'Select',
                    'name'    => 'Type',
                    'caption' => 'Type',
                    'options' => $typeFormOptions
                ]
            ]
        ];

        return [
            'elements'     => $elements,
            'translations' => $translations
        ];
    }

    //While the cache is active, every request for the same accessory returns the same object.
    //This allows accessories to keep state across multiple writes within one batch (e.g. Hue/Saturation/Brightness)
    public function beginAccessoryCache(): void
    {
        $this->accessoryCache = [];
    }

    public function endAccessoryCache(): void
    {
        foreach ($this->accessoryCache as $accessory) {
            $accessory->flushWriteCharacteristics();
        }
        $this->accessoryCache = null;
    }

    public function validateCharacteristics(int $aid, int $iid, $value)
    {
        return $this->getAccessoryObject($aid)->validateCharacteristic($iid, $value);
    }

    public function supportsWriteCharacteristics(int $aid, int $iid): bool
    {
        return $this->getAccessoryObject($aid)->supportsWriteCharacteristic($iid);
    }

    public function writeCharacteristics(int $aid, int $iid, $value): void
    {
        $accessory = $this->getAccessoryObject($aid);
        $accessory->writeCharacteristic($iid, $value);

        //Without an active batch there will be no endAccessoryCache, so flush immediately
        if ($this->accessoryCache === null) {
            $accessory->flushWriteCharacteristics();
        }
    }

    public function supportsReadCharacteristics(int $aid, int $iid): bool
    {
        return $this->getAccessoryObject($aid)->supportsReadCharacteristic($iid);
    }

    public function readCharacteristics(int $aid, int $iid)
    {
        return $this->getAccessoryObject($aid)->readCharacteristic($iid);
    }

    public function supportsNotifyCharacteristics(int $aid, int $iid): bool
    {
        return $this->getAccessoryObject($aid)->supportsNotifyCharacteristic($iid);
    }

    public function notifiesOnUpdate(int $aid, int $iid): bool
    {
        return $this->getAccessoryObject($aid)->notifiesOnUpdate($iid);
    }

    public function notifyCharacteristics(int $aid, int $iid)
    {
        return $this->getAccessoryObject($aid)->notifyCharacteristic($iid);
    }

    protected function mergeTranslations($arr1, $arr2): array
    {
        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1)) {
                if (is_array($value)) {
                    $arr1[$key] = $this->mergeTranslations($arr1[$key], $arr2[$key]);
                } else {
                    if ($arr1[$key] != $value) {
                        throw new Exception('Different value ' . $value . ' for key ' . $key . ' was found!');
                    }
                }
            } else {
                $arr1[$key] = $value;
            }
        }
        return $arr1;
    }

    //Only accessories with status OK are exported. The bridge always occupies aid 1
    private function getExportedAccessoryObjects(): array
    {
        $objects = [];

        foreach ($this->getAccessoriesData() as $data) {
            if (!in_array($data['Type'] ?? '', self::$supportedAccessories)) {
                continue;
            }

            $aid = intval($data['ID']) + 1;

            if (isset($objects[$aid])) {
                throw new Exception('AccessoryID has to be unique for all accessories');
            }

            if (call_user_func(self::configurationClassPrefix . $data['Type'] . '::getStatus', $data) != 'OK') {
                continue;
            }

            $class = self::classPrefix . $data['Type'];
            $object = new $class($data);

            if ($object instanceof HAPAccessory) {
                $objects[$aid] = $object;
            }
        }

        //Everything above the limit is dropped. Lowest IDs win, so adding accessories never removes existing ones
        ksort($objects);

        return array_slice($objects, 0, self::maxAccessories, true);
    }

    private function getAccessoriesData(): array
    {
        $datas = json_decode(IPS_GetProperty($this->instanceID, 'Accessories'), true);
        if (!is_array($datas)) {
            return [];
        }
        return $datas;
    }

    private function getAccessoryObject(int $aid): object
    {
        if ($this->accessoryCache !== null) {
            if (!isset($this->accessoryCache[$aid])) {
                $this->accessoryCache[$aid] = $this->createAccessoryObject($aid);
            }
            return $this->accessoryCache[$aid];
        }

        return $this->createAccessoryObject($aid);
    }

    private function createAccessoryObject(int $aid): object
    {
        if ($aid == 1) {
            $class = self::classPrefix . 'Bridge';
            $bridge = new $class([
                'Name' => IPS_GetProperty($this->instanceID, 'BridgeName')
            ]);
            if (!($bridge instanceof HAPAccessory)) {
                throw new Exception(sprintf('Cannot use accessory with ID %d', $aid));
            }

            return $bridge;
        }

        $datas = $this->getAccessoriesData();

        //Accessories above the limit are not exposed, so they must not be reachable either
        if ((count($datas) > self::maxAccessories) && !array_key_exists($aid, $this->getExportedAccessoryObjects())) {
            throw new Exception(sprintf('Cannot find accessory with ID %d', $aid));
        }

        foreach ($datas as $data) {
            if ((intval($data['ID']) + 1) == $aid) {
                if (!in_array($data['Type'] ?? '', self::$supportedAccessories)) {
                    break;
                }

                $class = self::classPrefix . $data['Type'];
                $object = new $class($data);
                if (!($object instanceof HAPAccessory)) {
                    throw new Exception(sprintf('Cannot use accessory with ID %d', $aid));
                }

                return $object;
            }
        }

        throw new Exception(sprintf('Cannot find accessory with ID %d', $aid));
    }
}
