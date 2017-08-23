<?php

declare(strict_types=1);

class IPSModule
{
    protected $InstanceID;

    private $propertiesBoolean = [];
    private $propertiesInteger = [];
    private $propertiesFloat = [];
    private $propertiesString = [];

    private $buffer = [];
    private $receiveDataFilter = "";
    private $forwardDataFilter = "";

    public function __construct($InstanceID)
    {
        $this->InstanceID = $InstanceID;
    }

    public function Create()
    {
        //Has to be overwritten by implementing module
    }

    public function Destroy()
    {
        //Has to be overwritten by implementing module
    }

    protected function GetIDForIdent($Ident)
    {
        return IPS_GetObjectIDByIdent($Ident, $this->InstanceID);
    }

    protected function RegisterPropertyBoolean($Name, $DefaultValue)
    {
        $this->propertiesBoolean[$Name] = $DefaultValue;
    }

    protected function RegisterPropertyInteger($Name, $DefaultValue)
    {
        $this->propertiesInteger[$Name] = $DefaultValue;
    }

    protected function RegisterPropertyFloat($Name, $DefaultValue)
    {
        $this->propertiesFloat[$Name] = $DefaultValue;
    }

    protected function RegisterPropertyString($Name, $DefaultValue)
    {
        $this->propertiesString[$Name] = $DefaultValue;
    }

    protected function RegisterTimer($Ident, $Milliseconds, $ScriptText)
    {
        //How and why do we want to test this?
    }

    protected function SetTimerInterval($Ident, $Milliseconds)
    {
        //How and why do we want to test this?
    }

    protected function RegisterScript($Ident, $Name, $Content, $Position)
    {
        //How and why do we want to test this?
    }

    private function RegisterVariable($Ident, $Name, $Type, $Profile = "", $Position = 0) {

        if($Profile != "") {
            //prefer system profiles
            if(IPS_VariableProfileExists("~".$Profile)) {
                $Profile = "~".$Profile;
            }
            if(!IPS_VariableProfileExists($Profile)) {
                throw new Exception("Profile with name ".$Profile." does not exist");
            }
        }

        //search for already available variables with proper ident
        $vid = @IPS_GetObjectIDByIdent($Ident, $this->InstanceID);

        //properly update variableID
        if($vid === false)
            $vid = 0;

        //we have a variable with the proper ident. check if it fits
        if($vid > 0) {
            //check if we really have a variable
            if(!IPS_VariableExists($vid))
                throw new Exception("Ident with name ".$Ident." is used for wrong object type"); //bail out

            //check for type mismatch
            if(IPS_GetVariable($vid)["VariableType"] != $Type) {
                //mismatch detected. delete this one. we will create a new below
                IPS_DeleteVariable($vid);
                //this will ensure, that a new one is created
                $vid = 0;
            }

        }

        //we need to create one
        if($vid == 0)
        {
            $vid = IPS_CreateVariable($Type);

            //configure it
            IPS_SetParent($vid, $this->InstanceID);
            IPS_SetIdent($vid, $Ident);
            IPS_SetName($vid, $Name);
            IPS_SetPosition($vid, $Position);
            //IPS_SetReadOnly($vid, true);

        }

        //update variable profile. profiles may be changed in module development.
        //this update does not affect any custom profile choices
        IPS_SetVariableCustomProfile($vid, $Profile);

        return $vid;

    }

    protected function RegisterVariableBoolean($Ident, $Name, $Profile, $Position)
    {
        return $this->RegisterVariable($Ident, $Name, 0, $Profile, $Position);
    }

    protected function RegisterVariableInteger($Ident, $Name, $Profile, $Position)
    {
        return $this->RegisterVariable($Ident, $Name, 1, $Profile, $Position);
    }

    protected function RegisterVariableFloat($Ident, $Name, $Profile, $Position)
    {
        return $this->RegisterVariable($Ident, $Name, 2, $Profile, $Position);
    }

    protected function RegisterVariableString($Ident, $Name, $Profile, $Position)
    {
        return $this->RegisterVariable($Ident, $Name, 3, $Profile, $Position);
    }

    protected function UnregisterVariable($Ident)
    {
        $vid = @IPS_GetObjectIDByIdent($Ident, $this->InstanceID);
        if($vid === false)
            return;
        if(!IPS_VariableExists($vid))
            return; //bail out
        IPS_DeleteVariable($vid);
    }

    protected function MaintainVariable($Ident, $Name, $Type, $Profile, $Position, $Keep)
    {
        if($Keep) {
            $this->RegisterVariable($Ident, $Name, $Type, $Profile, $Position);
        } else {
            $this->UnregisterVariable($Ident);
        }
    }

    protected function EnableAction($Ident)
    {
    }

    protected function DisableAction($Ident)
    {
    }

    protected function MaintainAction($Ident, $Keep)
    {
        if($Keep) {
            $this->EnableAction($Ident);
        } else {
            $this->DisableAction($Ident);
        }
    }

    protected function ReadPropertyBoolean($Name)
    {
        return $this->propertiesBoolean[$Name];
    }

    protected function ReadPropertyInteger($Name)
    {
        return $this->propertiesInteger[$Name];
    }

    protected function ReadPropertyFloat($Name)
    {
        return $this->propertiesFloat[$Name];
    }

    protected function ReadPropertyString($Name)
    {
        return $this->propertiesString[$Name];
    }

    protected function SendDataToParent($Data)
    {
    }

    protected function SendDataToChildren($Data)
    {
    }

    protected function ConnectParent($ModuleID)
    {
    }

    protected function RequireParent($ModuleID)
    {
    }

    protected function ForceParent($ModuleID)
    {
    }

    protected function SetStatus($Status)
    {
    }

    protected function SetSummary($Summary)
    {
    }

    protected function SetBuffer($Name, $Data)
    {
        $this->buffer[$Name] = $Data;
    }

    protected function GetBuffer($Name)
    {
        if(isset($this->buffer[$Name])) {
            return $this->buffer[$Name];
        } else {
            return '';
        }
    }

    protected function SendDebug($Message, $Data, $Format)
    {
        if($Format == 1 /* Binary */) {
            $Data = bin2hex($Data);
        }

        echo 'DEBUG: ' . $Message . ' | ' . $Data;
    }

    protected function RegisterMessage($SenderID, $Message)
    {
    }

    protected function UnregisterMessage($SenderID, $Message)
    {
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        //Has to be overwritten by implementing module
    }

    public function ApplyChanges()
    {
    }

    protected function SetReceiveDataFilter($RequiredRegexMatch)
    {
        $this->receiveDataFilter = $RequiredRegexMatch;
    }

    public function ReceiveData($JSONString)
    {
        //Has to be overwritten by implementing module
    }

    protected function SetForwardDataFilter($RequiredRegexMatch)
    {
        $this->forwardDataFilter = $RequiredRegexMatch;
    }

    public function ForwardData($JSONString)
    {
        //Has to be overwritten by implementing module
    }

    public function RequestAction($Ident, $Value)
    {
        //Has to be overwritten by implementing module
    }

    public function GetConfigurationForm()
    {
        return '{}';
    }

    public function GetConfigurationForParent()
    {
        return '{}';
    }

    public function Translate($Text)
    {
        return $Text;
    }
}