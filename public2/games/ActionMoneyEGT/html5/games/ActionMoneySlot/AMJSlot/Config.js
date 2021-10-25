function Config()
{
    this.reelWidth = 172 ;
    this.reelHeight = 516;
    this.reelSpacing = 17;

    this.numImages = 11;
    this.numReels = 5;
    this.numReelCards = 3;
    this.wildIndex = 8;

    this.linesCount = [1, 5, 10, 15, 20];
    this.hasFreespins = true;

    this.coinAnimationCoef = 20;

    this.linesSelectActiveColor = 0xffff00;
    this.linesSelectInactiveColor = 0xffffff;
    this.linesSelectActiveGlowColor = 0xbf0000;
    this.linesSelectInactiveGlowColor = 0x14120d;

    this.gameNumberTimeHelpColor = 0xded7ff;
    this.labelsColor = 0x414cc8;

    this.paytableExitColor = 0x70826;

    this.toolTipMainTextColor = 0xFFFFFF;
    this.toolTipWinAmountTextColor = 0xFFFFFF;

    this.toolTipDateTextColor = 0xbfbfbf;

    this.toolTipUsernameTextColor = 0x414cc8;
    this.toolTipCurrencyTextColor =0x414cc8;
    this.toolTipNumberOfWinnersTextColor = 0x414cc8;

    this.toolTipDateSeparator = "/";

    this.paytablePageCount = 5;
    this.paytableGamblePage = 2;

    this.scatterConfig  = [
        {index: 9, minCount: 3, bonusMinCount: 3,bonusScatterSound: "winScatterBonus",
            validReels: [ true, true, true, true, true ], bonusVideoIndex: 9,
            stopSounds:["stopBonusSound", "stopBonusSound", "stopBonusSound", "stopBonusSound", "stopBonusSound"]},
        {index: 10, minCount: 3, freespinMinCount: 3,
            validReels: [ false, false, true, true, true ], freespinVideoIndex: 10, freespinSound: "winScatterFs",
            stopSounds:[null, null, "stopScatterSound3", "stopScatterSound4", "stopScatterSound3"]}
    ];

    this.reelVideos = [
        {src:["images/videos/00-0.json"], fps: 20},
        {src:["images/videos/01-0.json"], fps: 20},
        {src:["images/videos/02-0.json", "images/videos/02-1.json"], fps: 20},
        {src:["images/videos/03-0.json", "images/videos/03-1.json"], fps: 18},
        {src:["images/videos/04-0.json"], fps: 20},
        {src:["images/videos/05-0.json", "images/videos/05-1.json","images/videos/05-2.json"], fps: 20},
        {src:["images/videos/06-0.json", "images/videos/06-1.json"], fps: 20},
        {src:["images/videos/07-0.json","images/videos/07-1.json"], fps: 20},
        {src:["images/videos/08-0.json", "images/videos/08-1.json", "images/videos/08-2.json"], fps: 20},
        {src:["images/videos/09-0.json"], fps: 20},
        {src:["images/videos/10-0.json", "images/videos/10-1.json"], fps: 20},
        null,
        null,
        null,
        null,
        null,
        {src:["images/videos/16-0.json","images/videos/16-1.json"], fps: 10},
        {src:["images/videos/17-0.json","images/videos/17-1.json"], fps: 10},
        {src:["images/videos/18-0.json","images/videos/18-1.json"], fps: 10},
    ];


    this.reelImages = ["reelImages.json"];

    this.linesCoords = [
        {coords:[184,320, 1095,320], color:0xfff221},
        {coords:[184,150, 1095,150], color:0xf9af0c},
        {coords:[184,497, 1095,497], color:0x17e614},
        {coords:[184,157, 259,157, 640,507, 1018,157, 1095,157], color:0xdf3d3d},
        {coords:[184,506, 259,506, 640,140, 1018,506, 1095,506], color:0xc90404},
        {coords:[184,143, 444,143, 833,488, 1095,488], color:0x60bad},
        {coords:[184,488, 457,488, 838,143, 1095,143], color:0x1189ba},
        {coords:[184,336, 259,336, 442,505, 850,505, 1018,336, 1095,336], color:0xc86b07},
        {coords:[184,305, 259,305, 462,141, 824,141, 1011,305, 1095,305], color:0x3bb108},
        {coords:[184,134, 245,134, 449,327, 829,327, 1039,134, 1095,134], color:0x4a82f3},
        {coords:[184,479, 280,479, 451,311, 825,311, 997,479, 1095,479], color:0xce0089},
        {coords:[184,343, 276,343, 440,497, 831,141, 1060,343, 1095,343], color:0xce52a1},
        {coords:[184,297, 262,297, 452,143, 851,497, 1049,297, 1095,297], color:0xaf2dc5},
        {coords:[184,164, 284,164, 447,318, 640,134, 830,320, 1000,164, 1095,164], color:0x3a9e26},
        {coords:[184,514, 236,514, 442,312, 640,497, 838,315, 1043,514, 1095,514], color:0x227ab3},
        {coords:[184,329, 466,329, 640,490, 816,329, 1095,329], color:0x6e0cb3},
        {coords:[184,311, 447,311, 640,129, 826,311, 1095,311], color:0xce00ba},
        {coords:[184,172, 264,172, 439,499, 640,137, 850,497, 1012,172, 1095,172], color:0xcba303},
        {coords:[184,471, 268,471, 451,141, 640,498, 831,141, 1009,471, 1095,471], color:0xbc3500},
        {coords:[184,289, 265,289, 453,137, 640,489, 831,138, 1002,289, 1095,289], color:0xa2a2a2}
    ];

    this.fullLineSounds = [
        {card:0, name:"fullLine1"}, {card:1, name:"fullLine1"},
        {card:2, name:"fullLine1"}, {card:3, name:"fullLine2"},
        {card:4, name:"fullLine2"}, {card:5, name:"fullLine2"},
        {card:6, name:"fullLine3"}, {card:7, name:"fullLine3"},
        {card:8, name:"fullLine4"}, {card:9, name:"fullLine4"}

    ];

    this.gameSounds = [
        {
            src:  "shortSounds.mp3", sounds:[
            {name: "reelAccelerateSound", 		    start:9, 	duration: 2.04},
            {name: "stopScatterSound3", 			start:12, 	duration: 0.862},
            {name: "stopScatterSound4", 			start:13, 	duration: 0.78},
            {name: "stopScatterSound5", 			start:14, 	duration: 0.764},
            {name: "doorSound", 			        start:8, 	duration: 0.724},
            {name: "winScatterFs", 			        start:4, 	duration: 3.92},
            {name: "suitcaseSound", 			    start:2, 	duration:0.8},
            {name: "stopBonusSound", 			    start:1, 	duration:0.361}
            ]
        },
        {
            src: "winSounds.mp3", sounds:[
            {name: "win0", 					    start:0, 	duration: 4.25},
            {name: "win1", 						start:5, 	duration: 3.145},
            {name: "win2", 				        start:9, 	duration: 5.14},
            {name: "win3", 					    start:15, 	duration: 4.762},
            {name: "win4", 						start:21, 	duration: 9.337},
            {name: "win5", 						start:31, 	duration: 8.25},
            {name: "win6", 					    start:40, 	duration: 4.065},
            {name: "win7", 					    start:45, 	duration: 4.68},
            {name: "win8", 					    start:69, 	duration: 4.5},
            {name: "winScatterBonus", 	        start:52, 	duration: 4.923},
            {name: "creditAnimationSound", 		start:58, 	duration: 10}
        ]
        }];

    this.freespinSounds = [
        {
            src: "freespinSounds.mp3",
            id: "freespinSounds",
            sounds:[
                {name: "pressStartSound", 				start:0, 	duration: 8.032},
                {name: "retriggerSound", 				start:18, 	duration: 11.73},
                {name: "outroSound", 					start:9, 	duration: 8.093},
                {name: "introSound", 					start:36, 	duration: 5.56},
                {name: "buildingsAppearSound", 		    start:30, 	duration: 0.42},
                {name: "explosionSound", 		        start:31, 	duration: 1.24},
                {name: "multiplierSound", 			    start:33, 	duration: 1.62},
                {name: "envelopeSound", 			    start:35, 	duration: 0.488},
                {name: "bankFonSound", 			        start:43, 	duration: 11.74},
                {name: "USSound", 			            start:55, 	duration: 8.13},
                {name: "EGSound", 			            start:64, 	duration: 13.33},
                {name: "JPSound", 			            start:89, 	duration: 9},
                {name: "RUSound", 			            start:99, 	duration: 8.05},
                {name: "FRSound", 			            start:78, 	duration: 10.3},
                {name: "scatterFonSound", 			    start:108, 	duration: 6.8},
                {name: "scatterSelectSound", 			start:115, 	duration: 1.3},
                {name: "scatterShowDigitSound", 	    start:117, 	duration: 3.1},
                {name: "scatterWaitSelectSound", 	    start:121, 	duration: 9.46}
            ]
        }];
    //'bg', 'ru', 'mk', 'fr', 'nl', 'es','ro'
    this.helpLanguages = ["en", "bg", "ro", "es", "pt"];
    this.paytableLanguages = ['en', 'bg', 'ru', 'mk', 'fr', 'nl', 'es', 'ro','pt'];
    this.freespinLanguages = ['en', 'bg', 'es', 'fr', 'mk', 'nl', 'ro', 'ru','pt'];
    this.bonusLanguages = ['en', 'bg', 'es', 'fr', 'mk', 'nl', 'ro', 'ru', 'pt'];
}

com.egt.baseslot.Config = Config;
com.egt.cascadeslot.buildTime = 1561035489401;