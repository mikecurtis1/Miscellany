Set WshShell = WScript.CreateObject("WScript.Shell")

dim myPath
myPath = createobject("wscript.shell").currentdirectory ' ex. C:\kiosk or E:\kiosk

'option to autorun on-screen keyboard
'WshShell.Run "OSK"

'the exit kiosk: ALT+F4
WshShell.run myPath & "\GoogleChromePortable\GoogleChromePortable.exe --kiosk file:///" & myPath & "/app/html/video.html"
