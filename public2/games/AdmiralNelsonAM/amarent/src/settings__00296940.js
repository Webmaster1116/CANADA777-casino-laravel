    
    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
	var serverString='';

    var XmlHttpRequest = new XMLHttpRequest();
    XmlHttpRequest.overrideMimeType("application/json");
    XmlHttpRequest.open('GET', '/socket_config.json', false);
    XmlHttpRequest.onreadystatechange = function ()
    {
        if (XmlHttpRequest.readyState == 4 && XmlHttpRequest.status == "200")
        {
            var serverConfig = JSON.parse(XmlHttpRequest.responseText);
            serverString=serverConfig.prefix_ws+serverConfig.host_ws+':'+serverConfig.port;
          
        }
    }
    XmlHttpRequest.send(null);



function Settings()
{
	this.value6 = serverString;
	this.value5 = ["en","de","es","fr","pt","ru","ka","ro","tr","et","el","bg","hu","cs","hr","it","sv"];
	this.value9 = true;
	this.value10 = 3000;
	this.value11 = false;
	this.value49 = true;
	this.value27 = true;
	this.value28 = false;
	this.value12 = false;//history button
	this.value81 = {g:false,p:false};
	this.value50 = {g:true,h:false};
	this.value51 = {d:true,m:true};
	this.value13 = true;
	this.value20 = 20;
	this.value36 = false;
	this.value37 = false;
	this.value39 = 0;
	this.value14 = 14;
	this.value15 = 4;
	this.value25 = 13;
	this.value32 = 5; 
	this.value33 = 14;
	this.value73 = {w:960,h:480};
	this.value19 = false;
	this.value21 = {e:true,s:null,m:0.01,ts:null,cs:null};
	this.value72 = {EUR:{s:"EUR",m:0.01,ts:null,cs:"."},GBP:{s:"GBP",m:0.01,ts:null,cs:"."},USD:{s:"USD",m:0.01,ts:null,cs:"."},AUD:{s:"AUD",m:0.01,ts:null,cs:"."},CAD:{s:"CAD",m:0.01,ts:null,cs:"."},NZD:{s:"NZD",m:0.01,ts:null,cs:"."},NOK:{s:"NOK",m:0.01,ts:null,cs:"."},SEK:{s:"SEK",m:0.01,ts:null,cs:"."},ZAR:{s:"ZAR",m:0.01,ts:null,cs:"."},INR:{s:"INR",m:1,ts:null,cs:null},RUB:{s:"RUB",m:0.01,ts:null,cs:"."},CHF:{s:"CHF",m:0.01,ts:null,cs:"."},HRK:{s:"HRK",m:0.01,ts:null,cs:"."},HUF:{s:"HUF",m:1,ts:null,cs:null},GEL:{s:"GEL",m:0.01,ts:null,cs:"."},UAH:{s:"UAH",m:0.01,ts:null,cs:"."},RON:{s:"RON",m:0.01,ts:null,cs:"."},BRL:{s:"BRL",m:0.01,ts:null,cs:"."},MYR:{s:"MYR",m:0.01,ts:null,cs:"."},CNY:{s:"CNY",m:0.01,ts:null,cs:"."},JPY:{s:"JPY",m:0.01,ts:null,cs:"."},KRW:{s:"KRW",m:0.01,ts:null,cs:"."},IDR:{s:"IDR",m:0.01,ts:null,cs:"."},TND:{s:"TND",m:0.01,ts:null,cs:"."},VND:{s:"VND",m:0.01,ts:null,cs:"."},THB:{s:"THB",m:0.01,ts:null,cs:"."}};
	this.value30 = "..";
	this.value40 = 1.0;
	this.value38 = {w:true,l:true,m:true,i:true,a:false};
	this.value43 = false;
	this.value44 = {d:false,e:false};
	this.value45 = 4;
	this.value46 = 5;
	this.value47 = 10000;
	this.value71 = 1000;
	this.value55 = false;
	this.value57 = "modern";
	this.value62 = true;
	this.value64 = false;
	this.value65 = 0;
	this.value66 = 1000;
	this.value67 = 0;
	this.value68 = "fun";
	this.value69 = true;
	this.value74 = null;
	this.value75 = null;
	this.value76 = null;
	this.value78 = {t:true,b:true,d:3000};
	this.value79 = 0;
	this.value82 = false;
	this.value83 = true;
	this.value86 = false;
	this.value8 = '../../../../';//exit url

	this.bfh = {value2:10};
	this.ges = {value2:10};
	this.bpa = {value2:10};
	this.apx = {value3:10};
	this.dof = {value2:10};
	this.dpe = {value2:10};
	this.vam = {value2:10};
	this.wsh = {value2:10};
	this.dim = {value2:10};
	this.ffr = {value2:10};
	this.ljo = {value2:10};
	this.mow = {value2:10};
	this.bef = {value2:10};
	this.bil = {value2:10};
	this.tbi = {value2:10};
	this.wir = {value3:10};
	this.wom = {value2:10};
	this.can = {value2:10};
	this.csn = {value2:10};
	this.dic = {value2:10};
	this.fic = {value2:10};
	this.hot = {value2:10};
	this.lbe = {value2:10};
	this.lga = {value2:10};
	this.mfr = {value2:10};
	this.run = {value2:10};
	this.dki = {value2:10};
	this.rec = {value2:10};
	this.maf = {value2:10};	
	this.sct = {value6:{g:[10,20,50,100,200,500],v:100,m:null,r:false,l:false,o:false}};
}

var webAudioEngine = null;
