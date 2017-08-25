<?php

declare(strict_types=1);
class HAPAccessory
{
    private $services;

    protected $data;

    public function __construct($data, $services)
    {
        if ((count($services) == 0) || !($services[0] instanceof HAPServiceAccessoryInformation)) {
            throw new Exception('First service is required to be HAPServiceAccessoryInformation');
        }
        $this->services = $services;
        $this->data = $data;
    }

    public function doExport($accessoryID)
    {
        $instanceID = 1;
        $services = [];
        foreach ($this->services as $service) {
            $services[] = $service->doExport($instanceID, $this);
            $instanceID += 100;
        }

        return [
            'aid'      => $accessoryID,
            'services' => $services
        ];
    }

    public function setCharacteristic($instanceID, $value)
    {
        $index = intval(floor($instanceID / 100));

        if ($index >= count($this->services)) {
            throw new Exception('InstanceID is out of bounds for accessory!');
        }
        $this->services[$index]->setCharacteristic($instanceID % 100, $value, $this);
    }

    public function getCharacteristic($instanceID)
    {
        $index = intval(floor($instanceID / 100));

        if ($index >= count($this->services)) {
            throw new Exception('InstanceID is out of bounds for accessory!');
        }

        return $this->services[$index]->getCharacteristic($instanceID % 100, $this);
    }
}

class HAPService
{
    private $type;
    private $requiredCharacteristics;
    private $optionalCharacteristics;

    public function __construct($type, $requiredCharacteristics, $optionalCharacteristics)
    {
        $this->type = $type;
        $this->requiredCharacteristics = $requiredCharacteristics;
        $this->optionalCharacteristics = $optionalCharacteristics;
    }

    private function validateCharacteristicValue($instanceID, $value)
    {
        $characteristics = array_merge($this->requiredCharacteristics, $this->optionalCharacteristics);
        $index = $instanceID - 2; //First InstanceID is the sevice itself - starting with 1

        if ($index >= count($characteristics)) {
            throw new Exception('InstanceID is out of bounds for accessory!');
        }

        if (is_int($value)) {
            if (!is_null($characteristics[$index]->getMinValue())) { /* Sollte reichen?! Wenn MinValue gesetzt ist, ist auch MaxValue gesetzt! */
                if ($value < $characteristics[$index]->getMinValue() or $value > $characteristics[$index]->getMaxValue()) {
                    $value = $characteristics[$index]->getMaxValue(); /* ToDo: wenn über MaxValue, MaxValue ausgeben, wenn unter MinValue, MinValue ausgeben?! */
                } else {
                    switch ($characteristics[$index]->getformat()) {
                  case HAPCharacteristicFormat::UnsignedInt8:
                    IPS_LogMessage('validateCharacteristicValue', 'UnsignedInt8');
                    return $value;
                    break;
                    case HAPCharacteristicFormat::UnsignedInt16:
                    IPS_LogMessage('validateCharacteristicValue', 'UnsignedInt16');
                    return $value;
                    break;
                  case HAPCharacteristicFormat::UnsignedInt32:
                    IPS_LogMessage('validateCharacteristicValue', 'UnsignedInt32');
                    return $value;
                    break;
                  case HAPCharacteristicFormat::UnsignedInt64:
                    IPS_LogMessage('validateCharacteristicValue', 'UnsignedInt64');
                    return $value;
                    break;
                  case HAPCharacteristicFormat::Integer:
                    IPS_LogMessage('validateCharacteristicValue', 'Integer');
                    return $value;
                    break;
                }
                }
            }
        }

        if (is_bool($value)) {
            IPS_LogMessage('validateCharacteristicValue', 'Boolean');
            /*ToDo: Wenn is_bool nicht greift? Was wollen wir dann ausgeben? */
            return $value;
        }

        if (is_float($value)) {
            if (!is_null($characteristics[$index]->getMinValue())) { /* Sollte reichen?! Wenn MinValue gesetzt ist, ist auch MaxValue gesetzt! */
                if ($value < $characteristics[$index]->getMinValue() or $value > $characteristics[$index]->getMaxValue()) {
                    $value = $characteristics[$index]->getMaxValue(); /* ToDo: wenn über MaxValue, MaxValue ausgeben, wenn unter MinValue, MinValue ausgeben?! */
                }
            }
            IPS_LogMessage('validateCharacteristicValue', 'Float');
            return $value;
        }

        if (is_string($value)) {
            IPS_LogMessage('validateCharacteristicValue', 'String');
            /*ToDo: Wenn is_string nicht greift? Was wollen wir dann ausgeben? Einfach einen String: "Value is not valid!"? */
            return $value;
        }
        /*ToDo check value on type Data and TLV8 */
        IPS_LogMessage('Apple HomeKit', 'Value ist not valid!' . ' ' . $value . ' ' . get_class($characteristics[$index]));
    }

    public function setCharacteristic($instanceID, $value, $accessory)
    {
        $characteristics = array_merge($this->requiredCharacteristics, $this->optionalCharacteristics);

        $index = $instanceID - 2; //First InstanceID is the sevice itself - starting with 1

        if ($index >= count($characteristics)) {
            throw new Exception('InstanceID is out of bounds for service!');
        }
        $accessory->{$this->makeSetFunctionName($characteristics[$index])}($value);
    }

    public function getCharacteristic($instanceID, $accessory)
    {
        $characteristics = array_merge($this->requiredCharacteristics, $this->optionalCharacteristics);

        $index = $instanceID - 2; //First InstanceID is the sevice itself - starting with 1

        if ($index >= count($characteristics)) {
            throw new Exception('InstanceID is out of bounds for accessory!');
        }

        $value = $this->validateCharacteristicValue($instanceID, $accessory->{$this->makeGetFunctionName($characteristics[$index])}());
        return $value;
    }

    public function doExport($baseInstanceID, $accessory)
    {
        $instanceID = $baseInstanceID;
        $characteristics = [];

        //Throw error if any of the required functions are not implemented
        foreach ($this->requiredCharacteristics as $characteristic) {

            //Always increment InstanceID
            $instanceID++;

            //Default value
            $value = null;

            if ($characteristic->hasPermission(HAPCharacteristicPermission::PairedWrite)) {

                //Check if Class properly implements the setter
                if (!method_exists($accessory, $this->makeSetFunctionName($characteristic))) {
                    throw new Exception('Missing function ' . $this->makeSetFunctionName($characteristic) . ' in Accessory ' . get_class($accessory));
                }
            }

            if ($characteristic->hasPermission(HAPCharacteristicPermission::PairedRead)) {

                //Check if Class properly implements the getter
                if (!method_exists($accessory, $this->makeGetFunctionName($characteristic))) {
                    throw new Exception('Missing function ' . $this->makeGetFunctionName($characteristic) . ' in Accessory ' . get_class($accessory));
                }

                //Call the function to get the current value
                $value = $accessory->{$this->makeGetFunctionName($characteristic)}();
            }

            $characteristics[] = $characteristic->doExport($instanceID, $value);
        }

        //Throw error if an incomplete set of functions is implemented
        foreach ($this->optionalCharacteristics as $characteristic) {

            //Always increment InstanceID
            $instanceID++;

            //Default value
            $value = null;

            $requireSetter = $characteristic->hasPermission(HAPCharacteristicPermission::PairedWrite);
            $requireGetter = $characteristic->hasPermission(HAPCharacteristicPermission::PairedRead);

            $hasSetter = method_exists($accessory, $this->makeSetFunctionName($characteristic));
            $hasGetter = method_exists($accessory, $this->makeGetFunctionName($characteristic));

            //Characteristic is not defined. Just continue as it is optional
            if (!$hasSetter && !$hasGetter) {
                continue;
            }

            //Check for requirements
            if ($requireSetter && !$hasSetter) {
                throw new Exception('Missing setter function for characteristic ' . get_class($characteristic) . ' in Accessory ' . get_class($accessory));
            }
            if ($requireGetter && !$hasGetter) {
                throw new Exception('Missing getter function for characteristic ' . get_class($characteristic) . ' in Accessory ' . get_class($accessory));
            }

            //Call the function to get the current value
            $value = $accessory->{$this->makeGetFunctionName($characteristic)}();

            $characteristics[] = $characteristic->doExport($instanceID, $value);
        }

        return [
            'type'            => strtoupper(dechex($this->type)),
            'iid'             => $baseInstanceID,
            'characteristics' => $characteristics
        ];
    }

    private function makeGetFunctionName($characteristic)
    {
        //Filter HAP from Class Name
        return 'get' . substr(get_class($characteristic), 3);
    }

    private function makeSetFunctionName($characteristic)
    {
        //Filter HAP from Class Name
        return 'set' . substr(get_class($characteristic), 3);
    }
}

class HAPCharacteristicFormat
{
    const Boolean = 'bool';
    const UnsignedInt8 = 'uint8';
    const UnsignedInt16 = 'uint16';
    const UnsignedInt32 = 'uint32';
    const UnsignedInt64 = 'uint64';
    const Integer = 'int';
    const Float = 'float';
    const String = 'string';
    const TLV8 = 'tlv8';
    const Data = 'data';
}

class HAPCharacteristicPermission
{
    const PairedRead = 'pr';
    const PairedWrite = 'pw';
    const Notify = 'ev'; //Originally named Events, but somehow used as Notify everywhere
    const AdditionalAuthorization = 'aa';
    const TimedWrite = 'tw';
    const Hidden = 'hd';
}

class HAPCharacteristicUnit
{
    const Celsius = 'celsius';
    const Percentage = 'percentage';
    const ArcDegrees = 'arcdegrees';
    const Lux = 'lux';
    const Seconds = 'seconds';
}

class HAPCharacteristic
{
    private $type;
    private $format;
    private $permissions;
    private $minValue;
    private $maxValue;
    private $minStep;
    private $unit;
    private $maxLen;

    public function __construct($type, $format, $permissions, $minValue = null, $maxValue = null, $minStep = null, $unit = null, $maxLen = null)
    {
        $this->type = $type;
        $this->format = $format;
        $this->permissions = $permissions;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        $this->minStep = $minStep;
        $this->unit = $unit;
        $this->maxLen = $maxLen;
    }

    public function doExport($instanceID, $value)
    {
        $export = [
            'type'   => strtoupper(dechex($this->getType())),
            'iid'    => $instanceID,
            'format' => $this->getFormat(),
            'perms'  => $this->getPermissions()
        ];

        if ($value !== null) {
            $export['value'] = $value;
        }

        if ($this->getMinValue() !== null) {
            $export['minValue'] = $this->getMinValue();
        }

        if ($this->getMaxValue() !== null) {
            $export['maxValue'] = $this->getMaxValue();
        }

        if ($this->getMinStep() !== null) {
            $export['minStep'] = $this->getMinStep();
        }

        if ($this->getUnit() !== null) {
            $export['unit'] = $this->getUnit();
        }

        if ($this->getMaxLen() !== null) {
            $export['maxLen'] = $this->getMaxLen();
        }

        return $export;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions);
    }

    public function getMinValue()
    {
        return $this->minValue;
    }

    public function getMaxValue()
    {
        return $this->maxValue;
    }

    public function getMinStep()
    {
        return $this->minStep;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getMaxLen()
    {
        return $this->maxLen;
    }
}
