<?php

declare(strict_types=1);

/* Object Manager */
function IPS_SetParent(int $ID, int $ParentID)
{
    IPS\Kernel::setParent($ID, $ParentID);
}

function IPS_SetIdent(int $ID, string $Ident)
{
    IPS\Kernel::setIdent($ID, $Ident);
}

function IPS_SetName(int $ID, string $Name)
{
    IPS\Kernel::setName($ID, $Name);
}

function IPS_SetInfo(int $ID, string $Info)
{
    IPS\Kernel::setInfo($ID, $Info);
}

function IPS_SetIcon(int $ID, string $Icon)
{
    IPS\Kernel::setIcon($ID, $Icon);
}

function IPS_SetPosition(int $ID, int $Position)
{
    IPS\Kernel::setPosition($ID, $Position);
}

function IPS_SetHidden(int $ID, bool $Hidden)
{
    IPS\Kernel::setHidden($ID, $Hidden);
}

function IPS_SetDisabled(int $ID, bool $Disabled)
{
    IPS\Kernel::setDisabled($ID, $Disabled);
}

function IPS_ObjectExists(int $ID)
{
    return IPS\Kernel::objectExists($ID);
}

function IPS_GetObject(int $ID)
{
    return IPS\Kernel::getObject($ID);
}

function IPS_GetObjectList()
{
    return IPS\Kernel::getObjectList();
}

function IPS_GetObjectIDByName(string $Name, int $ParentID)
{
    return IPS\Kernel::getObjectIDByName($Name, $ParentID);
}

function IPS_GetObjectIDByIdent(string $Ident, int $ParentID)
{
    return IPS\Kernel::getObjectIDByIdent($Ident, $ParentID);
}

function IPS_HasChildren(int $ID)
{
    return IPS\Kernel::hasChildren($ID);
}

function IPS_IsChild(int $ID, int $ParentID, bool $Recursive)
{
    return IPS\Kernel::isChild($ID, $ParentID, $Recursive);
}

function IPS_GetChildrenIDs(int $ID)
{
    return IPS\Kernel::getChildrenIDs($ID);
}

function IPS_GetName(int $ID)
{
    return IPS\Kernel::getName($ID);
}

function IPS_GetParent(int $ID)
{
    return IPS\Kernel::getParent($ID);
}

function IPS_GetLocation(int $ID)
{
    return IPS\Kernel::getLocation($ID);
}

/* Category Manager */
function IPS_CreateCategory()
{
    $id = IPS\Kernel::registerObject(0 /* Category */);
    IPS\Kernel::createCategory($id);

    return $id;
}

function IPS_DeleteCategory(int $CategoryID)
{
    IPS\Kernel::deleteCategory($CategoryID);
    IPS\Kernel::unregisterObject($CategoryID);
}

function IPS_CategoryExists(int $CategoryID)
{
    return IPS\Kernel::categoryExists($CategoryID);
}

function IPS_GetCategory(int $CategoryID)
{
    return IPS\Kernel::getCategory($CategoryID);
}

function IPS_GetCategoryList()
{
    return IPS\Kernel::getCategoryList();
}

function IPS_GetCategoryIDByName(string $Name, int $ParentID)
{
    return IPS\Kernel::getCategoryIDByName($Name, $ParentID);
}

/* Instance Manager */
function IPS_CreateInstance(string $ModuleID)
{
    $module = IPS\Kernel::getModule($ModuleID);
    $id = IPS\Kernel::registerObject(0 /* Category */);
    IPS\Kernel::createInstance($id, $module);

    return $id;
}

function IPS_DeleteInstance(int $InstanceID)
{
    IPS\Kernel::deleteInstance($InstanceID);
    IPS\Kernel::unregisterObject($InstanceID);
}

function IPS_InstanceExists(int $InstanceID)
{
    return IPS\Kernel::instanceExists($InstanceID);
}

function IPS_GetInstance(int $InstanceID)
{
    return IPS\Kernel::getInstance($InstanceID);
}

function IPS_GetInstanceList()
{
    return IPS\Kernel::getInstanceList();
}

function IPS_GetInstanceIDByName(string $Name, int $ParentID)
{
    return IPS\Kernel::getObjectIDByNameEx($Name, $ParentID, 1 /* Instance */);
}

function IPS_GetInstanceListByModuleType(int $ModuleType)
{
    return IPS\Kernel::getInstanceListByModuleType($ModuleType);
}

function IPS_GetInstanceListByModuleID(string $ModuleID)
{
    return IPS\Kernel::getInstanceListByModuleID($ModuleID);
}

/* Instance Manager - Configuration */
function IPS_HasChanges(int $InstanceID)
{
    return IPS\Kernel::getInstanceInterface($InstanceID)->HasChanges();
}

function IPS_ResetChanges(int $InstanceID)
{
    IPS\Kernel::getInstanceInterface($InstanceID)->ResetChanges();
}

function IPS_ApplyChanges(int $InstanceID)
{
    IPS\Kernel::getInstanceInterface($InstanceID)->ApplyChanges();
}

function IPS_GetProperty(int $InstanceID, string $Name)
{
    return IPS\Kernel::getInstanceInterface($InstanceID)->GetProperty($Name);
}

function IPS_GetConfiguration(int $InstanceID)
{
    return IPS\Kernel::getInstanceInterface($InstanceID)->GetConfiguration();
}

function IPS_GetConfigurationForParent(int $InstanceID)
{
    return IPS\Kernel::getInstanceInterface($InstanceID)->GetConfigurationForParent();
}

function IPS_GetConfigurationForm(int $InstanceID)
{
    return IPS\Kernel::getInstanceInterface($InstanceID)->GetConfigurationForm();
}

function IPS_SetProperty(int $InstanceID, string $Name, $Value)
{
    IPS\Kernel::getInstanceInterface($InstanceID)->SetProperty($Name, $Value);
}

function IPS_SetConfiguration(int $InstanceID, string $Configuration)
{
    IPS\Kernel::getInstanceInterface($InstanceID)->SetConfiguration($Configuration);
}

/* Instance Manager - Connections */
function IPS_ConnectInstance(int $InstanceID, int $ParentID)
{
    IPS\Kernel::connectInstance($InstanceID, $ParentID);
}

function IPS_DisconnectInstance(int $InstanceID)
{
    IPS\Kernel::disconnectInstance($InstanceID);
}

/* Instance Manager - Searching */
function IPS_StartSearch(int $InstanceID)
{
    throw new Exception('Not implemented');
}

function IPS_StopSearch(int $InstanceID)
{
    throw new Exception('Not implemented');
}

function IPS_SupportsSearching(int $InstanceID)
{
    throw new Exception('Not implemented');
}

function IPS_IsSearching(int $InstanceID)
{
    throw new Exception('Not implemented');
}

/* Instance Manager - Debugging */
function IPS_DisableDebug(int $ID)
{
    IPS\Kernel::disableDebug($ID);
}

function IPS_EnableDebug(int $ID, int $Duration)
{
    IPS\Kernel::enableDebug($ID, $Duration);
}

function IPS_SendDebug(int $SenderID, string $Message, string $Data, int $Format)
{
    IPS\Kernel::sendDebug($SenderID, $Message, $Data, $Format);
}

/* Instance Manager - Actions */
function IPS_RequestAction(int $InstanceID, string $VariableIdent, $Value)
{
    throw new Exception('Not implemented');
}

/* Variable Manager */
function IPS_CreateVariable(int $VariableType)
{
    return 0;
}

function IPS_DeleteVariable(int $VariableID)
{
    return true;
}

function IPS_VariableExists(int $VariableID)
{
    return true;
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

function IPS_SetVariableCustomAction(int $VariableID, int $ScriptID)
{
    return true;
}

function IPS_SetVariableCustomProfile(int $VariableID, string $ProfileName)
{
    return true;
}

/* Script Manager */
function IPS_CreateScript(int $ScriptType)
{
    return 0;
}

function IPS_DeleteScript(int $ScriptID, bool $DeleteFile)
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

/* Event Manager */
function IPS_CreateEvent(int $EventType)
{
    return 0;
}

function IPS_DeleteEvent(int $EventID)
{
    return true;
}

function IPS_EventExists(int $EventID)
{
    return true;
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

function IPS_GetScriptTimer(int $ScriptID)
{
    return 0;
}

function IPS_SetScriptTimer(int $ScriptID, int $Interval)
{
    return true;
}

/* Media Manager */
function IPS_CreateMedia(int $MediaType)
{
    return 0;
}

function IPS_DeleteMedia(int $MediaID, bool $DeleteFile)
{
    return true;
}

function IPS_MediaExists(int $MediaID)
{
    return false;
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

function IPS_SendMediaEvent(int $MediaID)
{
    return true;
}

/* Link Manager */
function IPS_CreateLink()
{
    return 0;
}

function IPS_DeleteLink(int $LinkID)
{
    return true;
}

function IPS_LinkExists(int $LinkID)
{
    return false;
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

function IPS_SetLinkTargetID(int $LinkID, int $ChildID)
{
    return true;
}

/* Profile Manager */
function IPS_CreateVariableProfile(string $ProfileName, int $ProfileType)
{
    return true;
}

function IPS_DeleteVariableProfile(string $ProfileName)
{
    return true;
}

function IPS_VariableProfileExists(string $ProfileName)
{
    return true;
}

function IPS_GetVariableProfile(string $ProfileName)
{
    return [];
}

function IPS_GetVariableProfileList()
{
    return false;
}

function IPS_GetVariableProfileListByType(int $ProfileType)
{
    return [];
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

/* Kernel */
function IPS_GetKernelDate()
{
    return time();
}

function IPS_GetKernelDir()
{
    return sys_get_temp_dir();
}

function IPS_GetKernelDirEx()
{
    return sys_get_temp_dir();
}

function IPS_GetKernelRunlevel()
{
    return 10103 /* KR_READY */;
}

function IPS_GetKernelStartTime()
{
    return time();
}

function IPS_GetKernelVersion()
{
    return '4.3';
}

function IPS_GetLogDir()
{
    return sys_get_temp_dir() . '/logs';
}

function IPS_LogMessage(string $Sender, string $Message)
{
    return true;
}

/* License Pool */
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

function IPS_GetDemoExpiration()
{
    return 0;
}

function IPS_GetLiveConsoleCRC()
{
    throw new Exception('Not implemented');
}

function IPS_GetLiveConsoleFile()
{
    throw new Exception('Not implemented');
}

function IPS_GetLiveDashboardCRC()
{
    throw new Exception('Not implemented');
}

function IPS_GetLiveDashboardFile()
{
    throw new Exception('Not implemented');
}

function IPS_GetLiveUpdateVersion()
{
    throw new Exception('Not implemented');
}

function IPS_SetLicense(string $Licensee, string $LicenseContent)
{
    throw new Exception('Not implemented');
}

/* Script Engine */
function IPS_RunScript(int $ScriptID)
{
    throw new Exception('Not implemented');
}

function IPS_RunScriptEx(int $ScriptID, array $Parameters)
{
    throw new Exception('Not implemented');
}

function IPS_RunScriptText(string $ScriptText)
{
    throw new Exception('Not implemented');
}

function IPS_RunScriptTextEx(string $ScriptText, array $Parameters)
{
    throw new Exception('Not implemented');
}

function IPS_RunScriptTextWait(string $ScriptText)
{
    throw new Exception('Not implemented');
}

function IPS_RunScriptTextWaitEx(string $ScriptText, array $Parameters)
{
    throw new Exception('Not implemented');
}

function IPS_RunScriptWait(int $ScriptID)
{
    throw new Exception('Not implemented');
}

function IPS_RunScriptWaitEx(int $ScriptID, array $Parameters)
{
    throw new Exception('Not implemented');
}

function IPS_ScriptExists(int $ScriptID)
{
    throw new Exception('Not implemented');
}

function IPS_SemaphoreEnter(string $Name, int $Milliseconds)
{
    throw new Exception('Not implemented');
}

function IPS_SemaphoreLeave(string $Name)
{
    throw new Exception('Not implemented');
}

function IPS_ScriptThreadExists(int $ThreadID)
{
    throw new Exception('Not implemented');
}

function IPS_GetScriptThread(int $ThreadID)
{
    throw new Exception('Not implemented');
}

function IPS_GetScriptThreadList()
{
    throw new Exception('Not implemented');
}

function IPS_GetScriptThreads(array $Parameter)
{
    throw new Exception('Not implemented');
}

function IPS_FunctionExists(string $FunctionName)
{
    throw new Exception('Not implemented');
}

function IPS_GetFunction(string $FunctionName)
{
    throw new Exception('Not implemented');
}

function IPS_GetFunctionList(int $InstanceID)
{
    throw new Exception('Not implemented');
}

function IPS_GetFunctionListByModuleID(string $ModuleID)
{
    throw new Exception('Not implemented');
}

function IPS_GetFunctions(array $Parameter)
{
    throw new Exception('Not implemented');
}

function IPS_GetFunctionsMap(array $Parameter)
{
    throw new Exception('Not implemented');
}

/* Timer Pool */
function IPS_TimerExists(int $TimerID)
{
    throw new Exception('Not implemented');
}

function IPS_GetTimer(int $TimerID)
{
    throw new Exception('Not implemented');
}

function IPS_GetTimerList()
{
    throw new Exception('Not implemented');
}

function IPS_GetTimers(array $Parameter)
{
    throw new Exception('Not implemented');
}

/* Module Loader */
function IPS_LibraryExists(string $LibraryID)
{
    return IPS\Kernel::libraryExists($LibraryID);
}

function IPS_GetLibrary(string $LibraryID)
{
    return IPS\Kernel::getLibrary($LibraryID);
}

function IPS_GetLibraryList()
{
    return IPS\Kernel::getLibraryList();
}

function IPS_GetLibraryModules(string $LibraryID)
{
    return IPS\Kernel::getLibraryModules($LibraryID);
}

function IPS_ModuleExists(string $ModuleID)
{
    return IPS\Kernel::moduleExists($ModuleID);
}

function IPS_GetModule(string $ModuleID)
{
    return IPS\Kernel::getModule($ModuleID);
}

function IPS_GetModuleList()
{
    return IPS\Kernel::getModuleList();
}

function IPS_GetModuleListByType(int $ModuleType)
{
    return IPS\Kernel::getModuleListByType($ModuleType);
}

function IPS_IsModuleCompatible(string $ModuleID, string $ParentModuleID)
{
    throw new Exception('Not implemented');
}

function IPS_GetCompatibleModules(string $ModuleID)
{
    throw new Exception('Not implemented');
}

function IPS_IsInstanceCompatible(int $InstanceID, int $ParentInstanceID)
{
    throw new Exception('Not implemented');
}

function IPS_GetCompatibleInstances(int $InstanceID)
{
    throw new Exception('Not implemented');
}

/* Module Loader - Helper */
function IPS_GetModules(array $Parameter)
{
    if (count($Parameter) == 0) {
        $Parameter = IPS_GetModuleList();
    }
    $result = [];
    foreach ($Parameter as $ModuleID) {
        $result[] = IPS_GetModule($ModuleID);
    }
    return $result;
}

function IPS_GetLibraries(array $Parameter)
{
    if (count($Parameter) == 0) {
        $Parameter = IPS_GetLibraryList();
    }
    $result = [];
    foreach ($Parameter as $LibraryID) {
        $result[] = IPS_GetLibrary($LibraryID);
    }
    return $result;
}

/* Settings */
function IPS_GetOption(string $Option)
{
    throw new Exception('Not implemented');
}

function IPS_GetSecurityMode()
{
    throw new Exception('Not implemented');
}

function IPS_GetSnapshot()
{
    throw new Exception('Not implemented');
}

function IPS_GetSnapshotChanges(int $LastTimestamp)
{
    throw new Exception('Not implemented');
}

function IPS_SetOption(string $Option, int $Value)
{
    throw new Exception('Not implemented');
}

function IPS_SetSecurity(int $Mode, string $Password)
{
    throw new Exception('Not implemented');
}

/* Additional */
function IPS_Execute(string $Filename, string $Parameter, bool $ShowWindow, bool $WaitResult)
{
    throw new Exception('Not implemented');
}

function IPS_ExecuteEx(string $Filename, string $Parameter, bool $ShowWindow, bool $WaitResult, int $SessionID)
{
    throw new Exception('Not implemented');
}

function IPS_Sleep(int $Milliseconds)
{
    usleep($Milliseconds * 1000);
}
