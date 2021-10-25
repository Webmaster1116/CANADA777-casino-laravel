$.Class("PlatformSettings", 
{
	socketType: "wss", // for demo and devel
	// socketType: "ws",//java tcpHost
	language: "en",
	translations: 
	{
		session_override:
		{
			en: "Disconnected due to multiple logins!",
			bg: "Поради множество влизания връзката беше прекъсната!",
			ru: "Соединение разорвано из-за нескольких логинов!",
			nl: "Verbinding verbroken door meerdere aanmeldingen!",
			fr: "Déconnecté en raison de connexions multiples!",
			mk: "Исклучено поради повеќе најави!",
			ro: "Deconectare din cauza logărilor multiple!",
			es: "¡Desconectado debido a múltiples inicios de sesión!",
			pt: "Desligado devido a múltiplos inícios de sessão.",
			it: "Disconnesso a causa di login multipli!",
			da: "Frakoblet grundet flere logins!",
			hu: "Szétcsatlakoztatva többszörös bejelentkezés miatt!",
			sv: "Utloggad på grund av inloggning från flera platser!",
			de: "Verbindung aufgrund mehrerer Anmeldungen getrennt!"
		},
		session_timeout:
		{
			en: "Your session has timed out due to inactivity.",
			bg: "Сесията Ви бе прекратена поради неактивност.",
			ru: "Ваша сессия была уничтожена из-за неактивности.",
			nl: "Er is een time-out van uw sessie opgetreden omwille van inactiviteit.",
			fr: "Votre session a expiré pour cause d'inactivité.",
			mk: "Вашата сесија истече поради неактивност.",
			ro: "Sesiunea dvs. a expirat din cauza inactivității.",
			es: "Su sesiń ha expirado debido a su inactividad.",
            pt: "A sua sessão foi encerrada por exceder o tempo sem atividade.",
            it: "La sua sessione è stata terminata per inattività.",
            da: "Din session er løbet ud grundet inaktivitet.",
			hu: "Az ön játékmenete lejárt inaktivitás miatti időtúllépés miatt.",
			sv: "Din session har avslutats på grund av inaktivitet.",
			de: "Ihre Sitzung wurde aufgrund von Inaktivität beendet."
		},
		connection_closed:
		{
            en: "The connection to the server has been lost.</br>Please close the window and try again.",
            bg: "Изгубена е връзка със сървъра.</br>Моля, затворете прозореца и опитайте отново.",
            ru: "Не удалось подключится к серверу.</br>Пожалуйста, зокройте окно и попробуйте снова.",
            mk: "Изгубена е врската со серверот.</br>Ве молиме затворете го прозорецот и пробајте повторно.",
            pt: "A ligação ao servidor perdeu-se.</br>Feche a janela e tente novamente.",
			ro: "Conexiunea cu serverul a fost pierdută.</br>Vă rugăm, închideți fereastra și încercați din nou.",
            hu: "A kapcsolat a szerverrel megszakadt.</br>Kérjük, zárja be az ablakot és próbálja meg újból."
		},
		maintenance:
		{
            en: "System Maintenance</br></br> We are currently offline due to server maintenance. Our team sincerely apologizes for the inconvenience. Thank you for your patience.",
            bg: "Профилактика на системата</br></br> В момента системата е офлайн заради профилактика. Екипът ни се извинява за причиненото неудобство. Благодарим за търпението.",
            ru: "Профилактические работы на системе</br></br> В настоящее время система находится оффлайн из-за профилактики. Наша комманда приносит свои извинения за временные неудобства. Спасибо Вам за терпение.",
            nl: "Systeemonderhoud</br></br> We zijn momenteel offline omwille van een serveronderhoud. Ons team biedt zijn excuses aan voor dit ongemak. Bedankt voor uw geduld.",
            fr: "Maintenance du système</br></br> Nous sommes actuellement hors-ligne en raison de la maintenance du serveur. Notre équipe présente ses sincères excuses pour la gêne occasionnée. Merci pour votre patience.",
            mk: "Систем за одржување</br></br> Моментално сме без линија поради одржување на сервер. Нашиот тим искрено се извинува поради непријатностите. Ви благодариме поради стрпливоста.",
            pt: "Manutenção de Sistema</br></br> Estamos offline de momento devido a manutenção do servidor. A nossa equipa pede desculpa pelo inconveniente. Obrigado pela sua paciência.",
            es: "Sistema en Mantenimiento</br></br>Actualmente estamos offline debido al mantenimiento del servidor. Nuestro equipo lamenta profundamente los inconvenientes. Gracias por su paciencia.",
            ro: "Întreținere sistem</br></br>Pentru moment suntem deconectați datorită lucrărilor de mentenanță la server. Echipa noastră își cere sincer scuze pentru neplăcerile create. Vă mulțumim pentru răbdare.",
            it: "Manutenzione Sistema</br></br>Siamo attualmente offline per manutenzione al server. Il nostro team si scusa per il disagio. Grazie per la tua pazienza.",
            da: "Systemvedligeholdelse</br></br>Vi er lige nu offline grundet servervedligeholdelse. Vores hold beklager ulejligheden. Tak for din tålmodighed.",
            hu: "Rendszerkarbantartás</br></br>Szerverkarbantartás miatt jelenleg szolgáltatásunk nem érhető el. Csapatunk elnézését kéri a kellemetlenségért.<br>Köszönjük a türelmét.",
			sv: "Systemunderhåll</br></br>Vi är för närvarande offline på grund av systemunderhåll. Vi ber om ursäkt om det skapar några problem för dig.<br>Vi tackar för din förståelse.",
			de: "Systemwartung</br></br>Aufgrund einer Serverwartung sind wir derzeit offline. Unser Team entschuldigt sich aufrichtig für die Unannehmlichkeit.<br>Vielen Dank für Ihre Geduld.",

        },
		shutdown_title:
		{
			en: "Maintenance notice",
			bg: "Известие за поддръжка",
			ru: "Уведомление о переходе в режим обслуживания",
			nl: "Onderhoudsmededeling",
			fr: "Avis de maintenance",
			mk: "Забелешка околу одржувањето",
			pt: "Aviso de manutenção",
			es: "Aviso de mantenimiento",
			ro: "Notificare mentenanță",
			it: "Avviso manutenzione",
			da: "Bemærkning om vedligeholdelse",
			hu: "Karbantartási értesítés",
			sv: "Underhållsnotis",
			de: "Wartungshinweis"
		},
		shutdown_message:
		{
			en: "Our game server will be entering a maintenance mode. Please finish your current game session and log out. Be aware that no wagers will be accepted during the maintenance period. Our team sincerely apologize for the inconvenience. Thank you for your patience.",
			bg: "Игралният ни сървър ще влезе в режим на поддръжка. Моля, завършете активната си сесия и излезте от системата. Имайте в предвид, че няма да бъдат приемани залози по време на поддръжката. Искренно Ви се извиняваме за причиненото неудобство. Благодарим Ви за търпението.",
			ru: "Игровой сервер переходит в режим обслуживания. Пожалуйста, завершите текущую игровую сессию и выйдите из системы. Во время проведения обслуживания ставки не принимаются. Мы приносим извинения за любые доставленные неудобства. Благодарим за понимание!",
			nl: "Onze spelserver gaat in de onderhoudsmodus. Beëindig uw huidige speelsessie en meld u af. Opgelet, tijdens de onderhoudsperiode worden er geen inzetten aanvaard. Ons team biedt zijn excuses aan voor dit ongemak. Bedankt voor uw geduld.",
			fr: "Notre serveur va rentrer en mode maintenance. Veuillez finir la session en cours et vous déconnecter. Vouillez noter que pendant la période de maintenance aucun pari ne sera pas accepté. Notre équipe vous présente nos sincères excuses pour la gêne occasionnée. Merci pour votre patience.",
			mk: "Нашиот сервер за игра ќе влезе во режим на одржување. Ве молиме да ја завршите вашата тековна сесија на игра и да се одјавите. Бидете свесни дека нема да биде прифатен никаков облог за време на периодот на одржување. Нашиот тим искрено се извинува поради непријатностите. Ви благодариме поради стрпливоста.",
			pt: "O nosso servidor de jogo vai entrar em modo de manutenção. Termine a sua sessão de jogo atual e desligue-se. Não serão aceites apostas durante o período de manutenção. A nossa equipa pede desculpa pelo inconveniente. Obrigado pela sua paciência.",
			es: "Nuestro servidor de juego entrará en modo de mantenimiento. Por favor, termine su sesión en el juego actual y salga. Tenga en cuenta que no se aceptarán apuestas durante el periodo de mantenimiento. Nuestro equipo lamenta profundamente las inconveniencias. Gracias por su paciencia.",
			ro: "Serverul nostru de joc va intra în modulul de mentenanță. Vă rugăm să terminați sesiunea curentă de joc și să vă delogați Atenție, nu vor fi acceptate pariurile plasate pe parcursul perioadei de mentenanță. Echipa noastră își cere sincer scuze pentru neplăcerile create. Vă mulțumim pentru răbdare.",
			it: "Il nostro server di gioco entrerà in modalità manutenzione. Termini la sua sessione di gioco corrente e si disconnetta. Attenzione, nessuna puntata sarà accettata durante il periodo di manutenzione. Il nostro team si scusa sinceramente per il disagio. Grazie per la sua pazienza.",
			da: "Vores spilleserver går i vedligeholdelsestilstand. Afslut din aktuelle spillesession og log ud. Bemærk, at ingen indsatser accepteres under vedligeholdelsesperioden. Vores hold beklager ulejligheden. Tak for din tålmodighed.",
            hu: "Játékszervereink karbantartási módba fognak lépni. Kérjük, fejezze be az aktuális játékmenetet és jelentkezzen ki. Kérjük, ne feledje, hogy a karbantartási időszak alatt új téteket nem fogad el a rendszer. Csapatunk elnézését kéri a kellemetlenségért. Köszönjük a türelmét.",
			sv: "Vår spelserver kommer behöva genomgå underhåll. Vänligen avsluta din nuvarande session och logga ut. Vänligen notera att inga insatser kommer godkännas under underhållsperioden. Vi ber om ursäkt om det skapar några problem för dig. Vi tackar för din förståelse.",
			de: "Unser Spiele-Server wechselt gleich in den Wartungsmodus. Bitte beenden Sie Ihre derzeitige Spielsitzung und melden Sie sich ab. Bitte beachten Sie, dass im Wartungszeitraum keine Einsätze angenommen werden. Unser Team entschuldigt sich aufrichtig für die Unannehmlichkeit. Vielen Dank für Ihre Geduld.",
		},
		reality_check_title:
		{
			en: "Responsible Gaming Reality Check",
			bg: "Разумно Залагане - Проверка",
			ru: "Ответственная игра – Проверка",
			nl: "Realiteitscontrole voor verantwoord spelen",
			fr: "État des lieux du jeu responsable",
			mk: "Одговорна игра на среќа, проверка на реалноста",
			ro: "Avertizare de joc responsabil",
			es: "Comprobación de Juego Responsable",
			pt: "Jogo Responsável - Pés na Terra",
			it: "Controllo Gioco Responsabile",
			da: "Ansvarligt spil virkelighedstjek",
			de: "Realitätscheck für verantwortungsbewusstes Spielen"
		},
		reality_check_message:
		{
			en: "You have been playing for <font color='#C3D2FF'>((playTime:String))</font> minutes. You have wagered <font color='#C3D2FF'>((totalBet:Money))</font>. You won <font color='#C3D2FF'>((totalWin:Money))</font> and your result is <font color='#C3D2FF'>((balance:Money))</font>.<br/><br/>If you wish to continue to play, please click OK, or press Exit to quit the game.",
			bg: "Играете от <font color='#C3D2FF'>((playTime:String))</font> минути. Заложили сте <font color='#C3D2FF'>((totalBet:Money))</font>. Спечелили сте <font color='#C3D2FF'>((totalWin:Money))</font> и резултатът Ви е <font color='#C3D2FF'>((balance:Money))</font>. Ако желаете да продължите играта, моля натиснете ОК, или изберете Изход за да напуснете играта.",
			ru: "Вы играли в течение <font color='#C3D2FF'>((playTime:String))</font> минут. Вы поставили всего <font color='#C3D2FF'>((totalBet:Money))</font>. Вы выиграли <font color='#C3D2FF'>((totalWin:Money))</font> и Ваш результат <font color='#C3D2FF'>((balance:Money))</font>. Если Вам хочется продолжить игру, пожалуйста, нажмите кнопку ОК, или чтобы выйти из игры - Выход.",
			nl: "U hebt <font color='#C3D2FF'>((playTime:String))</font> minuten gespeeld. U hebt <font color='#C3D2FF'>((totalBet:Money))</font> ingezet. U hebt <font color='#C3D2FF'>((totalWin:Money))</font> gewonnen en uw resultaat is <font color='#C3D2FF'>((balance:Money))</font>. Als u verder wilt spelen, klik op OK of druk op Afsluiten om het spel te verlaten.",
			fr: "Vous jouez depuis <font color='#C3D2FF'>((playTime:String))</font> minutes. Vous avez misé <font color='#C3D2FF'>((totalBet:Money))</font> Vous avez gagné <font color='#C3D2FF'>((totalWin:Money))</font> et votre résultat est de <font color='#C3D2FF'>((balance:Money))</font>. Cliquez sur OK si vous souhaitez continuer à jouer ou appuyez sur Sortie pour quitter le jeu.",
			mk: "Вие игравте <font color='#C3D2FF'>((playTime:String))</font> минути. Вие се обложивте <font color='#C3D2FF'>((totalBet:Money))</font>. Вие добивте <font color='#C3D2FF'>((totalWin:Money))</font> и вашиот резултат е <font color='#C3D2FF'>((balance:Money))</font>. Ако сакате да продолжите да играте, ве молиме притиснете OK, или притиснете Излез да ја откажете играта.",
			ro: "Ați jucat timp de <font color='#C3D2FF'>((playTime:String))</font> de minute. Ați pariat <font color='#C3D2FF'>((totalBet:Money))</font>. Ați câștigat <font color='#C3D2FF'>((totalWin:Money))</font> iar rezultatul dvs. este <font color='#C3D2FF'>((balance:Money))</font>. Dacă doriți să jucați în continuare, vă rugăm să faceți clic pe OK, sau apăsați Iesire pentru a iesi din joc.",
			es: "Ha jugado durante <font color='#C3D2FF'>((playTime:String))</font> minutos. A apostado <font color='#C3D2FF'>((totalBet:Money))</font>. Ha ganado <font color='#C3D2FF'>((totalWin:Money))</font> y su balance es de <font color='#C3D2FF'>((balance:Money))</font>.<br/><br/>Si desea continuar jugando, por favor, pulse OK, o pulse Salir para abandonar el juego.",
			pt: "Está a jogar há <font color='#C3D2FF'>((playTime:String))</font> minutos. Apostou <font color='#C3D2FF'>((totalBet:Money))</font>. Ganhou <font color='#C3D2FF'>((totalWin:Money))</font> e o seu resultado é de <font color='#C3D2FF'>((balance:Money))</font>.<br/><br/>Se deseja continuar a jogar, clique OK ou prima Sair para sair do jogo.",
			it: "Ha giocato per <font color='#C3D2FF'>((playTime:String))</font> minuti. Ha giocato <font color='#C3D2FF'>((totalBet:Money))</font>. Hai vinto <font color='#C3D2FF'>((totalWin:Money))</font> e il suo risultato è <font color='#C3D2FF'>((balance:Money))</font>.<br/><br/>Se desidera continuare a giocare cilcchi OK, altrimenti prema Uscita per uscire dal gioco.",
			da: "Du har spillet i <font color='#C3D2FF'>((playTime:String))</font> minutter. Du har satset <font color='#C3D2FF'>((totalBet:Money))</font>. Du har vundet <font color='#C3D2FF'>((totalWin:Money))</font> og dit resultat er <font color='#C3D2FF'>((balance:Money))</font>.<br/><br/>Hvis du ønsker at fortsætte med at spille, skal du klikke på OK, ellers tryk på EXIT for at gå ud af spillet.",
			de: "Sie spielen nun seit <font color='#C3D2FF'>((playTime:String))</font> Minuten. Sie haben <font color='#C3D2FF'>((totalBet:Money))</font> gesetzt. Sie haben <font color='#C3D2FF'>((totalWin:Money))</font> gewonnen und ihr Nettogewinn beträgt <font color='#C3D2FF'>((balance:Money))</font>.<br/><br/>Wenn Sie weiterspielen möchten, klicken Sie bitte auf OK, oder drücken Sie Verlassen, um das Spiel zu beenden."
		},
        reality_check_time_passed_only_message:
		{
			en: "You have been playing for <font color='#C3D2FF'>((playTime:String))</font> minutes. If you wish to continue to play, please click OK, or press Exit to quit the game.",
			bg: "Играете от <font color='#C3D2FF'>((playTime:String))</font> минути. Ако желаете да продължите играта, моля натиснете ОК, или изберете Изход за да напуснете играта.",
			ru: "Вы играли в течение <font color='#C3D2FF'>((playTime:String))</font> минут. Если Вам хочется продолжить игру, пожалуйста, нажмите кнопку ОК, или чтобы выйти из игры - Выход.",
			nl: "U hebt <font color='#C3D2FF'>((playTime:String))</font> minuten gespeeld. Als u verder wilt spelen, klik op OK of druk op Afsluiten om het spel te verlaten.",
            fr: "Vous jouez depuis <font color='#C3D2FF'>((playTime:String))</font> minutes. Cliquez sur OK si vous souhaitez continuer à jouer ou appuyez sur Sortie pour quitter le jeu.",
			mk: "Вие игравте <font color='#C3D2FF'>((playTime:String))</font> минути. Ако сакате да продолжите да играте, ве молиме притиснете OK, или притиснете Излез да ја откажете играта.",
            ro: "Ați jucat timp de <font color='#C3D2FF'>((playTime:String))</font> de minute. Dacă doriți să jucați în continuare, vă rugăm să faceți clic pe OK, sau apăsați Iesire pentru a iesi din joc.",
			es: "Ha jugado durante <font color='#C3D2FF'>((playTime:String))</font> minutos. Si desea continuar jugando, por favor, pulse OK, o pulse Salir para abandonar el juego.",
			pt: "Está a jogar há <font color='#C3D2FF'>((playTime:String))</font> minutos. Se deseja continuar a jogar, clique OK ou prima Sair para sair do jogo.",
            it: "Ha giocato per <font color='#C3D2FF'>((playTime:String))</font> minuti. Se desidera continuare a giocare cilcchi OK, altrimenti prema Uscita per uscire dal gioco.",
            da: "Du har spillet i <font color='#C3D2FF'>((playTime:String))</font> minutter. Hvis du ønsker at fortsætte med at spille, skal du klikke på OK, ellers tryk på EXIT for at gå ud af spillet.",
            de: "Sie spielen nun seit <font color='#C3D2FF'>((playTime:String))</font> Minuten. Wenn Sie weiterspielen möchten, klicken Sie bitte auf OK, oder drücken Sie Verlassen, um das Spiel zu beenden."
		},
		time_proximity_title:
		{
			en: "Responsible Gaming Time Notification",
			bg: "Разумно Залагане - Известие за времетраене на сесия",
			ru: "Ответственная игра: лимит по времени",
			nl: "Mededeling voor een verantwoorde speeltijd",
			mk: "Времено известување за одговорно играње",
			es: "Notificación de Tiempo de Juego Responsible",
			pt: "Aviso de Tempo de Jogo Responsável",
			it: "Avviso Tempo Gioco Responsabile",
			ro: "Notificare de timp pentru joc responsabil",
			fr: "Notification du temps en jeu responsable",
			da: "Ansvarligt spil tidsnotifikation",
			de: "Zeitbenachrichtigung für verantwortungsbewusstes Spielen"
		},
		time_proximity_message:
		{
			en: "The session is almost over. You are about to reach your self-imposed session time limit.<br/><br/>Please click OK to continue the game.",
			bg: "Сесията Ви е към края си. Достигате зададеното от Вас самоограничение за времетраене на сесия.<br/><br/>Моля натиснете ОК, за да продължите играта.",
			ru: "Ваша игровая сессия почти подошла к концу. Вы почти достигли установленного вами ограничения по времени.<br/><br/>Нажмите OK для возвращения к игре.",
			nl: "De sessie is bijna verstreken. De tijdslimiet voor de sessie die u zelf hebt ingesteld is bijna bereikt.<br/><br/>Klik op OK om het spel verder te spelen.",
			mk: "Сесијата е речиси завршена. Вие скоро го достигнавте своето наметнато временско ограничување на сесијата.<br/><br/>Ве молиме притиснете OK за да ја продолжите играта.",
			es: "La sesión de juego está a punto de finalizar. Está a punto de alcanzar su límite de Tiempo de sesión auto establecido.<br/><br/>Por favor, pulse OK para continuar jugando.",
			pt: "A sessão está quase a terminar. Está prestes a atingir o seu limite de tempo de sessão autoimposto.<br/><br/>Clicque em OK para prosseguir com o jogo.",
			it: "La sessione è quasi terminata. Sta per raggiungere il suo limite di tempo autoimposto.<br/><br/>Prema OK per continuare il gioco.",
			ro: "Sesiunea aproape că s-a terminat. Sunteți pe cale să ajungeți la limita de timp de sesiune auto-impusă.<br/><br/>Vă rugăm să faceți clic pe OK pentru a continua jocul.",
			fr: "La session est presque terminée. Vous êtes sur le point d'atteindre la limite de temps par session que vous vous êtes imposée.<br/><br/>Cliquez sur OK pour continuer à jouer.",
			da: "Sessionen er næsten slut. Du er næsten nået til din selvindstillede sessionsgrænse.<br/><br/>Klik på OK for at fortsætte spillet.",
			de: "Die Sitzung ist fast vorbei. Sie erreichen in Kürze Ihr selbst auferlegtes Sitzungszeitlimit.<br/><br/>Bitte klicken Sie OK, um weiterzuspielen."
		},
		credit_left_title:
		{
			en: "Responsible Gaming Bet Notification",
			bg: "Разумно Залагане - Известие за направени Залози",
			ru: "Ответственная игра: лимит по ставкам",
			nl: "Mededeling voor een verantwoorde speelinzet",
			mk: "Известување за облог за одговорно играње",
			es: "Notificación de Apuesta de Juego Responsible",
			pt: "Aviso de Aposta de Jogo Responsável",
			it: "Avviso Puntata Gioco Responsabile",
			ro: "Notificare de pariere pentru joc responsabil",
			fr: "Notification de mise en jeu responsable",
			da: "Ansvarligt spil indsatsnotifikation",
			de: "Einsatzbenachrichtigung für verantwortungsbewusstes Spielen"
		},
		credit_left_message:
		{
			en: "The session is almost over. You are about to reach your self-imposed bet limit.<br/><br/>Please click OK to continue the game.",
			bg: "Сесията Ви е към края си. Достигате зададеното от Вас самоограничение на Залози.<br/><br/>Моля натиснете ОК, за да продължите играта.",
			ru: "Ваша игровая сессия почти подошла к концу. Вы почти достигли установленного вами ограничения по ставкам.<br/><br/>Нажмите OK для возвращения к игре.",
			nl: "De sessie is bijna verstreken. De inzetlimiet die u zelf hebt ingesteld is bijna bereikt.<br/><br/>Klik op OK om het spel verder te spelen.",
			mk: "Сесијата е речиси завршена. Вие скоро го достигнавте своето наметнато ограничување за облог.<br/><br/>Ве молиме притиснете OK за да ја продолжите играта.",
			es: "La sesión de juego está a punto de finalizar. Está a punto de alcanzar su límite de apuesta auto establecido.<br/><br/>Por favor, pulse OK para continuar jugando.",
			pt: "A sessão está quase a terminar. Está prestes a atingir o seu limite de aposta autoimposto.<br/><br/>Clicque em OK para prosseguir com o jogo.",
			it: "La sessione è quasi terminata. Sta per raggiungere il suo limite di puntate autoimposto.<br/><br/>Prema OK per continuare il gioco.",
			ro: "Sesiunea aproape s-a terminat. Sunteți pe cale să ajungeți la limita de pariere auto-impusă.<br/><br/>Vă rugăm să faceți clic pe OK pentru a continua jocul.",
			fr: "La session est presque terminée. Vous êtes sur le point d'atteindre la limite de mise par session que vous vous êtes imposée.<br/><br/>Cliquez sur OK pour continuer à jouer.",
			da: "Sessionen er næsten slut. Du er næsten nået til din selvindstillede indsatsgrænse.<br/><br/>Klik på OK for at fortsætte spillet.",
			de: "Die Sitzung ist fast vorbei. Sie erreichen in Kürze Ihr selbst auferlegtes Einsatzlimit.<br/><br/>Bitte klicken Sie OK, um weiterzuspielen."
		},
		loss_limit_title:
		{
			en: "Responsible Gaming Loss Limit",
			bg: "Разумно Залагане - Ограничение на Загубите",
			ru: "Ответственная игра - Ограничение Проигрыша",
			nl: "Verlieslimiet voor verantwoord spelen",
			mk: "Одговорна игра на среќа, ограничување на изгубеното",
			es: "Límite de Pérdida de Juego Responsabe",
			pt: "Limite de Perdas de Jogo Responsável",
			it: "Limite Perdita Gioco Responsabile",
			ro: "Limită de pierdere pentru joc responsabil",
			fr: "Limite de pertes en jeu responsable",
			da: "Ansvarligt spil tabsgrænse",
			de: "Verlustlimit für verantwortungsbewusstes Spielen"
		},
		loss_limit_message:
		{
			en: "You have reached your self-imposed loss limit and your game session has been terminated.<br/><br/>For exit, please press OK.",
			bg: "Достигнахте зададеното от Вас самоограничение за Загуби и игралната Ви сесия бе прекратена.<br/><br/>За изход, моля натиснете ОК.",
			ru: "Вы достигли установленного Вами Ограичение Проигрыша и Ваш игровой сеанс был закрыт.<br/><br/>Для выхода, пожалуйста нажмите ОК.",
			nl: "U hebt uw zelf opgelegd verlieslimiet en uw speelsessie werd beëindigd.<br/><br/>Om af te sluiten, druk op OK.",
			mk: "Вие го достигнавте само наметнатото ограничување на изгубеното и сесијата за вашата игра е прекината.<br/><br/>За да излезете, ве молиме притиснете OK.",
			es: "Ha alcanzado su límite personal de pérdida y su sesión ha sido finalizada.<br/><br/>Para salir, por favor, pulse OK.",
			pt: "Atingiu o seu limite autoimposto de perdas e a sua sessão de jogo foi terminada.<br/><br/>Para sair, prima OK.",
			it: "Ha raggiunto il limite di perdite autoimposto, e la sua sessione di gioco è stata chiusa.<br/><br/>Per uscire, prema OK.",
			ro: "Ați atins limita de pierdere auto-impusă și sesiunea dvs. de joc a fost întreruptă.<br/><br/>Pentru Iesire, vă rugăm să apăsați OK.",
			fr: "Vous avez atteint la limite de pertes que vous vous êtes imposée et votre session de jeu a pris fin.<br/><br/>Pour quitter, appuyez sur OK.",
			da: "Du er nået til din selvindstillede tabsgrænse, og din spillesession er blevet afsluttet.<br/><br/>Tryk på OK for at gå ud.",
			de: "Sie haben Ihr selbst auferlegtes Verlustlimit erreicht und Ihre Spielsitzung wurde beendet.<br/><br/>Um das Spiel zu beenden, drücken Sie bitte OK."
		},
		bet_limit_title:
		{
			en: "Responsible Gaming Bet Limit",
			bg: "Разумно Залагане - Ограничение на Залозите",
			ru: "Ответственная игра - Ограничение Ставок",
			nl: "Goklimiet voor verantwoord spelen",
			mk: "Одговорна игра на среќа, ограничување на обложувањето",
			es: "Límite de Apuesta de Juego Responsabe",
			pt: "Limite de Aposta de Jogo Responsável",
			it: "Limite Puntata Gioco Responsabile",
			ro: "Limită de pariere pentru joc responsabil",
			fr: "Limite de mise en jeu responsable",
			da: "Ansvarligt spil indsatsgrænse",
			de: "Einsatzlimit für verantwortungsbewusstes Spielen"
		},
		bet_limit_message:
		{
			en: "You have reached your self-imposed bet limit and your game session has been terminated.<br/><br/>For exit, please press OK.",
			bg: "Достигнахте зададеното от Вас самоограничение Залози и игралната Ви сесия бе прекратена.<br/><br/>За изход, моля натиснете ОК.",
			ru: "Вы достигли установленного Вами Ограичение Ставок и Ваш игровой сеанс был закрыт.<br/><br/>Для выхода, пожалуйста нажмите ОК.",
			nl: "U hebt uw zelf opgelegd goklimiet bereikt en uw speelsessie werd beëindigd.<br/><br/>Om af te sluiten, druk op OK.",
			mk: "Вие го достигнавте само наметнатото ограничување на обложеното и сесијата за вашата игра е прекината.<br/><br/>За да излезете, ве молиме притиснете OK.",
			es: "Ha alcanzado su límite personal de apuesta y su sesión ha sido finalizada.<br/><br/>Para salir, por favor, pulse OK.",
			pt: "Atingiu o seu limite autoimposto de aposta e a sua sessão de jogo foi terminada.<br/><br/>Para sair, prima OK.",
			it: "Ha raggiunto il limite di puntate che si è autoimposto e la sua sessione di gioco è stata chiusa.<br/><br/>Per uscire, prema OK.",
			ro: "Ați atins limita de pariere auto-impusă și sesiunea dvs. de joc a fost întreruptă.<br/><br/>Pentru Iesire, vă rugăm să apăsați OK.",
			fr: "Vous avez atteint la limite de mise que vous vous êtes imposée et votre session de jeu a pris fin.<br/><br/>Pour quitter, appuyez sur OK.",
			da: "Du er nået til din selvindstillede indsatsgrænse, og din spillesession er blevet afsluttet.<br/><br/>Tryk på OK for at gå ud.",
			de: "Sie haben Ihr selbst auferlegtes Einsatzlimit erreicht und Ihre Spielsitzung wurde beendet.<br/><br/>Um das Spiel zu beenden, drücken Sie bitte OK."
		},
		time_limit_title:
		{
			en: "Responsible Gaming Time Limit",
			bg: "Разумно Залагане - Ограничение Продължителност на сесия",
			ru: "Ответственная игра - Ограничение Продолжительности сеанса",
			nl: "Tijdslimiet voor verantwoord spelen",
			mk: "Одговорна игра на среќа, ограничување на времето",
			es: "Límite de Tiempo de Juego Responsabe",
			pt: "Limite de Tempo de Jogo Responsável",
			it: "Limite Tempo Gioco Responsabile",
			ro: "Limită de timp pentru joc responsabil",
			fr: "Limite de temps en jeu responsable",
			da: "Ansvarligt spil tidsgrænse",
			de: "Zeitlimit für verantwortungsbewusstes Spielen"
		},
		time_limit_message:
		{
			en: "You have reached your self-imposed session time limit and your game session has been terminated.<br/><br/>For exit, please press OK.",
			bg: "Достигнахте зададеното от Вас самоограничение Продължителност на сесия и игралната Ви сесия бе прекратена.<br/><br/>За изход, моля натиснете ОК.",
			ru: "Вы достигли установленного Вами Ограичение Продолжительности сеанса и Ваш игровой сеанс был закрыт.<br/><br/>Для выхода, пожалуйста нажмите ОК.",
			nl: "U hebt uw zelf opgelegd tijdslimiet bereikt en uw speelsessie werd beëindigd.<br/><br/>Om af te sluiten, druk op OK.",
			mk: "Вие го достигнавте само наметнатото ограничување на времето и сесијата за вашата игра е прекината.<br/><br/>За да излезете, ве молиме притиснете OK.",
			es: "Ha alcanzado su límite personal de tiempo y su sesión ha sido finalizada.<br/><br/>Para salir, por favor, pulse OK.",
			pt: "Atingiu o seu limite autoimposto de tempo de jogo e a sua sessão de jogo foi terminada.<br/><br/>Para sair, prima OK.",
			it: "Ha raggiunto il limite di tempo che si è autoimposto e la sua sessione di gioco è stata chiusa.<br/><br/>Per uscire, prema OK.",
			ro: "Ați atins limita de timp de sesiune auto-impusă și sesiunea dvs. de joc a fost întreruptă.<br/><br/>Pentru Iesire, vă rugăm să apăsați OK.",
			fr: "Vous avez atteint la limite de temps que vous vous êtes imposée et votre session de jeu a pris fin.<br/><br/>Pour quitter, appuyez sur OK.",
			da: "Du er nået til din selvindstillede sessionsgrænse, og din spillesession er blevet afsluttet.<br/><br/>Tryk på OK for at gå ud.",
			de: "Sie haben Ihr selbst auferlegtes Sitzungszeitlimit erreicht und Ihre Spielsitzung wurde beendet.<br/><br/>Um das Spiel zu beenden, drücken Sie bitte OK."
		},
		game_unavailable_error:
		{
			en: "You have unfinished game, which is not available for mobile. Please, try to complete it via desktop computer.",
			bg: "Имате незавършена игра, която не е достъпна през мобилнo устройство. Моля, като използвате компютър или лаптоп, опитайте да я завършете.",
			ru: "У вас есть незаконченная игра, которая недоступна для мобильных устройств. Пожалуйста, используйте ваш компютер или ноутбук и попрбуйте завершит ее.",
			fr: "Une de vos parties reste inachevée dans un jeu indisponible pour mobile. Veuillez essayer de le terminer par l'intermédiaire d'un ordinateur.",
			mk: "Вие имате не довршена игра, која не е достапна за на мобилен. Ве молиме, пробајте да ја довршите преку десктоп компјутер.",
			ro: "Aveți un joc neterminat, care nu este disponibil pentru mobil. Vă rugăm, încercați să îl finalizați folosind un computer.",
			es: "Tiene sin finalizar un juego que no está disponible para móvil. Por favor, trate de completarlo a través de un ordenador.",
			nl: "U hebt een onvoltooid game, dat niet voor mobiele apparaten beschikbaar is. Probeer het te voltooien via een desktopcomputer.",
            hu: "Önnek befejezetlen játéka van, amely nem érhető el mobil eszközön. Kérjük, próbálja meg befejezni számítógépen."
		},
        game_filtered_error:
		{
            bg: "В момента играта не е достъпна.",
            en: "The game is currently unavailable.",
            es: "El juego no está disponible actualmente.",
            go: "The game is currently unavailable.",
            it: "Il gioco è attualmente non disponibile.",
            ru: "В данный момент игра недоступна.",
            nl: "Het spel is momenteel niet beschikbaar.",
            fr: "Le jeu est actuellement indisponible.",
            mk: "Играта моментално е недостапна.",
            ro: "Jocul este momentan indisponibil.",
            pt: "O jogo não está disponível de momento.",
            hr: "Igra je trenutno nedostupna.",
            da: "Spillet er ikke tilgængeligt i øjeblikket.",
			hu: "A játék jelenleg nem érhető el.",
			sv: "Spelet är inte tillgängligt för tillfället."
        },
		unsupported_browser_error:
			{
				en: "Please, use mobile devices to open mobile games",
				bg: "Моля, използвайте мобилни устройства, за да отворите мобилни игри.",
				es: "Por favor, usa un dispositivo móvil para abrir los juegos para móvil.",
				go: "Please, use mobile devices to open mobile games",
				it: "Per favore usa un dispositivo mobile per aprire i giochi mobile.",
				ru: "Пожалуйста, используйте мобильное устройство, чтобы открыть игры для мобильных устройств.",
				nl: "Gebruik mobiele apparaten om mobiele games te openen.",
				fr: "Veuillez utiliser un appareil mobile pour ouvrir les jeux mobiles.",
				mk: "Користете мобилни уреди за да отворите мобилни игри.",
				ro: "Vă rugăm folosiți dispozitive mobile pentru a deschide jocuri mobile.",
				pt: "Use dispositivos móveis para abrir jogos móveis.",
				hr: "Za otvaranje mobilnih igara koristite mobilne uređaje.",
				da: "Brug venligst mobilenheder til at åbne mobilspil.",
				hu: "Please, use mobile devices to open mobile games",
				sv: "Vänligen använd mobila enheter för att öppna mobilspel.",
			},
		login_error:
		{
			en: "Unable to connect to server. Please close the window and try again.",
			bg: "Връзката със сървъра не може да бъде осъществена. Моля, затворете прозореца и опитайте отново.",
			ru: "Не удалось подключится к серверу. Пожалуйста, зокройте окно и попробуйте снова.",
			mk: "Врската со серверот не може да се воспостави. Ве молиме затворете го прозорецот и пробајте повторно.",
            hu: "Csatlakozás a szerverhez sikertelen. Kérjük, zárja be az ablakot és próbálja meg újból."
		},
		content_error:
		{
			en: "Unable to load content. Please retry.",
			bg: "Съдържанието не може да бъде заредено. Моля, опитайте отново.",
			ru: "Невозможно загрузить содержание. Пожалуйста, попробуйте снова.",
			mk: "Содржината не може да се вчита. Ве молиме затворете го прозорецот и пробајте повторно.",
            hu: "Tartalom betöltése nem lehetséges. Kérem, várjon!"
		},
		no_connecton:
		{
			en: "NO CONNECTION",
			bg: "НЯМА ВРЪЗКА",
			ru: "НЕТ СВЯЗИ",
			nl: "GEEN VERBINDING",
			fr: "PAS DE CONNEXION",
			mk: "НЕПОВРЗАНОСТ",
			ro: "FĂRĂ CONEXIUNE",
			es: "SIN CONEXIÓN",
			pt: "SEM LIGAÇÃO",
			it: "CONNESSIONE ASSENTE",
			da: "INGEN FORBINDELSE",
			hu: "NEM CSATLAKOZIK",
			sv: "UPPKOPPLING SAKNAS",
			de: "KEINE VERBINDUNG"
		},
		ok_text:
		{
			en: "OK",
			bg: "OK",
			ru: "OK",
			nl: "OK",
			fr: "OK",
			mk: "OK",
			ro: "OK",
			pt: "OK",
			it: "OK",
			hu: "OK",
			sv: "OK",
			de: "OK"
		},
		exit_text:
		{
			en: "Exit",
			bg: "Изход",
			ru: "Выход",
			nl: "Afsluiten",
			fr: "Sortie",
			mk: "Излез",
			ro: "Iesire",
			es: "Salir",
			pt: "Sair",
			it: "Uscita",
			hu: "KILÉPÉS",
			sv: "AVSLUTA",
			de: "VERLASSEN",
		}
	},
	
	currencies: [

	],
	
	localize: function(key)
	{
		var availableLanguages = PlatformSettings.translations[key];
		if(availableLanguages)
		{
			if(availableLanguages[PlatformSettings.language])
				return availableLanguages[PlatformSettings.language];
			
			if(availableLanguages["en"])
				return availableLanguages["en"];
		}
		
		return key + " not translated";
	}
},
{});