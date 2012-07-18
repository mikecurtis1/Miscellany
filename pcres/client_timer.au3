#include <GUIConstantsEx.au3>
#include <StaticConstants.au3>
#include <WindowsConstants.au3>

Opt("TrayIconHide", 1) ; 1=hide, 0=show

$defaultusername = RegRead("HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Winlogon", "DefaultUserName")
$username = EnvGet("USERNAME")
$computername = EnvGet("COMPUTERNAME")
$url = "http://localhost/pcres/client_manager.php?defaultusername=" & $defaultusername & "&username=" & $username & "&computername=" & $computername

$sec_remaining = ClientCheck($url)
$time = CalcTimer($sec_remaining)

Opt("GUIOnEventMode", 1)  ; Change to OnEvent mode 
$mainwindow = GUICreate("Timer: "&$defaultusername, 165, 58, (@DesktopWidth)/2, (@DesktopHeight)/2, BitOr($WS_OVERLAPPED,$WS_SYSMENU,$WS_THICKFRAME), $WS_EX_TOPMOST)
$Label_1 = GUICtrlCreateLabel($time, 2, 2, 50, 25)
GUICtrlSetFont(-1,14,500)
$extendButton = GUICtrlCreateButton("Extend Time", 87, 2, 74, 25)

GUISetOnEvent($GUI_EVENT_CLOSE, "CLOSEClicked")
GUICtrlSetOnEvent($extendButton, "extendButton")
GUISetState(@SW_SHOW)

While 1
  $time = CalcTimer($sec_remaining)
  GUICtrlSetData($Label_1, $time)
  Sleep(1000)  ; Idle around
  $sec_remaining -= 1
  If $sec_remaining < 121 And Mod($sec_remaining, 20) = 0 Then
	 MsgBox(0,"Warning","You have less than two minutes remaining on your computer reservation: " & $sec_remaining, 8)
  EndIf
WEnd

Func extendButton()
   $extension_url = "http://localhost/pcres/extension_manager.php?defaultusername=" & $defaultusername
   $sData = InetRead($extension_url,1)
   $extension_response = BinaryToString($sData)
   $sec_remaining = ClientCheck($url)
   MsgBox(0,"Extension Response",$extension_response)
EndFunc

Func CLOSEClicked()
  Exit
EndFunc

Func ClientCheck ($url)
   Dim $response = ""
   Dim $status = ""
   Dim $sec_remaining = "" 
   Dim $clock_now = ""
   Dim $clock_stop = ""
   Dim $clock_remaining = ""
   Local $sData = InetRead($url,1) ; requires IE, 1 forces reload from remote site without using local cache
   $response = BinaryToString($sData)
   $data = StringSplit($response, "|")
   If $data[0] == 5 Then
	  $status = $data[1]
	  $sec_remaining = $data[2] 
	  $clock_now = $data[3]
	  $clock_stop = $data[4]
	  $clock_remaining = $data[5]
   Else
	  MsgBox(0,"Error", "Did not find 5 datum in reponse: " & @LF & $response)
   EndIf  
   return $sec_remaining
EndFunc

Func CalcTimer($sec_remaining)
   If $sec_remaining > 0 Then
	  $minutes = Int($sec_remaining / 60)
	  $seconds = Mod($sec_remaining, 60)
   Else
	  $minutes = 0
	  $seconds = 0
   EndIf
   $time = StringFormat("%02d", $minutes) & ":" & StringFormat("%02d", $seconds)
   return $time
EndFunc
