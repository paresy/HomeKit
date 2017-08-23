<?php

declare(strict_types=1);

class IPSKernel {
    public static $objects = [];
    public static $categories = [];
    public static $instances = [];

    public static $libraries = [];
    public static $modules = [];

    public static function loadLibrary(string $file): void {
        $library = json_decode(file_get_contents($file), true);
        self::$libraries[$library['id']] = [
            'LibraryID' => $library['id'],
            'Author' => $library['author'],
            'URL' => $library['url'],
            'Name' => $library['name'],
            'Version' => $library['version'],
            'Build' => $library['build'],
            'Date' => $library['date'],
        ];
    }
}

function IPS_ApplyChanges(int $InstanceID)
{
    return true;
}

function IPS_CategoryExists(int $CategoryID)
{
    return true;
}

function IPS_ConnectInstance(int $InstanceID, int $ParentID)
{
    return true;
}

function IPS_CreateCategory()
{
    return 0;
}

function IPS_CreateEvent(int $EventType)
{
    return 0;
}

function IPS_CreateInstance(string $ModuleID)
{
    return 0;
}

function IPS_CreateLink()
{
    return 0;
}

function IPS_CreateMedia(int $MediaType)
{
    return 0;
}

function IPS_CreateScript(int $ScriptType)
{
    return 0;
}

function IPS_CreateVariable(int $VariableType)
{
    return 0;
}

function IPS_CreateVariableProfile(string $ProfileName, int $ProfileType)
{
    return true;
}

function IPS_DeleteCategory(int $CategoryID)
{
    return true;
}

function IPS_DeleteEvent(int $EventID)
{
    return true;
}

function IPS_DeleteInstance(int $InstanceID)
{
    return true;
}

function IPS_DeleteLink(int $LinkID)
{
    return true;
}

function IPS_DeleteMedia(int $MediaID, bool $DeleteFile)
{
    return true;
}

function IPS_DeleteScript(int $ScriptID, bool $DeleteFile)
{
    return true;
}

function IPS_DeleteVariable(int $VariableID)
{
    return true;
}

function IPS_DeleteVariableProfile(string $ProfileName)
{
    return true;
}

function IPS_DisableDebug(int $ID)
{
    return true;
}

function IPS_DisconnectInstance(int $InstanceID)
{
    return true;
}

function IPS_EnableDebug(int $ID, int $Duration)
{
    return true;
}

function IPS_EventExists(int $EventID)
{
    return true;
}

function IPS_Execute(string $Filename, string $Parameter, bool $ShowWindow, bool $WaitResult)
{
    return '';
}

function IPS_ExecuteEx(string $Filename, string $Parameter, bool $ShowWindow, bool $WaitResult, int $SessionID)
{
    return '';
}

function IPS_FunctionExists(string $FunctionName)
{
    return true;
}

function IPS_GetCategory(int $CategoryID)
{
    return [];
}

function IPS_GetCategoryIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetCategoryList()
{
    return [];
}

function IPS_GetChildrenIDs(int $ID)
{
    return [];
}

function IPS_GetCompatibleInstances(int $InstanceID)
{
    return [];
}

function IPS_GetCompatibleModules(string $ModuleID)
{
    return [];
}

function IPS_GetConfiguration(int $InstanceID)
{
    return '';
}

function IPS_GetConfigurationForParent(int $InstanceID)
{
    return '';
}

function IPS_GetConfigurationForm(int $InstanceID)
{
    return '';
}

function IPS_GetDemoExpiration()
{
    return 0;
}

function IPS_GetEvent(int $EventID)
{
    return [];
}

function IPS_GetEventIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetEventList()
{
    return [];
}

function IPS_GetEventListByType(int $EventType)
{
    return [];
}

function IPS_GetFunction(string $FunctionName)
{
    return [];
}

function IPS_GetFunctionList(int $InstanceID)
{
    return [];
}

function IPS_GetFunctionListByModuleID(string $ModuleID)
{
    return [];
}

function IPS_GetFunctions(array $Parameter)
{
    return [];
}

function IPS_GetFunctionsMap(array $Parameter)
{
    return [];
}

function IPS_GetInstance(int $InstanceID)
{
    return [];
}

function IPS_GetInstanceIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetInstanceList()
{
    return [];
}

function IPS_GetInstanceListByModuleID(string $ModuleID)
{
    return [];
}

function IPS_GetInstanceListByModuleType(int $ModuleType)
{
    return [];
}

function IPS_GetKernelDate()
{
    return 0;
}

function IPS_GetKernelDir()
{
    return '';
}

function IPS_GetKernelDirEx()
{
    return '';
}

function IPS_GetKernelRunlevel()
{
    return 0;
}

function IPS_GetKernelStartTime()
{
    return 0;
}

function IPS_GetKernelVersion()
{
    return '';
}

function IPS_GetLibraries(array $Parameter)
{
    return [];
}

function IPS_GetLibrary(string $LibraryID)
{
    return [];
}

function IPS_GetLibraryList()
{
    return [];
}

function IPS_GetLibraryModules(string $LibraryID)
{
    return [];
}

function IPS_GetLicensee()
{
    return 'max@mustermann.de';
}

function IPS_GetLimitDemo()
{
    return 0;
}

function IPS_GetLimitServer()
{
    return '';
}

function IPS_GetLimitVariables()
{
    return 0;
}

function IPS_GetLimitWebFront()
{
    return 0;
}

function IPS_GetLink(int $LinkID)
{
    return [];
}

function IPS_GetLinkIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetLinkList()
{
    return [];
}

function IPS_GetLiveConsoleCRC()
{
    return '';
}

function IPS_GetLiveConsoleFile()
{
    return '';
}

function IPS_GetLiveDashboardCRC()
{
    return '';
}

function IPS_GetLiveDashboardFile()
{
    return '';
}

function IPS_GetLiveUpdateVersion()
{
    return '';
}

function IPS_GetLocation(int $ID)
{
    return '';
}

function IPS_GetLogDir()
{
    return '';
}

function IPS_GetMedia(int $MediaID)
{
    return [];
}

function IPS_GetMediaContent(int $MediaID)
{
    return '';
}

function IPS_GetMediaIDByFile(string $FilePath)
{
    return 0;
}

function IPS_GetMediaIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetMediaList()
{
    return [];
}

function IPS_GetMediaListByType(int $MediaType)
{
    return [];
}

function IPS_GetModule(string $ModuleID)
{
    return [];
}

function IPS_GetModuleList()
{
    return [];
}

function IPS_GetModuleListByType(int $ModuleType)
{
    return [];
}

function IPS_GetModules(array $Parameter)
{
    return [];
}

function IPS_GetName(int $ID)
{
    return '';
}

function IPS_GetObject(int $ID)
{
    return [];
}

function IPS_GetObjectIDByIdent(string $Ident, int $ParentID)
{
    return 0;
}

function IPS_GetObjectIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetObjectList()
{
    return [];
}

function IPS_GetOption(string $Option)
{
    return 0;
}

function IPS_GetParent(int $ID)
{
    return 0;
}

function IPS_GetProperty(int $InstanceID, string $Name)
{
    return '';
}

function IPS_GetScript(int $ScriptID)
{
    return [];
}

function IPS_GetScriptContent(int $ScriptID)
{
    return '';
}

function IPS_GetScriptEventList(int $ScriptID)
{
    return [];
}

function IPS_GetScriptFile(int $ScriptID)
{
    return '';
}

function IPS_GetScriptIDByFile(string $FilePath)
{
    return 0;
}

function IPS_GetScriptIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetScriptList()
{
    return [];
}

function IPS_GetScriptThread(int $ThreadID)
{
    return [];
}

function IPS_GetScriptThreadList()
{
    return [];
}

function IPS_GetScriptThreads(array $Parameter)
{
    return [];
}

function IPS_GetScriptTimer(int $ScriptID)
{
    return 0;
}

function IPS_GetSecurityMode()
{
    return 0;
}

function IPS_GetSnapshot()
{
    return [];
}

function IPS_GetSnapshotChanges(int $LastTimestamp)
{
    return [];
}

function IPS_GetTimer(int $TimerID)
{
    return [];
}

function IPS_GetTimerList()
{
    return [];
}

function IPS_GetTimers(array $Parameter)
{
    return [];
}

function IPS_GetVariable(int $VariableID)
{
    return [];
}

function IPS_GetVariableEventList(int $VariableID)
{
    return [];
}

function IPS_GetVariableIDByName(string $Name, int $ParentID)
{
    return 0;
}

function IPS_GetVariableList()
{
    return [];
}

function IPS_GetVariableProfile(string $ProfileName)
{
    return [];
}

function IPS_GetVariableProfileList()
{
    return [];
}

function IPS_GetVariableProfileListByType(int $ProfileType)
{
    return [];
}

function IPS_HasChanges(int $InstanceID)
{
    return true;
}

function IPS_HasChildren(int $ID)
{
    return true;
}

function IPS_InstanceExists(int $InstanceID)
{
    return true;
}

function IPS_IsChild(int $ID, int $ParentID, bool $Recursive)
{
    return true;
}

function IPS_IsInstanceCompatible(int $InstanceID, int $ParentInstanceID)
{
    return true;
}

function IPS_IsModuleCompatible(string $ModuleID, string $ParentModuleID)
{
    return true;
}

function IPS_IsSearching(int $InstanceID)
{
    return true;
}

function IPS_LibraryExists(string $LibraryID)
{
    return true;
}

function IPS_LinkExists(int $LinkID)
{
    return true;
}

function IPS_LogMessage(string $Sender, string $Message)
{
    return true;
}

function IPS_MediaExists(int $MediaID)
{
    return true;
}

function IPS_ModuleExists(string $ModuleID)
{
    return true;
}

function IPS_ObjectExists(int $ID)
{
    return true;
}

function IPS_RequestAction(int $InstanceID, string $VariableIdent, $Value)
{
    return true;
}

function IPS_ResetChanges(int $InstanceID)
{
    return true;
}

function IPS_RunScript(int $ScriptID)
{
    return true;
}

function IPS_RunScriptEx(int $ScriptID, array $Parameters)
{
    return true;
}

function IPS_RunScriptText(string $ScriptText)
{
    return true;
}

function IPS_RunScriptTextEx(string $ScriptText, array $Parameters)
{
    return true;
}

function IPS_RunScriptTextWait(string $ScriptText)
{
    return '';
}

function IPS_RunScriptTextWaitEx(string $ScriptText, array $Parameters)
{
    return '';
}

function IPS_RunScriptWait(int $ScriptID)
{
    return '';
}

function IPS_RunScriptWaitEx(int $ScriptID, array $Parameters)
{
    return '';
}

function IPS_ScriptExists(int $ScriptID)
{
    return true;
}

function IPS_ScriptThreadExists(int $ThreadID)
{
    return true;
}

function IPS_SemaphoreEnter(string $Name, int $Milliseconds)
{
    return true;
}

function IPS_SemaphoreLeave(string $Name)
{
    return true;
}

function IPS_SendDebug(int $SenderID, string $Message, string $Data, int $Format)
{
    return true;
}

function IPS_SendMediaEvent(int $MediaID)
{
    return true;
}

function IPS_SetConfiguration(int $InstanceID, string $Configuration)
{
    return true;
}

function IPS_SetDisabled(int $ID, bool $Disabled)
{
    return true;
}

function IPS_SetEventActive(int $EventID, bool $Active)
{
    return true;
}

function IPS_SetEventCyclic(int $EventID, int $DateType, int $DateValue, int $DateDay, int $DateDayValue, int $TimeType, int $TimeValue)
{
    return true;
}

function IPS_SetEventCyclicDateFrom(int $EventID, int $Day, int $Month, int $Year)
{
    return true;
}

function IPS_SetEventCyclicDateTo(int $EventID, int $Day, int $Month, int $Year)
{
    return true;
}

function IPS_SetEventCyclicTimeFrom(int $EventID, int $Hour, int $Minute, int $Second)
{
    return true;
}

function IPS_SetEventCyclicTimeTo(int $EventID, int $Hour, int $Minute, int $Second)
{
    return true;
}

function IPS_SetEventLimit(int $EventID, int $Count)
{
    return true;
}

function IPS_SetEventScheduleAction(int $EventID, int $ActionID, string $Name, int $Color, string $ScriptText)
{
    return true;
}

function IPS_SetEventScheduleGroup(int $EventID, int $GroupID, int $Days)
{
    return true;
}

function IPS_SetEventScheduleGroupPoint(int $EventID, int $GroupID, int $PointID, int $StartHour, int $StartMinute, int $StartSecond, int $ActionID)
{
    return true;
}

function IPS_SetEventScript(int $EventID, string $EventScript)
{
    return true;
}

function IPS_SetEventTrigger(int $EventID, int $TriggerType, int $TriggerVariableID)
{
    return true;
}

function IPS_SetEventTriggerSubsequentExecution(int $EventID, bool $AllowSubsequentExecutions)
{
    return true;
}

function IPS_SetEventTriggerValue(int $EventID, $TriggerValue)
{
    return true;
}

function IPS_SetHidden(int $ID, bool $Hidden)
{
    return true;
}

function IPS_SetIcon(int $ID, string $Icon)
{
    return true;
}

function IPS_SetIdent(int $ID, string $Ident)
{
    return true;
}

function IPS_SetInfo(int $ID, string $Info)
{
    return true;
}

function IPS_SetLicense(string $Licensee, string $LicenseContent)
{
    return true;
}

function IPS_SetLinkTargetID(int $LinkID, int $ChildID)
{
    return true;
}

function IPS_SetMediaCached(int $MediaID, bool $Cached)
{
    return true;
}

function IPS_SetMediaContent(int $MediaID, string $Content)
{
    return true;
}

function IPS_SetMediaFile(int $MediaID, string $FilePath, bool $FileMustExists)
{
    return true;
}

function IPS_SetName(int $ID, string $Name)
{
    return true;
}

function IPS_SetOption(string $Option, int $Value)
{
    return true;
}

function IPS_SetParent(int $ID, int $ParentID)
{
    return true;
}

function IPS_SetPosition(int $ID, int $Position)
{
    return true;
}

function IPS_SetProperty(int $InstanceID, string $Name, $Value)
{
    return true;
}

function IPS_SetScriptContent(int $ScriptID, string $Content)
{
    return true;
}

function IPS_SetScriptFile(int $ScriptID, string $FilePath)
{
    return true;
}

function IPS_SetScriptTimer(int $ScriptID, int $Interval)
{
    return true;
}

function IPS_SetSecurity(int $Mode, string $Password)
{
    return true;
}

function IPS_SetVariableCustomAction(int $VariableID, int $ScriptID)
{
    return true;
}

function IPS_SetVariableCustomProfile(int $VariableID, string $ProfileName)
{
    return true;
}

function IPS_SetVariableProfileAssociation(string $ProfileName, float $AssociationValue, string $AssociationName, string $AssociationIcon, int $AssociationColor)
{
    return true;
}

function IPS_SetVariableProfileDigits(string $ProfileName, int $Digits)
{
    return true;
}

function IPS_SetVariableProfileIcon(string $ProfileName, string $Icon)
{
    return true;
}

function IPS_SetVariableProfileText(string $ProfileName, string $Prefix, string $Suffix)
{
    return true;
}

function IPS_SetVariableProfileValues(string $ProfileName, float $MinValue, float $MaxValue, float $StepSize)
{
    return true;
}

function IPS_Sleep(int $Milliseconds)
{
    return 0;
}

function IPS_StartSearch(int $InstanceID)
{
    return true;
}

function IPS_StopSearch(int $InstanceID)
{
    return true;
}

function IPS_SupportsSearching(int $InstanceID)
{
    return true;
}

function IPS_TimerExists(int $TimerID)
{
    return true;
}

function IPS_VariableExists(int $VariableID)
{
    return true;
}

function IPS_VariableProfileExists(string $ProfileName)
{
    return true;
}

