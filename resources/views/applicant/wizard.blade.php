<x-app-layout title="Multi-Step Application - OUT & STTC (SUPA)">
    <x-slot name="header">Online Application (OUT + STTC SUPA Joint Admission)</x-slot>

    <script>
        const tanzaniaRegions = {
            "Arusha": ["Arusha Mjini", "Arusha Vijijini", "Meru", "Karatu", "Monduli", "Longido", "Ngorongoro", "Arusha Urban"],
            "Dar es Salaam": ["Ilala", "Kinondoni", "Temeke", "Kigamboni", "Ubungo"],
            "Dodoma": ["Dodoma Mjini", "Bahi", "Chamwino", "Chemba", "Kondoa", "Mpwapwa", "Kongwa", "Dodoma"],
            "Geita": ["Geita Mjini", "Geita Vijijini", "Chato", "Bukombe", "Mbogwe", "Nyang'hwale"],
            "Iringa": ["Iringa Mjini", "Iringa Vijijini", "Kilolo", "Mufindi"],
            "Kagera": ["Bukoba Mjini", "Bukoba Vijijini", "Muleba", "Karagwe", "Biharamulo", "Ngara", "Kyerwa", "Missenyi"],
            "Katavi": ["Mpanda Mjini", "Mpanda Vijijini", "Nsimbo", "Mlele", "Tanganyika"],
            "Kigoma": ["Kigoma Ujiji", "Kigoma Vijijini", "Kasulu Mjini", "Kasulu Vijijini", "Kibondo", "Kakonko", "Uvinza", "Buhigwe"],
            "Kilimanjaro": ["Moshi Mjini", "Moshi Vijijini", "Hai", "Rombo", "Mwanga", "Same", "Siha"],
            "Lindi": ["Lindi Mjini", "Lindi Vijijini", "Kilwa", "Ruangwa", "Nachingwea", "Liwale"],
            "Manyara": ["Babati Mjini", "Babati Vijijini", "Hanang", "Mbulu", "Simanjiro", "Kiteto"],
            "Mara": ["Musoma Mjini", "Musoma Vijijini", "Tarime", "Serengeti", "Bunda", "Rorya", "Butiama"],
            "Mbeya": ["Mbeya Mjini", "Mbeya Vijijini", "Rungwe", "Kyela", "Chunya", "Mbarali", "Busokelo"],
            "Morogoro": ["Morogoro Mjini", "Morogoro Vijijini", "Kilosa", "Kilombero", "Ulanga", "Gairo", "Mvomero", "Malinyi"],
            "Mtwara": ["Mtwara Mjini", "Mtwara Vijijini", "Masasi", "Masasi Mjini", "Nanyumbu", "Newala", "Tandahimba"],
            "Mwanza": ["Nyamagana", "Ilemela", "Sengerema", "Misungwi", "Magu", "Ukerewe", "Kwimba"],
            "Njombe": ["Njombe Mjini", "Njombe Vijijini", "Wanging'ombe", "Makete", "Ludewa"],
            "Pemba North": ["Wete", "Micheweni"],
            "Pemba South": ["Chake Chake", "Mkoani"],
            "Pwani": ["Bagamoyo", "Chalinze", "Kibaha Mjini", "Kibaha Vijijini", "Kisarawe", "Mkuranga", "Rufiji", "Mafia", "Kibiti"],
            "Rukwa": ["Sumbawanga Mjini", "Sumbawanga Vijijini", "Kalambo", "Nkasi"],
            "Ruvuma": ["Songea Mjini", "Songea Vijijini", "Mbinga", "Mbinga Mjini", "Nyasa", "Tunduru", "Namtumbo"],
            "Shinyanga": ["Shinyanga Mjini", "Shinyanga Vijijini", "Kahama Mjini", "Ushetu", "Msalala", "Kishapu"],
            "Simiyu": ["Bariadi Mjini", "Bariadi Vijijini", "Maswa", "Meatu", "Itilima", "Busega"],
            "Singida": ["Singida Mjini", "Singida Vijijini", "Iramba", "Manyoni", "Ikungi", "Mkalama", "Itigi", "Singida Manispaa", "Singida"],
            "Songwe": ["Mbozi", "Momba", "Tunduma", "Ileje", "Songwe"],
            "Tabora": ["Tabora Mjini", "Uyui", "Nzega", "Nzega Mjini", "Igunga", "Sikonge", "Urambo", "Kaliua"],
            "Tanga": ["Tanga Mjini", "Muheza", "Korogwe", "Korogwe Vijijini", "Lushoto", "Mkinga", "Pangani", "Handeni", "Handeni Vijijini", "Bumbuli", "Kilindi"],
            "Zanzibar North": ["Kaskazini A", "Kaskazini B"],
            "Zanzibar South": ["Kusini", "Kati"],
            "Zanzibar Urban/West": ["Mjini", "Magharibi A", "Magharibi B"]
        };

        const tanzaniaWards = {
            "Arusha Mjini": ["Kekwe", "Kaloleni", "Kati", "Sekei", "Sombetini", "Unga L.T.D", "Daraja Mbili", "Elerai", "Engutoto", "Kimandolu", "Lemara", "Levolosi", "Ngarenaro", "Olasiti", "Oloirien", "Sokon I", "Terrat", "Themi", "Baraa", "Moivaro", "Muriet", "Moshono", "Sinoni"],
            "Arusha Urban": ["Kekwe", "Kaloleni", "Kati", "Sekei", "Sombetini", "Unga L.T.D", "Daraja Mbili", "Elerai", "Engutoto", "Kimandolu", "Lemara", "Levolosi", "Ngarenaro", "Olasiti", "Oloirien", "Sokon I", "Terrat", "Themi", "Baraa", "Moivaro", "Muriet", "Moshono", "Sinoni"],
            "Arusha Vijijini": ["Bangata", "Boma la Ng'ombe", "Ilkiding'a", "Kiranyi", "Kisongo", "Mateves", "Mlangarini", "Moivo", "Mwandeti", "Nduruma", "Oldonyosambu", "Olkokola", "Olmotonyi", "Oltroto", "Oltrumet", "Sambasha", "Sokon II"],
            "Meru": ["Akheri", "Ambureni", "Imbaseny", "Kikatiti", "Kikwe", "King'ori", "Leguruki", "Maji ya Chai", "Makiba", "Maroroni", "Mbuguni", "Ngaresero", "Ngarenanyuki", "Nkoanrua", "Nkoaranga", "Nkoarisambu", "Poli", "Sei", "Songoro", "Usa River"],
            "Karatu": ["Baray", "Buger", "Daa", "Endabash", "Endamarariek", "Ganako", "Karatu", "Mang'ola", "Mbulumbulu", "Oldeani", "Qurus", "Rhotia"],
            "Monduli": ["Engaruka", "Engutoto", "Esilalei", "Lepurko", "Lolkisale", "Majengo", "Makuyuni", "Meserani", "Meto", "Monduli Juu", "Monduli Mjini", "Mto wa Mbu", "Nagarashi", "Sepeko", "Selela"],
            "Longido": ["Engarenaibor", "Engikaret", "Gelai Lumbwa", "Gelai Meirugoi", "Iloirero", "Kamwanga", "Kitenden", "Kimokouwa", "Longido", "Matale", "Mundarara", "Namanga", "Olmolog", "Orbomba", "Tingatinga"],
            "Ngorongoro": ["Alailelai", "Arash", "Digodigo", "Enduleni", "Kakesio", "Maambrene", "Malambo", "Nainokanoka", "Naiyobi", "Ngorongoro", "Olbalbal", "Oldonyo Sambu", "Oloipiri", "Oloirien", "Pinyinyi", "Sale", "Samunge", "Soit Sambu"],

            "Ilala": ["Buguruni", "Chanika", "Gerezani", "Gongo la Mboto", "Ilala", "Jangwani", "Kariakoo", "Kinyerezi", "Kipawa", "Kisutu", "Kitunda", "Kivukoni", "Kiwalani", "Mchafukoge", "Mchikichini", "Msongola", "Pugu", "Segerea", "Tabata", "Ukonga", "Upanga Magharibi", "Upanga Mashariki", "Vingunguti", "Zingiziwa", "Buyuni", "Majohe", "Liwiti", "Minazi Mirefu", "Mnyamani", "Kimanga", "Bonyokwa"],
            "Kinondoni": ["Bunju", "Hananasif", "Kawe", "Kibamba", "Kigogo", "Kijitonyama", "Kinondoni", "Kunduchi", "Mabwepande", "Magomeni", "Makumbusho", "Makruma", "Mbezi", "Mbweni", "Mikocheni", "Msasani", "Mwananyamala", "Mzimuni", "Ndugumbi", "Tandale", "Wazo", "Goba", "Saranga"],
            "Temeke": ["Azimio", "Chamazi", "Chang'ombe", "Charambe", "Keko", "Kibada", "Kimbiji", "Kisarawe II", "Kurasini", "Makangarawe", "Mbagala", "Mbagala Kuu", "Mianzini", "Miburani", "Mjimwema", "Pemba Mnazi", "Sandali", "Somangila", "Tandika", "Temeke", "Toangoma", "Vijibweni", "Yombo Vituka", "Buza", "Kilakala"],
            "Kigamboni": ["Ferry", "Kigamboni", "Kibada", "Kisarawe II", "Kimbiji", "Mjimwema", "Pemba Mnazi", "Somangila", "Tungi", "Vijibweni"],
            "Ubungo": ["Goba", "Kibamba", "Kimara", "Kwangwe", "Mabibo", "Makuburi", "Makurumla", "Manzese", "Mbezi", "Mburahati", "Msigani", "Sinza", "Ubungo"],

            "Dodoma Mjini": ["Chamwino", "Chang'ombe", "Chigongwe", "Chihanga", "Hazina", "Hombolo Bwawani", "Hombolo Makulu", "Ipagala", "Ipala", "Iyumbu", "Kikuyu Kaskazini", "Kikuyu Kusini", "Kilimani", "Kiwanja cha Ndege", "Kizota", "Madukani", "Majengo", "Makole", "Makutupora", "Mbabala", "Mbalawala", "Miyuji", "Mkonze", "Mnadani", "Mpunguzi", "Msalato", "Mtumba", "Nala", "Ndegai", "Njedengwa", "Nkuhungu", "Nzuguni", "Tambukareli", "Uhuru", "Veyula", "Zuzu"],
            "Dodoma": ["Chamwino", "Chang'ombe", "Chigongwe", "Chihanga", "Hazina", "Hombolo Bwawani", "Hombolo Makulu", "Ipagala", "Ipala", "Iyumbu", "Kikuyu Kaskazini", "Kikuyu Kusini", "Kilimani", "Kiwanja cha Ndege", "Kizota", "Madukani", "Majengo", "Makole", "Makutupora", "Mbabala", "Mbalawala", "Miyuji", "Mkonze", "Mnadani", "Mpunguzi", "Msalato", "Mtumba", "Nala", "Ndegai", "Njedengwa", "Nkuhungu", "Nzuguni", "Tambukareli", "Uhuru", "Veyula", "Zuzu"],
            "Bahi": ["Babayu", "Bahi", "Chali", "Chibelela", "Chikola", "Chipanga", "Ibihwa", "Ilindi", "Kigwe", "Lamaiti", "Mpalanga", "Mpamantwa", "Msisi", "Mtitaa", "Mundemu", "Nondwa", "Zanka"],
            "Chamwino": ["Buigiri", "Chamwino", "Chilonwa", "Chinugulu", "Chitoli", "Dabalo", "Fufu", "Handali", "Haneti", "Huzi", "Idifu", "Igandu", "Ikowa", "Iringa Mvumi", "Itiso", "Loje", "Majeleko", "Makang'wa", "Manchali", "Mlowa Bwawani", "Mlowa Barabarani", "Mpwayungu", "Msamalo", "Msanga", "Muungano", "Mvumi Makulu", "Mvumi Mission", "Nghambaku", "Nsegeli", "Segala", "Zajilwa"],
            "Chemba": ["Chandama", "Chemba", "Churuku", "Farkwa", "Gwandi", "Goima", "Kinyamsindo", "Kwamtoro", "Lahoda", "Lalta", "Makorongo", "Mondo", "Mpendo", "Paranga", "Sanzawa", "Songolo", "Tumbakose"],
            "Kondoa": ["Bumbuta", "Busi", "Changaa", "Chemchem", "Haubi", "Hondomairo", "Itaswi", "Kikilo", "Kikore", "Kilimani", "Kolo", "Kondoa Mjini", "Kinyasi", "Kisese", "Kwadelo", "Masange", "Mnenia", "Pahi", "Salanka", "Soera", "Suruke", "Thawi"],
            "Mpwapwa": ["Berege", "Chipogolo", "Chinyalingwe", "Chunyu", "Gulwe", "Godegode", "Ipera", "Iwondo", "Kimagai", "Kingiti", "Kibakwe", "Lufu", "Luhundwa", "Lumuma", "Massa", "Matomondo", "Mazae", "Mbuga", "Mpwapwa Mjini", "Mtera", "Nghambi", "Pwaga", "Rudi", "Ving'hawe", "Wotta"],
            "Kongwa": ["Chamkoroma", "Chitego", "Chiwe", "Hogoro", "Iduo", "Kibaigwa", "Kongwa", "Lenjulu", "Makawa", "Matongoro", "Mkoka", "Mlali", "Mtanana", "Nghumbi", "Ngomai", "Pandambili", "Sagara", "Sejeli", "Songambele", "Tubugwe", "Ugogoni", "Zoissa"],

            "Singida Mjini": ["Ipembe", "Kindai", "Majengo", "Mandewa", "Mitunduruni", "Minga", "Misuna", "Mtamaa", "Mtipa", "Mugumu", "Mwankoko", "Uhamaka", "Utemini", "Unambwe", "Unamfe", "Unanyoni", "Unyambwa", "Unyangwe", "Kisaki"],
            "Singida Manispaa": ["Ipembe", "Kindai", "Majengo", "Mandewa", "Mitunduruni", "Minga", "Misuna", "Mtamaa", "Mtipa", "Mugumu", "Mwankoko", "Uhamaka", "Utemini", "Unambwe", "Unamfe", "Unanyoni", "Unyambwa", "Unyangwe", "Kisaki"],
            "Singida": ["Ipembe", "Kindai", "Majengo", "Mandewa", "Mitunduruni", "Minga", "Misuna", "Mtamaa", "Mtipa", "Mugumu", "Mwankoko", "Uhamaka", "Utemini", "Unambwe", "Unamfe", "Unanyoni", "Unyambwa", "Unyangwe", "Kisaki"],
            "Singida Vijijini": ["Ihanja", "Ikhanoda", "Ilongero", "Itaja", "Kinyagigi", "Kinyeto", "Maghojoa", "Makuro", "Merya", "Mgori", "Mrama", "Msisi", "Mtumbe", "Mudida", "Mwasauya", "Ngimu", "Puma", "Ughandi"],
            "Iramba": ["Kaselya", "Kidaru", "Kinampanda", "Kiomboi", "Kisiriri", "Kyengege", "Mbelekese", "Ndago", "Nduguti", "Ntwike", "Old Kiomboi", "Shelui", "Tulya", "Ulemo", "Urughu"],
            "Manyoni": ["Aghondi", "Chikola", "Chikuyu", "Heka", "Idywili", "Isseke", "Kintinku", "Majiri", "Makanda", "Makuru", "Manyoni", "Maweni", "Mkwese", "Muhalala", "Nkonko", "Sanza", "Saranda", "Sasajila", "Sasilo", "Solya"],
            "Ikungi": ["Dung'unyi", "Ihanja", "Ikungi", "Irisya", "Isseke", "Issuna", "Iglansoni", "Kituntu", "Kinyeto", "Mang'onyi", "Minyinga", "Misughaa", "Mkiwa", "Mtunduru", "Muhintiri", "Mungaa", "Ntuntu", "Puma", "Sepuka", "Siuyu", "Unyangwe"],
            "Mkalama": ["Gumanga", "Ibaga", "Igonji", "Ilunda", "Kinyambuli", "Kinyangiri", "Kirumi", "Matongo", "Miganga", "Mpambala", "Msingi", "Mwangeza", "Nduguti", "Tumuli"],
            "Itigi": ["Agwandi", "Doroto", "Idywili", "Ipande", "Itigi Majengo", "Itigi Mjini", "Kalisya", "Kitaraka", "Mitundu", "Mwamagembe", "Rungwa", "Sanjaranda", "Tambukareli"],

            "Geita Mjini": ["Bombambili", "Bung'wangoko", "Kalangalala", "Kanyala", "Kaseme", "Katoma", "Katoro", "Mgusu", "Mtakuja", "Nyankumbu", "Nyang'hwale", "Nyakamwaga", "Shiloleli"],
            "Geita Vijijini": ["Bugalama", "Bugulula", "Bujula", "Bukoli", "Bukondo", "Busanda", "Butobela", "Chigunga", "Isulwabutundwe", "Izumacheli", "Kagu", "Kakubiro", "Kamena", "Kamhanga", "Kaseme", "Katoma", "Katoro", "Lubanga", "Lwamgasa", "Nyarugusu", "Nzera", "Senga"],
            "Chato": ["Buseresere", "Butengole", "Bwanga", "Bwera", "Bwina", "Chato", "Chato Mjini", "Ichwankima", "Ilemela", "Ilyamchele", "Iparamasa", "Kachwamba", "Kasenga", "Katende", "Kigongo", "Makurugusi", "Muganza", "Muungano", "Nyamirembe", "Nyambogo", "Nyarutembo"],
            "Bukombe": ["Bugelenga", "Bukombe", "Busonzo", "Butinzya", "Bwanga", "Iyogelo", "Lyambamgongo", "Namonge", "Ng'anzo", "Runzewe Magharibi", "Runzewe Mashariki", "Ushirombo"],
            "Mbogwe": ["Bukandwe", "Ikandiro", "Ikunguigazi", "Ilolangulu", "Iponya", "Lugunga", "Lulembela", "Masumbwe", "Mbogwe", "Nanda", "Ng'homolwa", "Nyakafulu", "Nyasato", "Ushirika"],
            "Nyang'hwale": ["Bukwimba", "Busolwa", "Izunya", "Kafita", "Kakora", "Kharumwa", "Mabuki", "Mwingiro", "Nundu", "Nyang'hwale", "Nyabubele", "Shabaka"],

            "Iringa Mjini": ["Gangilonga", "Ilala", "Isakalilo", "Kihesa", "Kitanzini", "Kitwiru", "Kwakilosa", "Mivinjeni", "Mkwawa", "Mlandege", "Mshindo", "Mtwivila", "Nduli", "Ruaha"],
            "Iringa Vijijini": ["Idodi", "Ifunda", "Ilolompya", "Image", "Ismani", "Izazi", "Kalenga", "Kihorotondo", "Kiwere", "Maboga", "Mahuninga", "Malengamakali", "Mgama", "Mlenge", "Mseke", "Nzihi", "Ulanda", "Wrole"],
            "Kilolo": ["Boma la Ng'ombe", "Dabaga", "Ibumu", "Idete", "Ihimbwa", "Ilula", "Image", "Irole", "Kimala", "Lugalo", "Mahenge", "Masisiwe", "Mlafu", "Mtitu", "Ng'ang'ange", "Ng'uruhe", "Nyalumbu", "Ruaha Mbuyuni", "Udekwa", "Ukumbi"],
            "Mufindi": ["Bumilayinga", "Idunda", "Ifwagi", "Igombavanu", "Igowole", "Ihanu", "Ihalimba", "Ikongosi", "Ikweha", "Itandula", "Kasanga", "Kibengu", "Kiyowela", "Luhunga", "Maduma", "Makungu", "Malangali", "Mapanda", "Mbalamaziwa", "Mdabulo", "Mninga", "Mtambula", "Mtwango", "Nyololo", "Sadani"],

            "Bukoba Mjini": ["Bakoba", "Buhembe", "Hamugembe", "Ishozi", "Kagondo", "Kahororo", "Kashai", "Kibeta", "Kitendaguro", "Miembeni", "Nshambya", "Nyamyaga", "Rwamishenye"],
            "Bukoba Vijijini": ["Buhendangabo", "Bujugo", "Butelankuzi", "Ibwera", "Izigo", "Kaagya", "Kaibanja", "Kanyangereko", "Karabagaine", "Kasharu", "Katerero", "Katoma", "Kibirizi", "Kikomero", "Kishanje", "Kishogo", "Kyamulaile", "Maruku", "Mikoni", "Mugajwale", "Nyakato", "Nyakibimbili", "Rubale", "Rukoma"],
            "Muleba": ["Biirabo", "Bisheke", "Buganguzi", "Bulyakashaju", "Bumbire", "Bureza", "Burungura", "Gubazi", "Gwanseli", "Ibuga", "Ijumbi", "Ikondo", "Ikuza", "Izigo", "Kabirizi", "Kagoma", "Kamachumu", "Karambi", "Kasharunga", "Kashasha", "Katoke", "Kerebe", "Kibanga", "Kishanda", "Kyebitembe", "Mafumbo", "Magata", "Mubunda", "Muleba", "Mushabago", "Ngenge", "Nshamba", "Nyabuntundu", "Nyakatanga", "Rulanda", "Rutooke"],
            "Karagwe": ["Bugene", "Bweranyange", "Chanika", "Igurwa", "Ihanda", "Ihembe", "Kanoni", "Kayanga", "Kibondo", "Kiruruma", "Kituntu", "Mihingiro", "Ndama", "Nyabiyonza", "Nyaishozi", "Nyakasimbi", "Rugera", "Rugu"],
            "Biharamulo": ["Biharamulo Mjini", "Bisunzu", "Kabindi", "Kalenge", "Kaniha", "Lusahunga", "Nemba", "Nyabusozi", "Nyakabingo", "Nyamahanga", "Nyamigogo", "Nyantakara", "Runazi", "Ruziba"],
            "Ngara": ["Bugarama", "Bukiriro", "Kabanga", "Kanazi", "Kasulo", "Keza", "Kirushya", "Mabawe", "Muganza", "Mugoma", "Murukulazo", "Ngara Mjini", "Ntobeye", "Nyamiaga", "Nyakiziba", "Rusumo", "Ruvubu"],
            "Kyerwa": ["Bugomora", "Businde", "Isingiro", "Kaisho", "Kamwema", "Kigarama", "Kikukuru", "Kimuli", "Kitwechembogo", "Kyerwa", "Mabira", "Murongo", "Nkwenda", "Nyakatuntu", "Rukuraijo", "Rwabwere"],
            "Missenyi": ["Bugandika", "Bugorora", "Buyango", "Bwanjai", "Gera", "Ishunju", "Kakunyu", "Kanyigo", "Kashenye", "Kassambya", "Kilimilile", "Kitobo", "Kyaka", "Minziro", "Missenyi", "Mutukula", "Nsunga"],

            "Mpanda Mjini": ["Kakese", "Kawanzige", "Magamba", "Majengo", "Makanyagio", "Misunkumalo", "Mpanda Hotel", "Mwamkulu", "Nsemulwa", "Shanwe", "Siloka", "Uwanja wa Ndege"],
            "Mpanda Vijijini": ["Ikola", "Kabungu", "Kapalamsenga", "Karema", "Katuma", "Mishamo", "Mpimbwe", "Mwese", "Sibwesa", "Tongwe"],
            "Nsimbo": ["Itenka", "Kanoge", "Kapanda", "Katumba", "Litapunga", "Machimboni", "Mtisi", "Nsimbo", "Sitalike", "Ugalla", "Uruwira"],
            "Mlele": ["Ilunde", "Inyonga", "Kamsisi", "Majimoto", "Mamba", "Mbede", "Usevya", "Utende"],
            "Tanganyika": ["Bulamata", "Ikola", "Kabungu", "Kapalamsenga", "Karema", "Katuma", "Kasekese", "Mishamo", "Mpanda Ndogo", "Mwese", "Sibwesa", "Tongwe"],

            "Kigoma Ujiji": ["Bangwe", "Buhanda", "Businde", "Buzebazeba", "Gungu", "Kagera", "Kasimbu", "Kibirizi", "Kigoma", "Kipampa", "Kitongoni", "Machinjioni", "Majengo", "Mwanga Kaskazini", "Mwanga Kusini", "Rubuga", "Rusimbi", "Ujiji"],
            "Kigoma Vijijini": ["Bitale", "Kagunga", "Kalinzi", "Kidahwe", "Mkabogo", "Matendo", "Mkongoro", "Mungonya", "Nkungwe", "Nyarubanda", "Simbo", "Ziwani"],
            "Kasulu Mjini": ["Heru Juu", "Kigondo", "Kimobwa", "Kumnyika", "Murubona", "Mwilamvya", "Nyansha", "Nyakitonto", "Ruhita"],
            "Kasulu Vijijini": ["Bugaga", "Buhoro", "Gitarama", "Kagera Bugutu", "Kasangezi", "Kigembe", "Kitagata", "Kurugongo", "Makere", "Muzye", "Nyakitonto", "Nyamidaho", "Nyamnyusi", "Rungwe Mpoma", "Rusesa", "Shunguliba", "Titye"],
            "Kibondo": ["Bitare", "Biturana", "Bunyambo", "Busagara", "Busunzu", "Itaba", "Kagezi", "Kibondo Mjini", "Kibondo Vijijini", "Kitahana", "Kumwambu", "Mabamba", "Misezero", "Murungu", "Rugongwe"],
            "Kakonko": ["Gwarama", "Kakonko", "Kasanda", "Kasuga", "Katanga", "Kiziguzigu", "Mugunzu", "Muhange", "Nyamtukuza", "Nyanzige", "Rugenge"],
            "Uvinza": ["Basanza", "Buhingu", "Heru Ushingo", "Igalula", "Ilagala", "Itebula", "Kandaga", "Kazuramimba", "Mganza", "Mtego wa Noti", "Nguruka", "Sigunga", "Sunuka", "Uvinza"],
            "Buhigwe": ["Biharu", "Buhigwe", "Bukuba", "Janda", "Kajana", "Kibande", "Kilelema", "Mugera", "Muhinda", "Munana", "Munanila", "Mwayaya", "Nyamugali", "Rusaba"],

            "Moshi Mjini": ["Boma Mbuzi", "Bondeni", "Kaloleni", "Karanga", "Kiboriloni", "Kilimanjaro", "Kiusa", "Korongoni", "Longuo B", "Majengo", "Mawenzi", "Mfumuni", "Miembeni", "Mji Mpya", "Msaranga", "Njoro", "Pasua", "Rau", "Shirimatunda", "Soweto"],
            "Moshi Vijijini": ["Arusha Chini", "Kahe Mashariki", "Kahe Magharibi", "Kibosho Kati", "Kibosho Magharibi", "Kibosho Mashariki", "Kindi", "Kirima", "Kirua Vunjo Kusini", "Kirua Vunjo Mashariki", "Kirua Vunjo Magharibi", "Mabogini", "Makuyuni", "Mamba Kaskazini", "Mamba Kusini", "Marangu Magharibi", "Marangu Mashariki", "Mbokomu", "Mwika Kaskazini", "Mwika Kusini", "Okaoni", "Old Moshi Magharibi", "Old Moshi Mashariki", "Uru Kaskazini", "Uru Kusini", "Uru Shimbwe", "Uru Mashariki"],
            "Hai": ["Bomang'ombe", "Hai Mjini", "Kia", "Machame Kaskazini", "Machame Kusini", "Machame Magharibi", "Machame Mashariki", "Machame Uroki", "Machame Weruweru", "Masama Kusini", "Masama Magharibi", "Masama Mashariki", "Masama Rundugai", "Mnadani"],
            "Rombo": ["Chala", "Ikuini", "Katangara Mrere", "Kelamfua Mokala", "Keni Mengeni", "Keni Mriti Mengwe", "Kirongo Samanga", "Kirwa Keni", "Kisale Msaranga", "Kitirima Kingachi", "Mahanje", "Mamsera", "Manda", "Marangu", "Mashati", "Mengeni", "Mkuu", "Motamburu Kitendeni", "Mrao Keryo", "Nanja", "Ngoyoni", "Olele", "Reha", "Shimbi", "Tarakea Motamburu", "Ubetu Kahe", "Ushiri Ikuini"],
            "Mwanga": ["Chomvu", "Jipe", "Kifula", "Kighare", "Kigonigoni", "Kileo", "Kilomeni", "Kirongwe", "Kirya", "Kwakoa", "Lang'ata", "Lembeni", "Mgagao", "Minazi", "Msangeni", "Mwanga", "Mwaniko", "Ngujini", "Shighatini", "Tolotha"],
            "Same": ["Bangalala", "Bombo", "Bwambo", "Chome", "Hedaru", "Kalemawe", "Kihurio", "Kirangare", "Kisima", "Kisiwani", "Lugulu", "Mabilioni", "Makanya", "Mamba Myamba", "Mamba Ndanda", "Mshewa", "Msindo", "Mwembe", "Myamba", "Ndungu", "Njoro", "Ruvu", "Same Mjini", "Stesheni", "Suji", "Turu", "Vudee", "Vuje", "Vumari", "Zamba"],
            "Siha": ["Biriri", "Gararagua", "Ivaeny", "Karansi", "Kashashi", "Livishi", "Makiwaru", "Nasai", "Ndumeti", "Ngarenairobi", "Olkolili", "Sanya Juu", "Songu"],

            "Lindi Mjini": ["Chilala", "Jamhuri", "Makonde", "Matopeni", "Mbanja", "Mikindani", "Mingoyo", "Mitandi", "Msinjahili", "Mtanda", "Mwenge", "Nachingwea", "Nandagala", "Ng'apa", "Rahaleo", "Rasbura", "Tandika", "Wireless"],
            "Lindi Vijijini": ["Chiponda", "Kilolambwani", "Kitomanga", "Kiwalala", "Mandwanga", "Matimba", "Mchinga", "Milola", "Mipingo", "Mnara", "Mnolela", "Mtama", "Mtumbya", "Nachunyu", "Navanga", "Nyambwe", "Nyangamara", "Nyangao", "Sudi"],
            "Kilwa": ["Chumo", "Kandawale", "Kikole", "Kipatimu", "Kivinje Singino", "Lihimalyao", "Likawage", "Mandawa", "Masoko", "Miguruwe", "Mingumbi", "Miteja", "Mitole", "Nanjirinji", "Pande Mikoma", "Songosongo", "Tingi"],
            "Ruangwa": ["Chibula", "Chienjere", "Chinongwe", "Chunyu", "Likunja", "Luchelegwe", "Makanjiro", "Malolo", "Mandara", "Mandawa", "Matambarale", "Mbekenyera", "Mnacho", "Nachingwea", "Nandagala", "Nanganga", "Nangurugai", "Ruangwa"],
            "Nachingwea": ["Boma", "Chilidu", "Chiola", "Kiegei", "Kilimani Hewa", "Kipara Mnero", "Lionja", "Marambo", "Mbondo", "Mchonda", "Mnero Miembeni", "Mnero Ngongo", "Mpiruka", "Mtua", "Naipanga", "Naipingo", "Namapwia", "Namatula", "Nambambo", "Namikango", "Nangowe", "Nditi", "Ngunichile", "Ruponda", "Stesheni"],
            "Liwale": ["Barikiwa", "Kiangara", "Kibutuka", "Kichonda", "Kimambi", "Liwale Mjini", "Liwale Boma", "Makata", "Mangirikiti", "Mihumo", "Mirui", "Mpigamiti", "Nangano", "Ngongowele"],

            "Babati Mjini": ["Babati", "Bagara", "Bonga", "Galapo", "Maisaka", "Mutuka", "Nangara", "Sigino", "Singe"],
            "Babati Vijijini": ["Ayasanda", "Bashnet", "Bonga", "Dabil", "Dareda", "Duru", "Endakiso", "Gallapo", "Gidas", "Kiru", "Madunga", "Magara", "Magugu", "Mamire", "Mwada", "Nkaiti", "Qash", "Riroda", "Ufana"],
            "Hanang": ["Balangdalalu", "Bassotu", "Endasak", "Endasiwet", "Ganana", "Garawja", "Gehandu", "Gendabi", "Getanuwas", "Giting", "Hidet", "Hirbadaw", "Katesh", "Lalaji", "Measkron", "Mogitu", "Nangwa", "Simbay", "Sirop", "Wareta"],
            "Mbulu": ["Ayalagaya", "Bargish", "Bashay", "Daudi", "Dinamu", "Dongobesh", "Endagikot", "Endamilay", "Gulumo", "Gunyoda", "Haydom", "Imboru", "Kainam", "Maghang", "Marang", "Mbulu Mjini", "Murray", "Nahote", "Sanu", "Tlawi", "Tumati", "Uhuru", "Yaeda Ampa", "Yaeda Chini"],
            "Simanjiro": ["Emboreet", "Endiamtu", "Endonyongijape", "Kitwai", "Komolo", "Liborsoit", "Loiborsiret", "Mirerani", "Msitu wa Tembo", "Naberera", "Naisinyai", "Ngorika", "Oljoro No. 5", "Orkesumet", "Ruvu Remiti", "Shambarai", "Terrat"],
            "Kiteto": ["Balozi", "Chapakazi", "Dongo", "Dosidosi", "Engusero", "Kibaya", "Kijungu", "Lengatei", "Loolera", "Magungu", "Makame", "Matui", "Ndedo", "Njoro", "Partimbo", "Songambele", "Sunya"],

            "Musoma Mjini": ["Bweri", "Iringo", "Kamunyonge", "Kigera", "Kitaji", "Makoko", "Mukendo", "Mwigobero", "Mwisenge", "Nyabikanu", "Nyamatare", "Nyasho", "Rwamisnyo"],
            "Musoma Vijijini": ["Bugwema", "Bukima", "Bukumi", "Buringa", "Busambara", "Busekera", "Etaro", "Ifulifu", "Kiriba", "Kugitimo", "Makojo", "Mugango", "Murangi", "Musanja", "Nyakatende", "Nyamrandirira", "Nyegina", "Rusoli", "Suguti", "Tegeruka"],
            "Tarime": ["Bumera", "Ganyange", "Gorong'a", "Itiryo", "Kemambo", "Kiore", "Komaswa", "Manga", "Matongo", "Mbogi", "Muriba", "Mwema", "Nyamongo", "Nyamwaga", "Nyansincha", "Nyarero", "Nyarokoba", "Sirari", "Susuni", "Tarime Mjini", "Turwa"],
            "Serengeti": ["Boma la Ng'ombe", "Ikoma", "Issenye", "Kebanchabancha", "Kenyamonta", "Kisangura", "Kisaka", "Kyambahi", "Machochwe", "Magange", "Majimoto", "Manchira", "Mbalibali", "Morotonga", "Mosso", "Mugumu", "Natta", "Nyamatare", "Nyamburi", "Nyamoko", "Ring'wani", "Rung'abure", "Sedeco", "Stendi Kuu", "Uwanja wa Ndege"],
            "Bunda": ["Balili", "Bunda Mjini", "Bunda Stoo", "Butimba", "Guta", "Hunzugu", "Igunsirira", "Kabasa", "Kasuguti", "Ketare", "Kibara", "Kisorya", "Kunzugu", "Manyamanyama", "Mcharo", "Mihingo", "Mugeta", "Nampindi", "Nansimo", "Neruma", "Nyabuzokana", "Nyamang'uta", "Nyamuswa", "Salama", "Sanzate", "Wariku"],
            "Rorya": ["Bukura", "Bukwe", "Goribe", "Ikoma", "Kirogo", "Komuge", "Koryo", "Kyang'ombe", "Mirare", "Nyamagaro", "Nyamtinga", "Nyathorogo", "Rabour", "Roche", "Tai", "Utegi"],
            "Butiama": ["Bisumwa", "Buhemba", "Bukabwa", "Buruma", "Buteba", "Butiama", "Bwiregi", "Kukirango", "Kyanyari", "Masaba", "Mirwa", "Muriaza", "Nyabirondo", "Nyamimange", "Nyankanga", "Sirorisimba"],

            "Mbeya Mjini": ["Forest", "Ghana", "Iduda", "Igane", "Iganzo", "Igawilo", "Ilemi", "Ilomba", "Isanga", "Isyesye", "Itagano", "Itende", "Itezi", "Itiji", "Iwambi", "Iyela", "Iyunga", "Kalobe", "Mbalizi Road", "Mabatini", "Maanga", "Majengo", "Mbalizi", "Mwakibete", "Mwanjelwa", "Mwansanga", "Nonde", "Nsalaga", "Nzovwe", "Ruanda", "Sinde", "Sisimba", "Uyole"],
            "Mbeya Vijijini": ["Bonde la Songwe", "Igoma", "Ilembo", "Ilungu", "Inyala", "Isuto", "Iswepu", "Itome", "Iwiji", "Izyira", "Lupeta", "Mjele", "Mshewe", "Santilya", "Songwe", "Tembela", "Ulenje", "Utengule Usangu"],
            "Rungwe": ["Bagamoyo", "Bulyaga", "Ibighi", "Ikuti", "Ilima", "Iponjola", "Isongole", "Itagata", "Kawetele", "Kinyala", "Kisiba", "Kiwira", "Kyimo", "Lufingo", "Lupepo", "Makandana", "Malindo", "Masoko", "Masukulu", "Matwebe", "Mpakani", "Mpuguso", "Msasani", "Ndanto", "Nkunga", "Sumbe", "Swaya", "Tukuyu Mjini"],
            "Kyela": ["Bondeni", "Bujonde", "Busale", "Ikama", "Ikolo", "Ipinda", "Ipumbu", "Itope", "Katumba Songwe", "Kyela Mjini", "Lusungo", "Makwale", "Matema", "Muungano", "Mwaya", "Ndobo", "Ngana", "Ngonga", "Njisi", "Nkokwa", "Serengeti"],
            "Chunya": ["Bwawani", "Chalangwa", "Chunya Mjini", "Gua", "Ifumbo", "Itewe", "Kambikatoto", "Kasanga", "Lualaje", "Lupa Tingatinga", "Luwalaje", "Mamba", "Matundasi", "Matwiga", "Mbangala", "Mbuyuni", "Nkung'ungu", "Sangambi", "Totowe"],
            "Mbarali": ["Chimala", "Igava", "Igurusi", "Ihahi", "Imalilosongwe", "Ipwani", "Itamboleo", "Kongolo Mswiswi", "Lugelele", "Madibira", "Mahongole", "Mawindi", "Miyombweni", "Mwatenga", "Rujewa", "Ruanda", "Ubaruku", "Utengule Usangu"],
            "Busokelo": ["Buluwa", "Isange", "Itete", "Kambasegela", "Kandete", "Kisegese", "Luanjilo", "Lupata", "Luteba", "Mpombo", "Ntaba"],

            "Morogoro Mjini": ["Bigwa", "Boma", "Chamwino", "Kichangani", "Kihonda", "Kihonda Maghorofani", "Kilakala", "Kingoluwira", "Kingwandu", "Kauzeni", "Lukobe", "Mafisa", "Magadu", "Magilisi", "Mazimbu", "Mbuyuni", "Mji Mkuu", "Mji Mpya", "Mlimani", "Mwembesongo", "Mzinga", "Sabasaba", "Sultan Area", "Tungi", "Uwanja wa Ndege", "Uwanja wa Taifa"],
            "Morogoro Vijijini": ["Bungu", "Bwakira Chini", "Bwakira Juu", "Gwata", "Kasanga", "Kibogwa", "Kibungo Juu", "Kidugalo", "Kinole", "Kiroka", "Kisaki", "Kisemu", "Kolero", "Konde", "Lundi", "Matombo", "Mavufe", "Mbezi", "Mhondo", "Mikese", "Mkulazi", "Mkuyuni", "Mngazi", "Muhoro", "Ngerengere", "Ruvu", "Selembala", "Singisa", "Tawa", "Tegetero", "Tomondo", "Tununguo"],
            "Kilosa": ["Berega", "Chanzuru", "Dumila", "Gairo", "Ilonga", "Kilangali", "Kilosa Mjini", "Kimamba A", "Kimamba B", "Kisanga", "Kitete", "Lumbiji", "Lumuma", "Mabwerebwere", "Mabula", "Madoto", "Magole", "Magubike", "Maguha", "Malolo", "Mamboya", "Masenze", "Mbumi", "Mikumi", "Mkwatani", "Msowero", "Muhenda", "Ruaha", "Rubeho", "Rudewa", "Tindiga", "Ulaya", "Uleling`ombe", "Vidunda", "Zombo"],
            "Kilombero": ["Chita", "Idete", "Ifakara", "Igima", "Kalengakelu", "Kamwene", "Katindiuka", "Kibaoni", "Kiberege", "Kidatu", "Kisawasawa", "Lipangalala", "Lumemo", "Mang'ula", "Mang'ula B", "Masawe", "Mbingu", "Mchombe", "Mkula", "Mngeta", "Msolwa Station", "Namwawala", "Sanje", "Signali", "Utengule", "Viwanja Sitini"],
            "Ulanga": ["Biro", "Chirombola", "Euga", "Iragwa", "Isongo", "Ketaketa", "Kichangani", "Lukande", "Lupiro", "Mahenge Mjini", "Mawenzi", "Mbuga", "Mwaya", "Nawenge", "Ruaha", "Sali", "Uponera", "Vigoi"],
            "Gairo": ["Chagongwe", "Chanjale", "Gairo", "Idibo", "Iyogwe", "Kibedya", "Mandege", "Msingisi", "Nongwe", "Rubeho", "Ukwamani"],
            "Mvomero": ["Bunduki", "Doma", "Hembeti", "Kanga", "Kibati", "Kikeo", "Langali", "Maskati", "Melela", "Mhonda", "Mkindo", "Mlali", "Mount Luhole", "Mvomero", "Nyandira", "Pemba", "Sungaji", "Tchenzema"],
            "Malinyi": ["Igawa", "Itete", "Malinyi", "Mtimbira", "Ngoheranga", "Sofi", "Usungule"],

            "Mtwara Mjini": ["Chikongola", "Chuno", "Jangwani", "Kisungule", "Likombe", "Magengeni", "Majengo", "Mitengo", "Mtonya", "Naliendele", "Rahaleo", "Railway", "Shangani", "Ufukoni", "Vigaeni"],
            "Mtwara Vijijini": ["Dihimba", "Kiromba", "Kitaya", "Kitunguli", "Madimba", "Mahurunga", "Mangopachanne", "Mayanga", "Mbawala", "Mkunwa", "Mpapura", "Msanga Mkuu", "Mtambaswala", "Muungano", "Nalingu", "Nanguruwe", "Ndumbwe", "Tangazo", "Ziwani"],
            "Masasi Mjini": ["Jida", "Migongo", "Mkomaindo", "Mkonona", "Mpindimbi", "Mtandi", "Mwenge", "Mwenge Mtapika", "Nyasa", "Sululu", "Temeke"],
            "Masasi": ["Chiungutwa", "Chigugu", "Chikundi", "Chikunja", "Chitote", "Lipumburu", "Lulindi", "Mbuyuni", "Mchauru", "Mlingula", "Mpanyani", "Mwena", "Namalenga", "Namajani", "Namatutwe", "Nanganga", "Nanjota", "Sindano"],
            "Nanyumbu": ["Chipuputa", "Kamundi", "Likokona", "Lumesule", "Mangaka", "Maratani", "Masuguru", "Mikangaula", "Mkonona", "Nandete", "Nanyumbu", "Napacho", "Sengenya"],
            "Newala": ["Chihangu", "Chilangala", "Chitandi", "Luchingu", "Makote", "Mcholi I", "Mcholi II", "Mdimba", "Mhichiga", "Mtonya", "Mtopwa", "Mtunguru", "Nandwahi", "Newala Mjini"],
            "Tandahimba": ["Chaume", "Chikongola", "Chingungwe", "Kitama", "Kwanyama", "Litehu", "Luagala", "Lukokoda", "Lyenje", "Mahuta", "Mchichira", "Mihambwe", "Mkonjowano", "Mkoreha", "Nanhyanga", "Nanyindwa", "Tandahimba"],

            "Nyamagana": ["Buhongwa", "Butimba", "Igogo", "Igoma", "Isamilo", "Kishili", "Luchelele", "Lwanhima", "Mabatini", "Mahina", "Mbugani", "Mikuyuni", "Mirongo", "Mkolani", "Nyamagana", "Nyamhロング", "Nyegezi", "Pamba"],
            "Ilemela": ["Bugogwa", "Buswelu", "Ilemela", "Kawekamo", "Kirumba", "Kitangiri", "Nyakato", "Nyamanoro", "Nyasaka", "Pasiansi", "Sangabuye", "Shibula"],
            "Sengerema": ["Buyagu", "Buzilasoga", "Chifunfu", "Ibisabageni", "Katunguru", "Kagunga", "Kasungamile", "Kishinda", "Mission", "Nyampande", "Nyamazugo", "Nyakaliro", "Sengerema", "Tabaruka"],
            "Misungwi": ["Buhingo", "Bwanjoro", "Fella", "Gulumungu", "Idetemya", "Igokelo", "Ilujamate", "Kanyelele", "Kasololo", "Koromije", "Lubili", "Mabuki", "Mamaye", "Misasi", "Misungwi", "Mondo", "Mwaniko", "Nhundulu", "Shilalo", "Sumbugu", "Ukiriguru", "Usagara"],
            "Magu": ["Bujashi", "Bukandwe", "Fukalo", "GOriginal", "Itumbili", "Kabita", "Kahangara", "Kisesa", "Kitongo", "Kongolo", "Luhingo", "Magu Mjini", "Nyanguge", "Nkungulu", "Nyigogo", "Shukrani", "Sukuma"],
            "Ukerewe": ["Bukanda", "Bukiko", "Bukindo", "Bukongo", "Bukuriro", "Bwiro", "Gallu", "Ilangala", "Irugwa", "Kagera", "Kagunguli", "Kakerege", "Kakukuru", "Muriti", "Murutunguru", "Nakatunguru", "Namagondo", "Namilembe", "Nansio", "Ndudurumo", "Ngoma"],
            "Kwimba": ["Bungulwa", "Bupamwa", "Fukalo", "Hungumalwa", "Igongwa", "Ilula", "Iseni", "Kikubiji", "Lyoma", "Maligisu", "Malya", "Mantare", "Mhande", "Mwabomba", "Mwagi", "Mwakilyambiti", "Mwamashimba", "Ng'hundi", "Ngulla", "Nkalalo", "Nyambiti", "Nyamilama", "Shilembo", "Sumve", "Wala"],

            "Bagamoyo": ["Dunda", "Fukayosi", "Kaole", "Kiromo", "Kerege", "Kingani", "Magomeni", "Majengo", "Makurunge", "Matimbwa", "Mlingotini", "Nia Njema", "Yombo", "Zinga"],
            "Chalinze": ["Bwilingu", "Chalinze", "Kibindu", "Kimange", "Lugoba", "Mandera", "Mboga", "Miono", "Mkange", "Msata", "Msolwa", "Pera", "Talawanda", "Ubena", "Ubenazomozi", "Vigwaza"],
            "Kibaha Mjini": ["Kongowe", "Maili Moja", "Mbwawa", "Mkuza", "Misugwi", "Pangani", "Picha ya Ndege", "Ruvu", "Tangini", "Tumbi", "Visiga"],
            "Kibaha Vijijini": ["Bokomnemele", "Dutumi", "Gwata", "Janga", "Kawawa", "Kikongo", "Kilangalanga", "Kipangege", "Kwala", "Magindu", "Mlandizi", "Mtambani", "Mtongani", "Ruvu", "Soga"],
            "Kisarawe": ["Cholesamvula", "Kazimzumbwi", "Kibuta", "Kiluvya", "Kisarawe", "Kuruhi", "Mafizi", "Maneromango", "Marumbo", "Marui", "Masaki", "Msimbu", "Mzenga", "Msanga", "Sungwi", "Vikumbulu"],
            "Mkuranga": ["Beta", "Bupu", "Dondo", "Kimanzichana", "Kisegese", "Kitomondo", "Lukanga", "Magawa", "Mbezi", "Mkuranga", "Mlongoni", "Mwandege", "Njia Nne", "Nyamato", "Panzuo", "Shungubweni", "Tambani", "Tengela", "Vianzi", "Vikindu"],
            "Rufiji": ["Chemchem", "Chumbi", "Ikwiriri", "Kibiti", "Kipugira", "Mbwambo", "Mgomba", "Mtanza Msona", "Mwaseni", "Ngorongo", "Umwe", "Utete"],
            "Mafia": ["Baleni", "Jibondo", "Kanga", "Kilindoni", "Kirongwe", "Kiegeani", "Miburani", "Ndagoni"],
            "Kibiti": ["Dimani", "Jaribu", "Kibiti", "Kiongoroni", "Mahege", "Maparoni", "Mchukwi", "Mjawa", "Mpotunju", "Mtunda", "Mwambao", "Ruaruke", "Salale"],

            "Songea Mjini": ["Bombambili", "Lizaboni", "Majengo", "Matarawe", "Matogoro", "Mfaranyaki", "Mhukuru", "Mjini", "Mletele", "Msamala", "Mshangano", "Mwengemshindo", "Ndilimalitembo", "Ruvuma", "Seed Farm", "Subira", "Tanga"],
            "Songea Vijijini": ["Gumbiro", "Kilagano", "Liganga", "Litisha", "Maposeni", "Matimira", "Mbinga Mhalule", "Mpandangindo", "Muhukuru", "Parangu", "Peramiho"],
            "Mbinga Mjini": ["Betherehemu", "Kigonsera", "Kilimani", "Lusonga", "Masumuni", "Mateka", "Mbambi", "Mbinga Mjini", "Mpepai", "Myangayanga", "Utiri"],
            "Mbinga Vijijini": ["Kigonsera", "Kihangi Mahuka", "Kilimani", "Kindimba", "Liparamba", "Litumbandyosi", "Litembo", "Lituhi", "Matiri", "Mbuji", "Mpapa", "Mikalanga", "Nyoni", "Ruanda"],
            "Nyasa": ["Chiulu", "Kigonsera", "Kilondo", "Kingerikiti", "Liuli", "Mbaha", "Mbamba Bay", "Mtipwili", "Ndongosi", "Ngumbo", "Tingi", "Upendo"],
            "Tunduru": ["Chuinga", "Kalulu", "Kidodoma", "Ligoma", "Ligunga", "Lukumbule", "Majengo", "Marumba", "Masonya", "Mchesi", "Mlingoti Magharibi", "Mlingoti Mashariki", "Mtina", "Mtonya", "Nalasi", "Namakambale", "Namasakata", "Nampungu", "Namtumbo", "Ngapa", "Tuwemacho"],
            "Namtumbo": ["Hanga", "Kitanda", "Ligera", "Luchili", "Luegu", "Lusewa", "Magazini", "Mgombasi", "Mputa", "Msindo", "Namabengo", "Namtumbo", "Rwinga", "Suluti"],

            "Shinyanga Mjini": ["Chamaguha", "Chibe", "Ibadakuli", "Ibinzamata", "Kambarage", "Kitangili", "Kizumbi", "Kolandoto", "Lubaga", "Masekelo", "Mwamalili", "Mwawaza", "Ndala", "Ndembezi", "Ngokolo", "Old Shinyanga", "Shinyanga Mjini"],
            "Shinyanga Vijijini": ["Bukene", "Didia", "Ilola", "Iselamagazi", "Itwangi", "Lyabukande", "Lyabusalu", "Masengwa", "Mwamala", "Mwantini", "Mwungondamalenga", "Pandagichiza", "Salawe", "Samuye", "Solwa", "Tinde", "Usanda", "Usule"],
            "Kahama Mjini": ["Busoka", "Isagehe", "Iyenze", "Kahama Mjini", "Kagongwa", "Kilimahewa", "Majengo", "Malunga", "Mhongolo", "Mhunguzwalo", "Mondo", "Mwendakulima", "Ngambo", "Nyahanga", "Nyasubi", "Nyihogo", "Wendele", "Zongomera"],
            "Ushetu": ["Bulungwa", "Chambo", "Chona", "Idahina", "Igunda", "Kinungu", "Kisuke", "Mapamba", "Mpunze", "Nyamilangano", "Sabasabini", "Ubagwe", "Ukune", "Ushetu", "Uyogo"],
            "Msalala": ["Bugarama", "Chela", "Ikinda", "Isaka", "Jana", "Kashishi", "Kigwena", "Lunguya", "Mega", "Mwakata", "Mwalugulu", "Mwanase", "Ngaya", "Ntobo", "Shilela"],
            "Kishapu": ["Bubiki", "Bunambiyu", "Itilima", "Kiloleli", "Kishapu", "Lagana", "Maganzo", "Masanga", "Mondo", "Mwadui Lohumbo", "Mwaweja", "Ngofila", "Seke Bugoro", "Shonghele", "Songwa", "Talaga", "Uchunga", "Ukenyenge"],

            "Bariadi Mjini": ["Bariadi", "Bunamhala", "Guduwi", "Isanga", "Malambo", "Matongo", "Mhango", "Nkololo", "Nyakabindi", "Nyalikungu", "Sima", "Somanda"],
            "Bariadi Vijijini": ["Bariadi", "Chinamili", "Dutwa", "Gambosi", "Gilya", "Kasoli", "Kilalo", "Mhango", "Mwadobana", "Mwaswale", "Nkololo", "Nyakabindi", "Sakwe", "Sapiwi", "Zagayu"],
            "Maswa": ["Badi", "Binza", "Buchambi", "Budekwa", "Busangi", "Dakama", "Ipililo", "Isanga", "Jija", "Kadoto", "Kulimi", "Lalambo", "Malampaka", "Masela", "Mataba", "Mbaragane", "Mpindo", "Mwabuzo", "Mwamashimba", "Mwang'honoli", "Ng'wigwa", "Nyabubinza", "Nyalikungu", "Seng'wa", "Shanwa", "Shishiyu", "Sola", "Sukuma", "Zanzui"],
            "Meatu": ["Bukundi", "Imalaseko", "Isengwa", "Itinje", "Kimali", "Kisesa", "Lingeka", "Lubiga", "Mabuhima", "Mbuga", "Mbutu", "Mwakisandu", "Mwamalole", "Mwamanimba", "Mwamanongu", "Mwamishali", "Mwandoya", "Mwangundo", "Mwanhuzi", "Mwanjolo", "Mwashata", "Mwisi", "Ngasamo", "Nkoma", "Sakasaka"],
            "Itilima": ["Budalabujiga", "Bumera", "Chinamili", "Gaswa", "Ikindu", "Kinang'weli", "Lagangabilili", "Lugulu", "Mbita", "Mhunze", "Migato", "Mwalushu", "Mwamapalala", "Mwamtani", "Nangale", "Nkoma", "Sawida", "Zagayu"],
            "Busega": ["Badugu", "Bukinanyanja", "Kabita", "Kalemela", "Kiloleli", "Lamadi", "Lutubiga", "Malili", "Mwamanyili", "Mkula", "Ngasamo", "Nyaluhande", "Nyamikoma", "Shigala"],

            "Tabora Mjini": ["Chemchem", "Cheyo", "Gongoni", "Ifucha", "Isevya", "Itetemia", "Itonjanda", "Kabila", "Kakola", "Kalunde", "Kanyenye", "Kitete", "Maili Saba", "Malolo", "Mbugani", "Misha", "Mpembya", "Mtendeni", "Ndevelwa", "Ng'ambo", "Ntalikwa", "Tambukareli", "Tumbi", "Uyui"],
            "Uyui": ["Bukumbi", "Goweko", "Ibiri", "Igalula", "Igisi", "Ikongolo", "Ilolangulu", "Isikizya", "Kigwa", "Lojwale", "Magiri", "Miswaki", "Miyenze", "Ndono", "Nsimbo", "Shitage", "Tura", "Upuge", "Ufuluma"],
            "Nzega Mjini": ["Kitongo", "Mbogwe", "Mubale", "Nzega Mjini", "Nzega Magharibi", "Nzega Mashariki", "Old Nzega", "Uchama"],
            "Nzega": ["Bukene", "Ikindwa", "Itobo", "Karitu", "Lusu", "Magengati", "Mambali", "Miguwa", "Mizibaziba", "Mwakashanhala", "Nkiniziwa", "Puge", "Semembwe", "Tabora Mndimu", "Tongoni", "Utupubu", "Wela", "Ziba"],
            "Igunga": ["Choma", "Igunga", "Igurubi", "Itumba", "Kinungu", "Kining'inila", "Mbutu", "Mwisi", "Nanga", "Ndembezi", "Ngulu", "Nkinga", "Nyandekwa", "Simbo", "Sungwizi", "Ziba"],
            "Sikonge": ["Chabutwa", "Igigwa", "Ipole", "Kiloleli", "Kiloli", "Kipanga", "Kipili", "Kisanga", "Kitunda", "Misheni", "Mole", "Mpombwe", "Ngoywa", "Pangale", "Sikonge", "Tutuo"],
            "Urambo": ["Capital", "Kasisi", "Kiloleni", "Muungano", "Nsenda", "Songambele", "Ugalla", "Ukondamoyo", "Urambo", "Usisya", "Ussoke", "Uyumbu", "Vumilia"],
            "Kaliua": ["Ichemba", "Igombemkulu", "Igwisi", "Ilyanghulu", "Kanindo", "Kanoge", "Kashishi", "Kazaroho", "Milambo", "Mwongozo", "Silambo", "Ugala", "Ukumbi Siganga", "Ulyankulu", "Usenye", "Usinge", "Uyowa", "Zugimlole"],

            "Tanga Mjini": ["Central", "Chumbageni", "Duga", "Kiomoni", "Kirare", "Mabawa", "Mabokweni", "Majengo", "Makorora", "Marungu", "Maweni", "Mazingara", "Mnyanjani", "Msambweni", "Mwanzange", "Ngamiani Kaskazini", "Ngamiani Kati", "Ngamiani Kusini", "Nguvumali", "Pongwe", "Tangasisi", "Tongoni", "Usagara"],
            "Muheza": ["Amani", "Bwembwera", "Genge", "Kigombe", "Kilulu", "Kisiwani", "Kwemkabala", "Lusanga", "Magila", "Majengo", "Maramba", "Masuguru", "Mlingoni", "Mtimbwani", "Mpapayu", "Ngomeni", "Nkumba", "Potwe", "Songa", "Tongwe", "Zirai"],
            "Korogwe Mjini": ["Bagamoyo", "Kwamsisi", "Kizara", "Magunga", "Majengo", "Manundu", "Mashewa", "Mgombezi", "Mtonga", "Old Korogwe"],
            "Korogwe Vijijini": ["Bungu", "Chekelei", "Dindira", "Kizara", "Kwagunda", "Kwashemshi", "Lutindi", "Magamba Kwalukonge", "Makuyuni", "Mombo", "Mpale", "Mnyuzi", "Vugiri"],
            "Lushoto": ["Bumbuli", "Dule 'M'", "Gare", "Kwai", "Kwemboma", "Lushoto", "Magamba", "Makanya", "Malibwi", "Malindi", "Mbaramo", "Mbaru", "Mbuzii", "Migambo", "Mlungui", "Mnazi", "Mng'aro", "Mombo", "Ngwelo", "Rangwi", "Shume", "Soni", "Ubiri", "Usambara"],
            "Mkinga": ["Boma", "Bosha", "Doda", "Duga", "Gomani", "Kigongoi", "Kwanyange", "Mkinga", "Moa", "Mtimbwani", "Paramba", "Sigaya"],
            "Pangani": ["Bweni", "Bushiri", "Kimang'a", "Kipumbwi", "Madanga", "Masaika", "Mwera", "Pangani Magharibi", "Pangani Mashariki", "Tungamaa", "Ubungo"],
            "Handeni Mjini": ["Chanika", "Kideleko", "Konje", "Kwamgwe", "Kwediyamba", "Mabanda", "Malezi", "Mdoe", "Mlimani", "Msikitini", "Vibaoni"],
            "Handeni Vijijini": ["Kabuku", "Kabuku Ndani", "Kang'ata", "Kiva", "Komatangi", "Kwale", "Kwasunga", "Kwedizinga", "Mazingara", "Mkata", "Misufini", "Ndolwa", "Segera", "Sindeni"],
            "Bumbuli": ["Baga", "Bumbuli", "Dule B", "Funta", "Kizara", "Kwemashai", "Mamba", "Mayo", "Mbuzii", "Mgwashi", "Mponde", "Nkoo", "Soni", "Tamota", "Usambara", "Vuga"],
            "Kilindi": ["Jangalo", "Kikunde", "Kilindi", "Kimbe", "Kivilindi", "Kwediboma", "Kwekivu", "Lulago", "Masagalu", "Mvungwe", "Negero", "Pagwi", "Saunyi", "Songe", "Tunguli"],

            "Mbozi": ["Bara", "Halungu", "Hasamba", "Igamba", "Ihanda", "Ilolo", "Isansa", "Itaka", "Iyula", "Kamsamba", "Kilondo", "Mlangali", "Mlowo", "Msia", "Nambinzo", "Ndalambo", "Nzyuka", "Ruanda", "Shiwinga", "Vwawa"],
            "Momba": ["Chitete", "Kamsamba", "Kapele", "Mkulwe", "Msangano", "Myunga", "Ndole", "Ndalambo", "Nzoka"],
            "Tunduma": ["Chapwa", "Chiwele", "Kaloleni", "Katete", "Majengo", "Mpemba", "Mwaka", "Nalwigo", "Tunduma", "Uwanja wa Ndege"],
            "Ileje": ["Bupigu", "Chitete", "Ibaba", "Ikinga", "Isongole", "Itumba", "Kafule", "Lubanda", "Lusafi", "Malangali", "Mlale", "Ndola", "Ngulilo", "Ngulugulu"],
            "Songwe": ["Gua", "Ifumbo", "Kapalala", "Kanga", "Mbangala", "Mbuyuni", "Mkwajuni", "Mwambani", "Namkukwe", "Ngwala", "Saza", "Totowe"],

            "Kaskazini A": ["Bandamaji", "Chaani Kubwa", "Chaani Masingini", "Gambagamba", "Gomani", "Kandwi", "Kibeni", "Kidoti", "Kijini", "Kinyasini", "Matemwe", "Mkwajuni", "Nungwi", "Pitanazako", "Tumbatu Gomani", "Tumbatu Jongowe"],
            "Kaskazini B": ["Donge Karange", "Donge Kipange", "Donge Mbiji", "Donge Mnyimbi", "Fujoni", "Kinduni", "Kitope", "Kiwengwa", "Mahonda", "Majenzi", "Makoba", "Misufini", "Mkadini", "Pangeni", "Upenja", "Zingwezingwe"],
            "Kusini": ["Bwejuu", "Dongwe", "Jambiani Kibigija", "Jambiani Kikadini", "Kajengwa", "Kibuteni", "Kijini", "Kizimkazi Dimbani", "Kizimkazi Mkunguni", "Makunduchi", "Muungoni", "Muyuni A", "Muyuni B", "Muyuni C", "Paje"],
            "Kati": ["Bambi", "Binguni", "Bungi", "Charawe", "Cheju", "Chwaka", "Dunga Bweni", "Dunga Kiembeni", "Jendele", "Jumbi", "Kiboje", "Marumbi", "Mitakawani", "Mpapa", "Ndijani Mseweni", "Ndijani Mwembepunda", "Tunguu", "Ukongoroni", "Uroa", "Uzi", "Uzini"],
            "Mjini": ["Amani", "Chumbuni", "Gulioni", "Jang'ombe", "Karakana", "Kikwajuni Bondeni", "Kikwajuni Juu", "Kilimahewa", "Kilimani", "Kiponda", "Kisima Majongoo", "Kwaalamsha", "Kwamtipura", "Magomeni", "Malindi", "Matembwe", "Mbuyuni", "Mchangani", "Miembeni", "Mikunguni", "Mkele", "Mkunazini", "Mlandege", "Mnazi Mmoja", "Muembeladu", "Muungano", "Mwembemakumbi", "Mwembetanga", "Nyerere", "Rahaleo", "Sebleni", "Shangani", "Shaurimoyo", "Urusi", "Vikokotoni"],
            "Magharibi A": ["Bububu", "Chuini", "Kibweni", "Kijichi", "Kinuni", "Mbweni", "Mfenesini", "Mtoni", "Mwera", "Sharifu Msa", "Welezo"],
            "Magharibi B": ["Chukwani", "Dimani", "Fumba", "Kombeni", "Kisauni", "Maungani", "Mombasa", "Nyamanzi", "Pangawe", "Shakani", "Tomondo"],
            "Wete": ["Bopwe", "Chamboni", "Fundo", "Gando", "Jadida", "Junguni", "Kambini", "Kangagani", "Kipangani", "Kizimbani", "Limbani", "Mchanga Mdogo", "Mjimbini", "Mtambwe Kaskazini", "Mtambwe Kusini", "Pandani", "Pembeni", "Piki", "Shengejuu", "Utaani"],
            "Micheweni": ["Chambani", "Kifundi", "Kinowe", "Kipange", "Konde", "Majenzi", "Makangale", "Maziwa Ng'ombe", "Micheweni", "Msuka", "Shumba Mjini", "Tumbe Mashariki", "Tumbe Magharibi", "Wingwi Mapofu", "Wingwi Mjananza", "Wingwi Njuguni"],
            "Chake Chake": ["Chachani", "Chambani", "Chanjaani", "Chonga", "Dodo", "Gombani", "Kichungwani", "Kilindi", "Kiwani", "Madungu", "Matale", "Mchanga Mdogo", "Mfikiwa", "Mkoroshoni", "Msingini", "Ng'ambwa", "Pujini", "Ruvu", "Shungi", "Tibirinzi", "Vitongoji", "Wawi", "Ziwani"],
            "Mkoani": ["Chambani", "Chokocho", "Chwaka", "Kangani", "Kengeja", "Kisiwa Panza", "Kiombamvua", "Kiwani", "Michenzani", "Mkoani", "Mtambile", "Mwambe", "Ngombeni", "Shidi", "Stahabu", "Uweleni", "Wambaa"]
        };
    </script>

    @php
        $docsMap = [];
        if (isset($application) && $application->documents) {
            foreach ($application->documents as $doc) {
                $docsMap[$doc->document_type] = [
                    'name' => $doc->original_filename,
                    'url' => asset('storage/' . $doc->file_path),
                    'status' => true,
                    'verification_status' => $doc->verification_status,
                    'rejection_comment' => $doc->rejection_comment,
                ];
            }
        }

        $form4Doc = $docsMap['csee_certificate'] ?? null;
        
        $admType = $application->admission_type ?? 'Diploma';
        $form6DipDoc = ($admType === 'Form Six') ? ($docsMap['acsee_certificate'] ?? null) : ($docsMap['diploma_certificate'] ?? null);
        
        $transcriptDoc = $docsMap['transcript'] ?? null;
        $nidaDoc = $docsMap['nida_id'] ?? null;
        
        $passportDoc = $docsMap['passport'] ?? null;
        if (!$passportDoc && isset($user->applicant) && $user->applicant->passport_photo_path) {
            $passportDoc = [
                'name' => basename($user->applicant->passport_photo_path),
                'url' => asset('storage/' . $user->applicant->passport_photo_path),
                'status' => true,
                'verification_status' => 'pending',
                'rejection_comment' => '',
            ];
        }
        
        $receiptDoc = null;
        if (isset($application->payment) && $application->payment->receipt_path) {
            $receiptDoc = [
                'name' => basename($application->payment->receipt_path),
                'url' => asset('storage/' . $application->payment->receipt_path),
                'status' => true,
                'verification_status' => 'pending',
                'rejection_comment' => '',
            ];
        }

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();
        $consentRequired = ($activePolicy || $activeTerms);
        $hasConsented = isset($user->applicant) && $user->applicant->initial_consent_given;
        $consentGivenVal = (!$consentRequired || $hasConsented) ? 'true' : 'false';
        $defaultStep = $application->current_step ?? 1;
        $currentStepVal = (!$consentRequired || $hasConsented) ? request()->get('step', $defaultStep) : 1;

        $existingDiploma = $application?->academicProfile?->diploma_programme_name ?? '';
        $standardDiplomas = [
            'Stashahada ya Elimu ya Msingi',
            'Stashahada ya Elimu ya Awali',
            'Diploma in Primary Education (DPEE)',
            'Diploma in Pre Primary Education (DPPE)',
            'Diploma in Secondary Education (DSED)',
            'Diploma in Adult Education (DAE)',
        ];

        if (empty($existingDiploma)) {
            $selectedDiplomaType = '';
            $customDiplomaName = '';
        } elseif (in_array($existingDiploma, $standardDiplomas, true)) {
            $selectedDiplomaType = $existingDiploma;
            $customDiplomaName = '';
        } else {
            $selectedDiplomaType = 'Other Diploma';
            $customDiplomaName = $existingDiploma;
        }
    @endphp

    <script>
        function applicationWizardData() {
            return {
                currentStep: {{ $currentStepVal }},
                maxSteps: 7,
                applicationId: {{ $application->id ?? 'null' }},
                isGuest: {{ (session('guest_user_id') == ($user->id ?? null) || \Illuminate\Support\Str::contains($user->email ?? '', '@supa-guest.com')) ? 'true' : 'false' }},
                confirmAccurate: false,
                readPrivacy: false,
                readTerms: false,
                consentGiven: {{ $consentGivenVal }},
                understandPenalty: false,
                privacyPolicyVersion: '2.1',
                termsVersion: '2.1',
                parentConsentGiven: false,
                parentName: '',
                parentSignature: '',
                parentConsentDate: {!! json_encode(now()->toDateString()) !!},
                showClaimAccountForm: false,
                guestCredentials: {
                    name: {!! json_encode(($user && !\Illuminate\Support\Str::contains($user->email ?? '', '@supa-guest.com')) ? $user->name : '') !!},
                    email: {!! json_encode(($user && !\Illuminate\Support\Str::contains($user->email ?? '', '@supa-guest.com')) ? $user->email : '') !!},
                    phone: {!! json_encode(($user && !\Illuminate\Support\Str::contains($user->email ?? '', '@supa-guest.com')) ? $user->phone : '') !!},
                    whatsapp_number: {!! json_encode($user->applicant?->whatsapp_number ?? '') !!}
                },
                claimPassword: {
                    password: '',
                    password_confirmation: ''
                },
                dob_day: '',
                dob_month: '',
                dob_year: '',
                personal: {
                    gender: {!! json_encode($user->applicant?->gender ?? 'male') !!},
                    date_of_birth: {!! json_encode(optional($user->applicant?->date_of_birth)->toDateString() ?? '') !!},
                    nida_number: {!! json_encode($user->applicant?->nida_number ?? '') !!},
                    voter_id_number: {!! json_encode($user->applicant?->voter_id_number ?? '') !!},
                    work_id_number: {!! json_encode($user->applicant?->work_id_number ?? '') !!},
                    whatsapp_number: {!! json_encode($user->applicant?->whatsapp_number ?? '') !!},
                    region: {!! json_encode($user->applicant?->region ?? '') !!},
                    district: {!! json_encode($user->applicant?->district ?? '') !!},
                    ward: {!! json_encode($user->applicant?->ward ?? '') !!},
                    next_of_kin_name: {!! json_encode($user->applicant?->next_of_kin_name ?? '') !!},
                    next_of_kin_phone: {!! json_encode($user->applicant?->next_of_kin_phone ?? '') !!},
                    next_of_kin_relation: {!! json_encode($user->applicant?->next_of_kin_relation ?? '') !!}
                },
                idType: {!! json_encode(!empty($user->applicant?->work_id_number) ? 'work' : (!empty($user->applicant?->voter_id_number) ? 'voter' : 'nida')) !!},
                idNumber: {!! json_encode($user->applicant?->work_id_number ?? ($user->applicant?->voter_id_number ?? ($user->applicant?->nida_number ?? ''))) !!},
                onIdTypeChange() {
                    if (this.idType === 'nida') {
                        this.idNumber = this.personal.nida_number || '';
                    } else if (this.idType === 'voter') {
                        this.idNumber = this.personal.voter_id_number || '';
                    } else if (this.idType === 'work') {
                        this.idNumber = this.personal.work_id_number || '';
                    }
                },
                selectedDiplomaType: {!! json_encode($selectedDiplomaType) !!},
                customDiplomaName: {!! json_encode($customDiplomaName) !!},
                academic: {
                    admission_type: {!! json_encode($application?->admission_type ?? 'Diploma') !!},
                    programme_id: {{ request()->query('programme_id') ? (int)request()->query('programme_id') : ($application?->programme_id ?? ($programmes->first()->id ?? 'null')) }},
                    academic_year_id: {{ $application?->academic_year_id ?? ($academicYears->first()->id ?? 'null') }},
                    intake_id: {{ $application?->intake_id ?? ($intakes->first()->id ?? 'null') }},
                    college_name: {!! json_encode($application?->academicProfile?->college_name ?? '') !!},
                    diploma_programme_name: {!! json_encode($application?->academicProfile?->diploma_programme_name ?? '') !!},
                    diploma_registration_number: {!! json_encode($application?->academicProfile?->diploma_registration_number ?? '') !!},
                    diploma_graduation_year: {!! json_encode((string)($application?->academicProfile?->diploma_graduation_year ?? '')) !!},
                    gpa: {!! json_encode((string)($application?->academicProfile?->gpa ?? '')) !!},
                    csee_number: {!! json_encode($application?->academicProfile?->csee_number ?? '') !!},
                    csee_year: {!! json_encode((string)($application?->academicProfile?->csee_year ?? '')) !!},
                    csee_school: {!! json_encode($application?->academicProfile?->csee_school ?? '') !!},
                    acsee_number: {!! json_encode($application?->academicProfile?->acsee_number ?? '') !!},
                    acsee_year: {!! json_encode((string)($application?->academicProfile?->acsee_year ?? '')) !!},
                    acsee_school: {!! json_encode($application?->academicProfile?->acsee_school ?? '') !!},
                    acsee_combination: {!! json_encode($application?->academicProfile?->acsee_combination ?? '') !!},
                    acsee_subject1: {!! json_encode($application?->academicProfile?->acsee_subject1 ?? '') !!},
                    acsee_subject2: {!! json_encode($application?->academicProfile?->acsee_subject2 ?? '') !!},
                    acsee_subject3: {!! json_encode($application?->academicProfile?->acsee_subject3 ?? '') !!},
                    acsee_grade1: {!! json_encode($application?->academicProfile?->acsee_grade1 ?? '') !!},
                    acsee_grade2: {!! json_encode($application?->academicProfile?->acsee_grade2 ?? '') !!},
                    acsee_grade3: {!! json_encode($application?->academicProfile?->acsee_grade3 ?? '') !!},
                    acsee_gs_grade: {!! json_encode($application?->academicProfile?->acsee_gs_grade ?? '') !!},
                    acsee_points: {!! json_encode($application?->academicProfile?->acsee_points ?? '') !!}
                },
                scoreType: 'gpa',
                selectedGrade: '',
                setScoreType(type) {
                    this.scoreType = type;
                    if (type === 'grade') {
                        const val = parseFloat(this.academic.gpa);
                        if (!isNaN(val)) {
                            if (val >= 4.4) this.selectedGrade = '4.50';
                            else if (val >= 3.5) this.selectedGrade = '3.80';
                            else if (val >= 3.0) this.selectedGrade = '3.20';
                            else if (val >= 2.0) this.selectedGrade = '2.50';
                            else this.selectedGrade = '1.80';
                        }
                    }
                },
                acseeCombinations: {
                    'PCM': ['Physics', 'Chemistry', 'Mathematics'],
                    'PCB': ['Physics', 'Chemistry', 'Biology'],
                    'CBG': ['Chemistry', 'Biology', 'Geography'],
                    'HGL': ['History', 'Geography', 'Language'],
                    'HKL': ['History', 'Kiswahili', 'Language'],
                    'ECA': ['Economics', 'Commerce', 'Accountancy'],
                    'EGM': ['Economics', 'Geography', 'Mathematics'],
                    'HGE': ['History', 'Geography', 'Economics'],
                    'HGK': ['History', 'Geography', 'Kiswahili'],
                    'PGM': ['Physics', 'Geography', 'Mathematics'],
                    'CBA': ['Chemistry', 'Biology', 'Agriculture'],
                    'CBN': ['Chemistry', 'Biology', 'Nutrition'],
                    'KLF': ['Kiswahili', 'Language', 'French']
                },
                getGradePoint(grade) {
                    if (!grade) return '';
                    const pointsMap = {
                        'A': 1, 'B': 2, 'C': 3, 'D': 4, 'E': 5, 'S': 6, 'F': 7
                    };
                    return pointsMap[grade.toUpperCase()] || '';
                },
                calculatedCategory: {!! json_encode($application?->admission_category ?? 'Direct Entry') !!},
                programmes: {!! json_encode($programmes) !!},
                payment: {
                    id: {!! json_encode($application?->payment?->id ?? '') !!},
                    control_number: {!! json_encode($application?->payment?->control_number ?? '') !!},
                    status: {!! json_encode($application?->payment?->payment_status ?? 'pending') !!},
                    transaction_reference: {!! json_encode($application?->payment?->transaction_reference ?? '') !!},
                    receipt_url: {!! json_encode($application?->payment?->receipt_path ? asset('storage/' . $application->payment->receipt_path) : '') !!},
                    singida_synced: {{ ($application?->payment?->singida_synced ?? false) ? 'true' : 'false' }},
                    rejection_reason: {!! json_encode($application?->payment?->rejection_reason ?? '') !!}
                },
                requestingControlNumber: false,
                checkingPaymentStatus: false,
                paymentPollingTimer: null,

                init() {
                    this.initDobFields();
                    this.$watch('dob_day', () => this.updateDobString());
                    this.$watch('dob_month', () => this.updateDobString());
                    this.$watch('dob_year', () => this.updateDobString());

                    this.$watch('currentStep', (newStep) => {
                        if (newStep === 6) {
                            this.startPaymentAutoDetect();
                        } else {
                            this.stopPaymentAutoDetect();
                        }
                    });

                    if (this.currentStep === 6) {
                        this.startPaymentAutoDetect();
                    }
                },

                initDobFields() {
                    if (this.personal && this.personal.date_of_birth) {
                        const parts = this.personal.date_of_birth.split('-');
                        if (parts.length === 3) {
                            this.dob_year = String(parseInt(parts[0], 10));
                            this.dob_month = String(parseInt(parts[1], 10));
                            this.dob_day = String(parseInt(parts[2], 10));
                        }
                    }
                },

                updateDobString() {
                    const day = this.dob_day || (this.$refs && this.$refs.dobDay ? this.$refs.dobDay.value : '');
                    const month = this.dob_month || (this.$refs && this.$refs.dobMonth ? this.$refs.dobMonth.value : '');
                    const year = this.dob_year || (this.$refs && this.$refs.dobYear ? this.$refs.dobYear.value : '');

                    if (year && month && day) {
                        const pad = (num) => num.toString().padStart(2, '0');
                        this.personal.date_of_birth = `${year}-${pad(month)}-${pad(day)}`;
                    } else {
                        this.personal.date_of_birth = '';
                    }
                },

                startPaymentAutoDetect() {
                    this.stopPaymentAutoDetect();
                    if (this.payment.status === 'paid') return;

                    if (this.needsSingidaControlNumber()) {
                        this.requestControlNumber(false);
                    }

                    // Immediate background check
                    this.checkPaymentStatus(false);

                    // Auto-poll status every 4 seconds while on Step 6
                    this.paymentPollingTimer = setInterval(() => {
                        if (this.currentStep !== 6 || this.payment.status === 'paid') {
                            this.stopPaymentAutoDetect();
                            return;
                        }
                        this.checkPaymentStatus(false);
                    }, 4000);
                },

                stopPaymentAutoDetect() {
                    if (this.paymentPollingTimer) {
                        clearInterval(this.paymentPollingTimer);
                        this.paymentPollingTimer = null;
                    }
                },

                needsSingidaControlNumber() {
                    const cn = String(this.payment.control_number || '');
                    if (!cn) return true;
                    if (cn.startsWith('PENDING-') || cn.startsWith('99100')) return true;
                    return !this.payment.singida_synced;
                },

                async requestControlNumber(force = false) {
                    this.requestingControlNumber = true;
                    try {
                        const res = await axios.post('{{ url('/applicant/request-control-number') }}', { force: force ? 1 : 0 });
                        if (res.data.payment) {
                            this.payment.id = res.data.payment.id;
                            this.payment.control_number = res.data.payment.control_number;
                            this.payment.status = res.data.payment.payment_status;
                            this.payment.singida_synced = !!res.data.payment.singida_synced;
                        }
                        toast(res.data.message || 'Control number ready.', 'success');
                    } catch (err) {
                        const msg = err.response?.data?.message || 'Failed to get control number from Singida.';
                        toast(msg, 'error');
                    } finally {
                        this.requestingControlNumber = false;
                    }
                },

                async checkPaymentStatus(showToast = true) {
                    this.checkingPaymentStatus = true;
                    try {
                        const res = await axios.get('{{ url('/applicant/payment-status') }}');
                        if (res.data.payment) {
                            const prevStatus = this.payment.status;
                            this.payment.id = res.data.payment.id;
                            this.payment.control_number = res.data.payment.control_number;
                            this.payment.status = res.data.payment.status;
                            this.payment.transaction_reference = res.data.payment.transaction_reference;
                            this.payment.singida_synced = !!res.data.payment.singida_synced;
                            this.payment.rejection_reason = res.data.payment.rejection_reason || '';

                            if (this.payment.status === 'paid') {
                                this.stopPaymentAutoDetect();
                                toast('🎉 Hongera! Malipo yako ya TZS 20,000 yamethibitishwa kikamilifu! Unaelekezwa kwenye hatua ya vyeti...', 'success');
                                if (this.currentStep === 6) {
                                    setTimeout(() => {
                                        this.currentStep = 5;
                                    }, 1000);
                                }
                            } else if (showToast) {
                                toast('Bado malipo hayajakamilika. Fanya malipo kupitia NMB au Simu ya Mkononi kisha mfumo utatambua kiotomatiki.', 'info');
                            }
                        }
                    } catch (err) {
                        if (showToast) {
                            const msg = err.response?.data?.message || 'Haikuweza kupata hali ya malipo kwa sasa.';
                            toast(msg, 'error');
                        }
                    } finally {
                        this.checkingPaymentStatus = false;
                    }
                },

                goToStep(step) {
                    if (step > 1 && !this.consentGiven) {
                        toast('Tafadhali ridhia fomu ya ridhaa ya udahili ili kuendelea.', 'error');
                        this.currentStep = 1;
                        return;
                    }
                    if (this.payment.status !== 'paid') {
                        if (step === 5 || step === 7) {
                            toast('Tafadhali kamilisha malipo ya ada ya fomu kwanza. (Please pay the admission fee first.)', 'error');
                            return;
                        }
                    }
                    this.currentStep = step;
                },
                checklist: {
                    form4: {{ $form4Doc ? 'true' : 'false' }},
                    form6_diploma: {{ $form6DipDoc ? 'true' : 'false' }},
                    transcript: {{ $transcriptDoc ? 'true' : 'false' }},
                    nida_id: {{ $nidaDoc ? 'true' : 'false' }},
                    passport_photos: {{ $passportDoc ? 'true' : 'false' }}
                },
                uploadedDocs: {
                    form4: { 
                        name: '{{ $form4Doc['name'] ?? '' }}', 
                        url: '{{ $form4Doc['url'] ?? '' }}', 
                        status: {{ $form4Doc ? 'true' : 'false' }},
                        verification_status: '{{ $form4Doc['verification_status'] ?? 'pending' }}',
                        rejection_comment: {!! json_encode($form4Doc['rejection_comment'] ?? '') !!}
                    },
                    form6_diploma: { 
                        name: '{{ $form6DipDoc['name'] ?? '' }}', 
                        url: '{{ $form6DipDoc['url'] ?? '' }}', 
                        status: {{ $form6DipDoc ? 'true' : 'false' }},
                        verification_status: '{{ $form6DipDoc['verification_status'] ?? 'pending' }}',
                        rejection_comment: {!! json_encode($form6DipDoc['rejection_comment'] ?? '') !!}
                    },
                    transcript: { 
                        name: '{{ $transcriptDoc['name'] ?? '' }}', 
                        url: '{{ $transcriptDoc['url'] ?? '' }}', 
                        status: {{ $transcriptDoc ? 'true' : 'false' }},
                        verification_status: '{{ $transcriptDoc['verification_status'] ?? 'pending' }}',
                        rejection_comment: {!! json_encode($transcriptDoc['rejection_comment'] ?? '') !!}
                    },
                    nida_id: { 
                        name: '{{ $nidaDoc['name'] ?? '' }}', 
                        url: '{{ $nidaDoc['url'] ?? '' }}', 
                        status: {{ $nidaDoc ? 'true' : 'false' }},
                        verification_status: '{{ $nidaDoc['verification_status'] ?? 'pending' }}',
                        rejection_comment: {!! json_encode($nidaDoc['rejection_comment'] ?? '') !!}
                    },
                    passport_photos: { 
                        name: '{{ $passportDoc['name'] ?? '' }}', 
                        url: '{{ $passportDoc['url'] ?? '' }}', 
                        status: {{ $passportDoc ? 'true' : 'false' }},
                        verification_status: '{{ $passportDoc['verification_status'] ?? 'pending' }}',
                        rejection_comment: {!! json_encode($passportDoc['rejection_comment'] ?? '') !!}
                    }
                },
                previewDocModal: false,
                activePreviewDoc: { title: '', name: '', url: '' },

                handleDocUpload(event, key) {
                    const file = event.target.files[0];
                    if (!file) return;

                    let docType = '';
                    if (key === 'form4') {
                        docType = 'csee_certificate';
                    } else if (key === 'form6_diploma') {
                        docType = this.academic.admission_type === 'Form Six' ? 'acsee_certificate' : 'diploma_certificate';
                    } else if (key === 'transcript') {
                        docType = 'transcript';
                    } else if (key === 'nida_id') {
                        docType = 'nida_id';
                    } else if (key === 'passport_photos') {
                        docType = 'passport';
                    }

                    if (!docType) {
                        toast('Invalid document type.', 'error');
                        return;
                    }

                    let formData = new FormData();
                    formData.append('document_type', docType);
                    formData.append('document', file);

                    let url = '{{ url('/applicant/upload-document') }}';
                    
                    axios.post(url, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    })
                    .then(res => {
                        this.uploadedDocs[key].name = file.name;
                        if (res.data.document && res.data.document.file_path) {
                            this.uploadedDocs[key].url = '{{ asset('storage') }}/' + res.data.document.file_path;
                        } else {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.uploadedDocs[key].url = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                        this.uploadedDocs[key].status = true;
                        this.uploadedDocs[key].verification_status = 'pending';
                        this.uploadedDocs[key].rejection_comment = '';
                        this.checklist[key] = true;

                        toast(file.name + ' uploaded successfully!', 'success');
                    })
                    .catch(err => {
                        const errorMsg = err.response?.data?.message || 'Error uploading document.';
                        toast(errorMsg, 'error');
                    });
                },

                openPreviewDoc(title, docObj) {
                    this.activePreviewDoc = { title: title, name: docObj.name, url: docObj.url };
                    this.previewDocModal = true;
                },

                signatureData: {!! json_encode($user->name ?? '') !!},
                loading: false,
                errorMsg: '',
                
                savePersonal() {
                    this.updateDobString();
                    const trimmedId = (this.idNumber || '').trim();
                    if (!trimmedId) {
                        const label = this.idType === 'nida' ? 'Kitambulisho cha NIDA' : (this.idType === 'voter' ? 'Kitambulisho cha Kura' : 'Kitambulisho cha Kazi');
                        toast('Tafadhali jaza namba ya ' + label + ' ili kuendelea.', 'error');
                        this.errorMsg = 'Tafadhali jaza namba ya ' + label + ' ili kuendelea.';
                        return;
                    }

                    if (this.idType === 'nida') {
                        this.personal.nida_number = trimmedId;
                        this.personal.voter_id_number = '';
                        this.personal.work_id_number = '';
                    } else if (this.idType === 'voter') {
                        this.personal.voter_id_number = trimmedId;
                        this.personal.nida_number = '';
                        this.personal.work_id_number = '';
                    } else if (this.idType === 'work') {
                        this.personal.work_id_number = trimmedId;
                        this.personal.nida_number = '';
                        this.personal.voter_id_number = '';
                    }

                    this.loading = true;
                    this.errorMsg = '';
                    axios.post('{{ url('/applicant/personal-info') }}', this.personal)
                        .then(res => {
                            this.loading = false;
                            toast('Personal information saved successfully!', 'success');
                            this.currentStep = 3;
                        })
                        .catch(err => {
                            this.loading = false;
                            const msg = err.response?.data?.message || 'Error saving personal info';
                            this.errorMsg = msg;
                            toast(msg, 'error');
                        });
                },

                saveAcademic() {
                    if (this.academic.admission_type === 'Diploma') {
                        if (this.selectedDiplomaType === 'Other Diploma') {
                            this.academic.diploma_programme_name = this.customDiplomaName.trim() || 'Other Diploma';
                        } else if (this.selectedDiplomaType) {
                            this.academic.diploma_programme_name = this.selectedDiplomaType;
                        }
                    }
                    this.loading = true;
                    this.errorMsg = '';
                    axios.post('{{ url('/applicant/academic-profile') }}', this.academic)
                        .then(res => {
                            this.loading = false;
                            this.calculatedCategory = res.data.admission_category;
                            this.applicationId = res.data.application.id;
                            if (res.data.application.payment) {
                                this.payment.id = res.data.application.payment.id;
                                this.payment.control_number = res.data.application.payment.control_number;
                                this.payment.status = res.data.application.payment.payment_status;
                                this.payment.transaction_reference = res.data.application.payment.transaction_reference;
                                this.payment.receipt_url = res.data.application.payment.receipt_url;
                                this.payment.singida_synced = !!res.data.application.payment.singida_synced;
                            }
                            toast('Academic profile saved! Category: ' + this.calculatedCategory, 'success');
                            this.currentStep = 4;
                        })
                        .catch(err => {
                            this.loading = false;
                            const msg = err.response?.data?.message || 'Error saving academic profile';
                            this.errorMsg = msg;
                            toast(msg, 'error');
                        });
                },

                isUnder18() {
                    if (!this.personal || !this.personal.date_of_birth) return false;
                    const dob = new Date(this.personal.date_of_birth);
                    const today = new Date();
                    let age = today.getFullYear() - dob.getFullYear();
                    const m = today.getMonth() - dob.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                        age--;
                    }
                    return age < 18;
                },

                submitFinal() {
                    if (!this.consentGiven) {
                        toast('Lazima ukubali fomu ya idhini ya udahili ili kuwasilisha maombi.', 'error');
                        return;
                    }
                    if (this.payment.status !== 'paid') {
                        toast('Huwezi kuwasilisha maombi kabla ya malipo ya ada ya fomu (TZS 20,000) kuthibitishwa na Admin.', 'error');
                        this.currentStep = 5;
                        return;
                    }
                    if (this.isUnder18() && (!this.parentConsentGiven || !this.parentName || !this.parentSignature)) {
                        toast('Taarifa na saini ya mzazi/mlezi zinahitajika kwa kuwa mwombaji ana umri chini ya miaka 18.', 'error');
                        return;
                    }
                    this.loading = true;
                    this.errorMsg = '';
                    axios.post('{{ url('/applicant/submit-final') }}', {
                        digital_signature: this.signatureData || {!! json_encode($user->name ?? '') !!},
                        confirm_accurate: this.consentGiven,
                        read_privacy: this.consentGiven,
                        read_terms: this.consentGiven,
                        consent_given: this.consentGiven,
                        understand_penalty: this.consentGiven,
                        privacy_policy_version: this.privacyPolicyVersion,
                        terms_version: this.termsVersion,
                        parent_consent_given: this.parentConsentGiven,
                        parent_name: this.parentName,
                        parent_signature: this.parentSignature
                    })
                    .then(res => {
                        this.loading = false;
                        toast('Maombi yamewasilishwa kikamilifu!', 'success');
                        if (this.isGuest) {
                            this.showClaimAccountForm = true;
                        } else {
                            window.location.href = '{{ url('/applicant/dashboard') }}';
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        const msg = err.response?.data?.message || 'Hitilafu ya kuwasilisha maombi';
                        this.errorMsg = msg;
                        toast(msg, 'error');
                    });
                },

                saveGuestCredentials() {
                    if (!this.consentGiven) {
                        toast('Tafadhali ridhia fomu ya ridhaa ya udahili ili kuendelea.', 'error');
                        return;
                    }
                    this.loading = true;
                    this.errorMsg = '';
                    axios.post('{{ url('/applicant/guest-credentials') }}', {
                        ...this.guestCredentials,
                        consent_given: this.consentGiven
                    })
                        .then(res => {
                            this.loading = false;
                            if (this.guestCredentials.whatsapp_number) {
                                this.personal.whatsapp_number = this.guestCredentials.whatsapp_number;
                            }
                            toast('Taarifa zako zimehifadhiwa vizuri!', 'success');
                            this.currentStep = 2;
                        })
                        .catch(err => {
                            this.loading = false;
                            const msg = err.response?.data?.message || 'Kosa limetokea wakati wa kuhifadhi taarifa.';
                            this.errorMsg = msg;
                            toast(msg, 'error');
                        });
                },

                saveInitialConsentAndNext() {
                    this.currentStep = 2;
                },

                claimGuestAccount() {
                    this.loading = true;
                    this.errorMsg = '';
                    axios.post('{{ url('/applicant/claim-account') }}', this.claimPassword)
                        .then(res => {
                            this.loading = false;
                            toast('Password imewekwa kikamilifu! Karibu kwenye dashboard.', 'success');
                            window.location.href = '{{ url('/applicant/dashboard') }}';
                        })
                        .catch(err => {
                            this.loading = false;
                            const msg = err.response?.data?.message || 'Kosa limetokea wakati wa kuweka nenosiri.';
                            this.errorMsg = msg;
                            toast(msg, 'error');
                        });
                },

                init() {
                    if (this.academic.acsee_combination && this.acseeCombinations[this.academic.acsee_combination.toUpperCase()]) {
                        const subjects = this.acseeCombinations[this.academic.acsee_combination.toUpperCase()];
                        this.academic.acsee_subject1 = this.academic.acsee_subject1 || subjects[0];
                        this.academic.acsee_subject2 = this.academic.acsee_subject2 || subjects[1];
                        this.academic.acsee_subject3 = this.academic.acsee_subject3 || subjects[2];
                    }
                    
                    this.$watch('academic.acsee_combination', (value) => {
                        if (value) {
                            const combUpper = value.toUpperCase();
                            if (this.acseeCombinations[combUpper]) {
                                const subjects = this.acseeCombinations[combUpper];
                                this.academic.acsee_subject1 = subjects[0];
                                this.academic.acsee_subject2 = subjects[1];
                                this.academic.acsee_subject3 = subjects[2];
                            }
                        }
                    });

                    this.$watch('academic.gpa', (value) => {
                        this.autoSelectProgramme();
                    });
                    this.$watch('academic.acsee_points', (value) => {
                        this.autoSelectProgramme();
                    });
                    this.$watch('academic.admission_type', (value) => {
                        this.autoSelectProgramme();
                    });
                    // Initial call
                    this.autoSelectProgramme();

                    if (this.academic.gpa) {
                        const val = parseFloat(this.academic.gpa);
                        if (!isNaN(val)) {
                            if (val >= 4.4) this.selectedGrade = '4.50';
                            else if (val >= 3.5) this.selectedGrade = '3.80';
                            else if (val >= 3.0) this.selectedGrade = '3.20';
                            else if (val >= 2.0) this.selectedGrade = '2.50';
                            else this.selectedGrade = '1.80';
                        }
                    }

                    // Safety guards
                    this.$watch('currentStep', (value) => {
                        if (value > 1 && !this.consentGiven) {
                            this.currentStep = 1;
                            toast('Tafadhali ridhia fomu ya ridhaa ya udahili ili kuendelea.', 'error');
                        }
                        if (value >= 7 && this.payment.status !== 'paid') {
                            this.currentStep = 6;
                            toast('Tafadhali kamilisha malipo ya ada ya fomu (TZS 20,000) na usubiri uthibitisho wa Admin ili kuendelea na tamko la mwisho.', 'error');
                        }
                        if (value === 6) {
                            this.checkPaymentStatus(false);
                        }
                    });

                    if (this.currentStep > 1 && !this.consentGiven) {
                        this.currentStep = 1;
                    }
                    if (this.currentStep >= 7 && this.payment.status !== 'paid') {
                        this.currentStep = 6;
                    }
                },

                autoSelectProgramme() {
                    let filtered = this.filteredProgrammes;
                    if (filtered.length > 0) {
                        let exists = filtered.some(p => p.id == this.academic.programme_id);
                        if (!exists) {
                            this.academic.programme_id = filtered[0].id;
                        }
                    } else {
                        this.academic.programme_id = '';
                    }
                },

                get filteredProgrammes() {
                    let list = this.programmes || [];
                    if (this.academic.admission_type === 'Diploma') {
                        let gpaVal = parseFloat(this.academic.gpa);
                        if (isNaN(gpaVal)) return list;
                        if (gpaVal >= 3.0) {
                            return list.filter(p => p.code !== 'Foundation');
                        } else if (gpaVal >= 2.0) {
                            return list.filter(p => p.code === 'Foundation');
                        } else {
                            return [];
                        }
                    } else if (this.academic.admission_type === 'Form Six') {
                        let pointsVal = parseFloat(this.academic.acsee_points);
                        if (isNaN(pointsVal)) return list;
                        if (pointsVal <= 17) {
                            return list.filter(p => p.code !== 'Foundation');
                        } else {
                            return list.filter(p => p.code === 'Foundation');
                        }
                    }
                    return list;
                },

                saveProgrammeSelectionAndNext() {
                    if (!this.academic.programme_id) {
                        toast('Tafadhali chagua programu kabla ya kuendelea. (Please select a programme before proceeding.)', 'error');
                        return;
                    }
                    this.loading = true;
                    this.errorMsg = '';
                    axios.post('{{ url('/applicant/academic-profile') }}', this.academic)
                        .then(res => {
                            this.loading = false;
                            this.calculatedCategory = res.data.admission_category;
                            this.applicationId = res.data.application.id;
                            if (res.data.application.payment) {
                                this.payment.id = res.data.application.payment.id;
                                this.payment.control_number = res.data.application.payment.control_number;
                                this.payment.status = res.data.application.payment.payment_status;
                                this.payment.transaction_reference = res.data.application.payment.transaction_reference;
                                this.payment.receipt_url = res.data.application.payment.receipt_url;
                                this.payment.singida_synced = !!res.data.application.payment.singida_synced;
                                this.payment.rejection_reason = res.data.application.payment.rejection_reason || '';
                            }
                            toast('Programu imehifadhiwa! Sasa kamilisha malipo ya fomu (TZS 20,000).', 'success');
                            this.currentStep = (this.payment.status === 'paid') ? 5 : 6;
                        })
                        .catch(err => {
                            this.loading = false;
                            const msg = err.response?.data?.message || 'Kosa limetokea wakati wa kuhifadhi programu.';
                            this.errorMsg = msg;
                            toast(msg, 'error');
                        });
                },

                logoutGuest() {
                    this.loading = true;
                    axios.post('{{ route('logout') }}')
                        .then(() => {
                            window.location.href = '{{ route('home') }}';
                        })
                        .catch(() => {
                            window.location.href = '{{ route('home') }}';
                        });
                }
            };
        }
    </script>

    <div class="max-w-5xl mx-auto space-y-8" x-data="applicationWizardData()">

        <!-- Official Partnership Header Badge -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 rounded-3xl p-6 border border-blue-950 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="px-3.5 py-1 rounded-full bg-amber-500/20 text-amber-400 text-[10px] font-extrabold uppercase tracking-wider inline-block">
                    CHUO KIKUU HURIA CHA TANZANIA (OUT) + CHUO CHA UALIMU SINGIDA (STTC)
                </span>
                <h2 class="text-base sm:text-lg font-black text-white">FOMU YA MAOMBI YA UDAHILI (SUPA ADMISSION PORTAL)</h2>
                <p class="text-xs text-blue-100">Shahada (Bachelor), Shahada ya Uzamili (Integrated Master's), na Foundation Programme.</p>
            </div>
            <div class="sm:text-right shrink-0 bg-blue-950/50 sm:bg-transparent p-3 sm:p-0 rounded-2xl border sm:border-0 border-blue-800/40">
                <span class="text-[10px] font-extrabold uppercase text-blue-200 block">Ada ya Fomu</span>
                <span class="text-lg sm:text-xl font-black text-amber-400">TZS 20,000/=</span>
            </div>
        </div>

        <!-- Quick Downloads & Guides Action Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-white border border-slate-200 text-xs shadow-sm">
            <div class="flex items-center space-x-2 text-slate-700">
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-700 font-extrabold text-[11px] uppercase tracking-wider">📥 Miongozo ya Udahili</span>
                <span class="font-medium text-slate-600 hidden sm:inline">Pakua orodha ya mahitaji na mwongozo wa hatua 7 za fomu kabla ya kuanza:</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('public.download.admission-excel') }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Pakua Fomu ya Excel (.CSV)</span>
                </a>
                <a href="{{ route('public.admission-steps-guide') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Mwongozo wa Hatua 7 (PDF)</span>
                </a>
            </div>
        </div>

        <!-- Progress Steps Bar & Timeline -->
        <div x-show="!showClaimAccountForm" x-cloak class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-lg space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block">Admission Timeline</span>
                    <h2 class="text-base font-extrabold text-slate-900"
                        x-text="currentStep === 1 ? 'Step 1: Account Verification' : (currentStep === 2 ? 'Step 2: Taarifa Binafsi za Mwombaji' : (currentStep === 3 ? 'Step 3: Taarifa za Taaluma na Elimu' : (currentStep === 4 ? 'Step 4: Uchaguzi wa Programu na Kundi' : (currentStep === 5 ? 'Step 5: Orodha ya Vyeti na Nyaraka' : (currentStep === 6 ? 'Step 6: Malipo ya Ada ya Fomu (TZS 20,000)' : 'Hatua ya 7: Tamko la Mwombaji & Kuwasilisha Maombi')))))">
                    </h2>
                </div>
                <span class="px-4 py-1.5 rounded-full bg-amber-500/10 text-amber-600 font-extrabold text-xs border border-amber-500/30">
                    Step <span x-text="currentStep"></span> of 7
                </span>
            </div>

            <!-- Animated Bar -->
            <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden p-0.5">
                <div class="bg-gradient-to-r from-blue-600 via-amber-500 to-emerald-500 h-full rounded-full transition-all duration-500" :style="'width: ' + (currentStep / maxSteps * 100) + '%'"></div>
            </div>

            <!-- Interactive Step Indicator Circles -->
            <div class="hidden sm:flex justify-between text-xs font-bold text-slate-500">
                <template x-for="i in 7" :key="i">
                    <button @click="goToStep(i)" :disabled="payment.status !== 'paid' && (i === 5 || i === 7)" class="flex flex-col items-center gap-1.5 focus:outline-none disabled:opacity-50">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-extrabold text-xs transition-all"
                             :class="i === currentStep ? 'bg-amber-500 text-slate-950 shadow-md ring-4 ring-amber-500/20' : (i < currentStep ? 'bg-emerald-600 text-white' : (payment.status !== 'paid' && (i === 5 || i === 7) ? 'bg-slate-100 text-slate-300' : 'bg-slate-100 text-slate-500'))">
                            <span x-text="payment.status !== 'paid' && (i === 5 || i === 7) ? '🔒' : (i < currentStep ? '✓' : i)"></span>
                        </div>
                        <span class="text-[10px]" :class="i === currentStep ? 'text-amber-500 font-black' : ''" x-text="'Step ' + i"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Step Container Card -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-2xl space-y-8 relative">
            
            <div x-show="errorMsg" class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-500 text-xs font-bold" x-cloak x-text="errorMsg"></div>

            <!-- Step 1: Account Overview / Guest Info -->
            <div x-show="currentStep === 1" x-cloak class="space-y-6">
                <div class="p-6 bg-blue-50 border border-blue-200 rounded-3xl text-blue-900 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-xl shadow">🤝</div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900">Personal Data Consent Active</h4>
                        <p class="text-xs text-slate-600 mt-0.5">You have read and accepted the latest Privacy Policy and Terms & Conditions of STTC & OUT admission application.</p>
                    </div>
                </div>

                <template x-if="isGuest">
                    <div class="space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-xl font-extrabold text-slate-900">Step 1: Jaza Taarifa za Mwanzoni</h3>
                            <p class="text-xs text-slate-500">Tafadhali jaza jina lako kamili, barua pepe na namba ya simu kuanza fomu ya maombi.</p>
                        </div>

                        <form @submit.prevent="saveGuestCredentials()" class="space-y-4 text-xs font-semibold">
                            <div>
                                <label class="block text-xs font-extrabold uppercase mb-1">Jina Kamili (Full Name)</label>
                                <input type="text" x-model="guestCredentials.name" required placeholder="e.g. John Joseph" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold uppercase mb-1">Barua Pepe (Email Address)</label>
                                <input type="email" x-model="guestCredentials.email" required placeholder="e.g. john@example.com" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold uppercase mb-1">Namba ya Simu (Phone Number)</label>
                                <input type="text" x-model="guestCredentials.phone" required placeholder="e.g. 0712345678" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold uppercase mb-1">Namba ya WhatsApp (WhatsApp Number) – Hiari</label>
                                <input type="text" x-model="guestCredentials.whatsapp_number" placeholder="e.g. 0712345678" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>

                            <div class="flex justify-between items-center pt-4">
                                <button type="button" @click="logoutGuest()" class="px-6 py-3.5 rounded-2xl bg-red-50 text-red-600 font-extrabold text-xs hover:bg-red-100 transition-all">
                                    Decline & Exit
                                </button>
                                <button type="submit" :disabled="loading || !consentGiven" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!loading">Next: Taarifa Binafsi &rarr;</span>
                                    <span x-show="loading" x-cloak>Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </template>

                <template x-if="!isGuest">
                    <div class="space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-xl font-extrabold text-slate-900">Step 1: Account Credentials Verified</h3>
                            <p class="text-xs text-slate-500">Your student applicant account is verified and ready for OUT + STTC SUPA admission profiling.</p>
                        </div>

                        <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-900/10 to-indigo-900/10 border border-blue-200 space-y-3 text-xs">
                            <p class="flex justify-between"><span class="text-slate-500">Jina Kamili la Mwombaji:</span> <strong class="text-slate-900">{{ $user->name }}</strong></p>
                            <p class="flex justify-between"><span class="text-slate-500">Registered Email:</span> <strong class="text-slate-900">{{ $user->email }}</strong></p>
                            <p class="flex justify-between"><span class="text-slate-500">Registered Phone:</span> <strong class="text-slate-900">{{ $user->phone }}</strong></p>
                            <div class="pt-2 border-t border-slate-200 flex justify-between items-center">
                                <span class="text-slate-500">Verification Status:</span>
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">Verified Applicant</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <button type="button" @click="logoutGuest()" class="px-6 py-3.5 rounded-2xl bg-red-50 text-red-600 font-extrabold text-xs hover:bg-red-100 transition-all">
                                Decline & Exit
                            </button>
                            <button @click="saveInitialConsentAndNext()" :disabled="loading || !consentGiven" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loading">Next: Taarifa Binafsi za Mwombaji &rarr;</span>
                                <span x-show="loading" x-cloak>Saving Consent...</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Step 2: Personal Information -->
            <div x-show="currentStep === 2" x-cloak class="space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-extrabold text-slate-900">Step 2: Taarifa Binafsi za Mwombaji</h3>
                    <p class="text-xs text-slate-500">Sehemu ya 1 ya Fomu: Jaza Jinsia, Tarehe ya Kuzaliwa, NIDA, Vitambulisho, Mkoa, Halmashauri na Kata.</p>
                </div>

                <form @submit.prevent="savePersonal()" class="space-y-6">
                    <div class="p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-xs flex items-center gap-2.5">
                        <span class="text-base">🪪</span>
                        <p><strong>Kumbuka:</strong> Chagua aina moja ya kitambulisho (<strong>Kitambulisho cha NIDA</strong>, <strong>Kitambulisho cha Kura</strong>, au <strong>Kitambulisho cha Kazi</strong>) kisha jaza namba yake.</p>
                    </div>

                    <!-- Row 1: JINSIA & TAREHE YA KUZALIWA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Jinsia (Gender)</label>
                            <select x-model="personal.gender" required class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="male">Me (Male)</option>
                                <option value="female">Ke (Female)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Tarehe ya Kuzaliwa (Date of Birth)</label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <select x-model="dob_day" x-ref="dobDay" required class="w-full px-3 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                        <option value="">Siku (Day)</option>
                                        @for ($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <select x-model="dob_month" x-ref="dobMonth" required class="w-full px-3 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                        <option value="">Mwezi (Month)</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <select x-model="dob_year" x-ref="dobYear" required class="w-full px-3 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                        <option value="">Mwaka (Year)</option>
                                        @for ($y = 1950; $y <= 2026; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: DROPDOWN YA AINA YA KITAMBULISHO & NAMBA YA KITAMBULISHO -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-5 rounded-3xl bg-amber-500/5 border-2 border-amber-500/20">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-900 uppercase mb-2 flex items-center gap-1.5">
                                <span>🪪 Aina ya Kitambulisho (Choose ID Type)</span>
                                <span class="text-amber-600 font-bold">*</span>
                            </label>
                            <select x-model="idType" @change="onIdTypeChange()" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-extrabold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm">
                                <option value="nida">Kitambulisho cha NIDA (National ID)</option>
                                <option value="voter">Kitambulisho cha Kura (Voter's ID)</option>
                                <option value="work">Kitambulisho cha Kazi (Work / Employment ID)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-900 uppercase mb-2 flex items-center justify-between">
                                <span x-text="idType === 'nida' ? 'Namba ya Kitambulisho cha NIDA' : (idType === 'voter' ? 'Namba ya Kitambulisho cha Kura' : 'Namba ya Kitambulisho cha Kazi')"></span>
                                <span class="text-amber-600 font-bold">*</span>
                            </label>
                            <input type="text" 
                                   x-model="idNumber" 
                                   required
                                   :placeholder="idType === 'nida' ? 'e.g. 19950815123450000112' : (idType === 'voter' ? 'e.g. T-1234-5678-901' : 'e.g. NIDA-KAZI-001')" 
                                   class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm font-mono">
                        </div>
                    </div>

                    <!-- Row 3: MKOA, HALMASHAURI, KATA -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Mkoa Unaoishi</label>
                            <select x-model="personal.region" @change="personal.district = ''; personal.ward = ''" required class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="">Chagua Mkoa</option>
                                <template x-for="r in Object.keys(tanzaniaRegions)" :key="r">
                                    <option :value="r" x-text="r"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Halmashauri Unayoishi</label>
                            <select x-model="personal.district" @change="personal.ward = ''" required :disabled="!personal.region" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 disabled:opacity-50">
                                <option value="">Chagua Halmashauri</option>
                                <template x-for="d in (tanzaniaRegions[personal.region] || [])" :key="d">
                                    <option :value="d" x-text="d"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Kata</label>
                            <select x-model="personal.ward" :disabled="!personal.district" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 disabled:opacity-50">
                                <option value="">Chagua Kata</option>
                                <template x-for="w in (tanzaniaWards[personal.district] || [])" :key="w">
                                    <option :value="w" x-text="w"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="button" @click="currentStep = 1" class="px-6 py-3.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Back</button>
                        <button type="submit" :disabled="loading" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl">
                            <span x-show="!loading">Save & Continue &rarr;</span>
                            <span x-show="loading" x-cloak>Saving Personal Info...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Academic Profile (Diploma vs Form Six) -->
            <div x-show="currentStep === 3" x-cloak class="space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-extrabold text-slate-900">Step 3: Taarifa za Taaluma na Elimu ya Nyuma</h3>
                    <p class="text-xs text-slate-500">Sehemu ya 2 ya Fomu: Jaza Kipengele A (Diploma/Stashahada) au Kipengele B (Form Six).</p>
                </div>

                <form @submit.prevent="saveAcademic()" class="space-y-6">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-3">Chagua Njia ya Udahili</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="p-5 rounded-2xl border-2 flex items-center space-x-3 cursor-pointer transition-all"
                                   :class="academic.admission_type === 'Diploma' ? 'border-amber-500 bg-amber-500/10' : 'border-slate-200 bg-slate-50'">
                                <input type="radio" x-model="academic.admission_type" value="Diploma" class="text-amber-500">
                                <div>
                                    <span class="font-extrabold text-sm text-slate-900 block">Kipengele A: Diploma / Stashahada</span>
                                    <span class="text-[11px] text-slate-500">GPA 3.0-5.0 (Direct) au GPA 2.0-2.9 (Foundation)</span>
                                </div>
                            </label>
                            <label class="p-5 rounded-2xl border-2 flex items-center space-x-3 cursor-pointer transition-all"
                                   :class="academic.admission_type === 'Form Six' ? 'border-blue-500 bg-blue-500/10' : 'border-slate-200 bg-slate-50'">
                                <input type="radio" x-model="academic.admission_type" value="Form Six" class="text-blue-500">
                                <div>
                                    <span class="font-extrabold text-sm text-slate-900 block">Kipengele B: Form Six (ACSEE)</span>
                                    <span class="text-[11px] text-slate-500">Form IV & Form VI Index Numbers & Points</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Kipengele A: Diploma Fields -->
                    <div x-show="academic.admission_type === 'Diploma'" class="space-y-4" x-cloak>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">1. Jina la Chuo Ulichohitimu</label>
                                <input type="text" x-model="academic.college_name" placeholder="e.g. STTC Singida / DIT / ATC" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">2. Jina la Kozi / Stashahada Uliyohitimu</label>
                                <select x-model="selectedDiplomaType" @change="if(selectedDiplomaType !== 'Other Diploma') { academic.diploma_programme_name = selectedDiplomaType; } else { academic.diploma_programme_name = customDiplomaName || 'Other Diploma'; }" :required="academic.admission_type === 'Diploma'" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">Chagua Stashahada / Diploma</option>
                                    <option value="Stashahada ya Elimu ya Msingi">1. Stashahada ya Elimu ya Msingi</option>
                                    <option value="Stashahada ya Elimu ya Awali">2. Stashahada ya Elimu ya Awali</option>
                                    <option value="Diploma in Primary Education (DPEE)">3. Diploma in Primary Education (DPEE)</option>
                                    <option value="Diploma in Pre Primary Education (DPPE)">4. Diploma in Pre Primary Education (DPPE)</option>
                                    <option value="Diploma in Secondary Education (DSED)">5. Diploma in Secondary Education (DSED)</option>
                                    <option value="Diploma in Adult Education (DAE)">6. Diploma in Adult Education (DAE)</option>
                                    <option value="Other Diploma">7. Other Diploma (Stashahada Nyingine)</option>
                                </select>
                                <div x-show="selectedDiplomaType === 'Other Diploma'" x-cloak class="mt-3">
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Andika Jina la Diploma Nyingine:</label>
                                    <input type="text" x-model="customDiplomaName" @input="academic.diploma_programme_name = customDiplomaName" placeholder="e.g. Diploma in Special Needs Education" class="w-full px-4 py-3 rounded-2xl border border-amber-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">3. Namba ya Mtihani</label>
                                <input type="text" x-model="academic.diploma_registration_number" placeholder="e.g. STTC/2022/098" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">4. Mwaka wa Kuhitimu</label>
                                <select x-model="academic.diploma_graduation_year" :required="academic.admission_type === 'Diploma'" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">Chagua Mwaka</option>
                                    @for ($year = (int)date('Y'); $year >= 1980; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase">5. Kiwango cha Ufaulu</label>
                                    <div class="flex items-center gap-1 bg-slate-200 p-0.5 rounded-xl text-[10px] font-extrabold">
                                        <button type="button" @click="setScoreType('gpa')" 
                                                :class="scoreType === 'gpa' ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-900'" 
                                                class="px-2.5 py-1 rounded-lg transition-all">
                                            📊 GPA (Namba)
                                        </button>
                                        <button type="button" @click="setScoreType('grade')" 
                                                :class="scoreType === 'grade' ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-900'" 
                                                class="px-2.5 py-1 rounded-lg transition-all">
                                            🎖️ Daraja / Grade
                                        </button>
                                    </div>
                                </div>

                                <!-- Mode A: Numeric GPA input -->
                                <div x-show="scoreType === 'gpa'" x-cloak>
                                    <select x-model="academic.gpa" 
                                            class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                        <option value="">-- Chagua GPA --</option>
                                        <option value="5.00">5.0 - 5.09</option>
                                        <option value="4.00">4.0 - 4.09</option>
                                        <option value="3.00">3.0 - 3.09</option>
                                        <option value="2.00">2.0 - 2.09</option>
                                        <option value="1.00">1.0 - 1.09</option>
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1">Chagua alama ya GPA.</p>
                                </div>

                                <!-- Mode B: Grade Classification Dropdown -->
                                <div x-show="scoreType === 'grade'" x-cloak>
                                    <select x-model="selectedGrade" @change="if(selectedGrade) { academic.gpa = selectedGrade; }" 
                                            class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                        <option value="">-- Chagua Daraja / Grade --</option>
                                        <option value="4.50">🏆 Daraja la Kwanza / Distinction / First Class (GPA 4.40 – 5.00)</option>
                                        <option value="3.80">🥇 Daraja la Pili Juu / Credit / Upper Second (GPA 3.50 – 4.30)</option>
                                        <option value="3.20">🥈 Daraja la Pili Chini / Pass / Lower Second (GPA 3.00 – 3.40)</option>
                                        <option value="2.50">🥉 Daraja la Chini / Pass / Foundation (GPA 2.00 – 2.90)</option>
                                        <option value="1.80">⚠️ Chini ya Vigezo / Fail (Chini ya GPA 2.00)</option>
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1">Chagua daraja lililoandikwa kwenye cheti/stashahada yako.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kipengele B: Form Six Fields -->
                    <div x-show="academic.admission_type === 'Form Six'" class="space-y-4" x-cloak>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">1. Namba ya Mtihani Form IV (CSEE)</label>
                                <input type="text" x-model="academic.csee_number" placeholder="e.g. S0101/0001/2020" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">2. Mwaka wa Kuhitimu Form IV</label>
                                <select x-model="academic.csee_year" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">-- Chagua Mwaka --</option>
                                    @for ($year = (int)date('Y'); $year >= 1980; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">3. Shule Uliyosoma Form IV</label>
                                <input type="text" x-model="academic.csee_school" placeholder="e.g. Singida Secondary School" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">4. Namba ya Mtihani Form VI (ACSEE)</label>
                                <input type="text" x-model="academic.acsee_number" placeholder="e.g. S0101/0501/2022" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">5. Mwaka wa Kuhitimu Form VI</label>
                                <select x-model="academic.acsee_year" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">-- Chagua Mwaka --</option>
                                    @for ($year = (int)date('Y'); $year >= 1980; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">6. Shule Uliyosoma Form VI</label>
                                <input type="text" x-model="academic.acsee_school" placeholder="e.g. Singida Secondary School" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>

                        <!-- Combination & ACSEE Grades -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border border-slate-100 p-6 rounded-3xl bg-slate-50/50">
                            <div class="md:col-span-3 pb-2 border-b border-slate-100">
                                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Tahasusi na Daraja za Masomo (Combination & Grades)</h4>
                                <p class="text-[10px] text-slate-500 mt-1">Chagua combination yako na uingize madaraja ya masomo matatu (principal subjects) yaliyoko kwenye matokeo yako.</p>
                            </div>

                            <!-- Combination Form VI Select -->
                            <div class="md:col-span-3">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">7. Combination Form VI</label>
                                <input type="text" x-model="academic.acsee_combination" placeholder="e.g. PCB / PCM / ECA" 
                                       class="w-full sm:w-1/2 px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 uppercase transition-all">
                            </div>
                                         <!-- Points Form VI Select -->
                            <div class="md:col-span-3 mt-2">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">8. Jumla ya Pointi za ACSEE (ACSEE Points Total)</label>
                                <div class="w-full sm:w-1/2">
                                    <input type="number" min="3" max="21" step="1" x-model="academic.acsee_points" placeholder="e.g. 9" 
                                           class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                                    <span class="text-[10px] text-slate-500 mt-1 block font-medium">Kokotoa jumla ya alama za masomo matatu ya tahasusi (A=1, B=2, C=3, D=4, E=5, S=6, F=7).</span>
                                </div>
                            </div>

                            <!-- Live Points Display Card -->
                            <div class="md:col-span-3 mt-2" x-show="academic.acsee_combination && academic.acsee_points" x-cloak>
                                <div class="p-4 rounded-2xl border border-slate-200 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                                    <div class="space-y-1">
                                        <span class="text-xs font-extrabold text-slate-700 block uppercase">Uhakiki wa Pointi za ACSEE (ACSEE Eligibility Status)</span>
                                        <span class="text-[11px] text-slate-500 block">Kulingana na pointi ulizochagua (Pointi <= 17 zina sifa za Bachelor Degree Direct Entry)</span>
                                    </div>
                                    <div class="flex items-center gap-3 self-end sm:self-auto">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-extrabold px-3 py-1.5 rounded-xl" 
                                                  :class="parseInt(academic.acsee_points) <= 17 ? 'bg-emerald-500/10 text-emerald-700 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-700 border border-amber-500/20'"
                                                  x-text="parseInt(academic.acsee_points) <= 17 ? 'Sifa za Bachelor (Direct Entry)' : 'Sifa za Foundation'">
                                            </span>
                                            <span class="text-2xl font-black text-slate-900" x-text="academic.acsee_points + ' Pointi'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="button" @click="currentStep = 2" class="px-6 py-3.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Back</button>
                        <button type="submit" :disabled="loading" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl">
                            <span x-show="!loading">Hifadhi & Auto-Calculate Category &rarr;</span>
                            <span x-show="loading" x-cloak>Calculating Admission Category...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 4: Admission Category & Programme Selection -->
            <div x-show="currentStep === 4" x-cloak class="space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-extrabold text-slate-900">Step 4: Uchaguzi wa Programu na Kundi la Udahili</h3>
                    <p class="text-xs text-slate-500">Sehemu ya 3 ya Fomu: Auto-calculated result based on official OUT/STTC entry criteria.</p>
                </div>

                <div class="p-6 rounded-3xl bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white space-y-2 border border-blue-950 shadow-xl">
                    <span class="text-[10px] uppercase font-extrabold text-amber-400 tracking-wider">Kundi la Udahili Lililopatikana</span>
                    <h4 class="text-3xl font-black" x-text="calculatedCategory === 'Direct Entry' ? 'Kundi la Kwanza: GPA 3.0–5.0 / Form VI (Direct Entry — OUT)' : 'Kundi la Pili: GPA 2.0–2.9 (Foundation Programme — SUPA/STTC)'"></h4>
                    <p class="text-xs text-blue-100">
                        Kundi la Kwanza: GPA &ge; 3.0 au Form VI Points &ge; 5 (Direct Entry - OUT). Kundi la Pili: GPA 2.0–2.9 (Foundation Programme kupitia SUPA/STTC).
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="prog in filteredProgrammes" :key="prog.id">
                        <div @click="academic.programme_id = prog.id" 
                             class="p-6 rounded-2xl border-2 cursor-pointer transition-all space-y-3 card-hover-effect"
                             :class="academic.programme_id == prog.id ? 'border-amber-500 bg-amber-500/10' : 'border-slate-200 bg-slate-50'">
                            <div class="flex justify-between items-center">
                                <span class="font-extrabold text-sm text-slate-900" x-text="prog.code + ' - ' + prog.name"></span>
                                <span class="text-xs font-bold text-amber-500" x-text="'TZS ' + Number(prog.annual_fee).toLocaleString()"></span>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed" x-text="prog.description"></p>
                        </div>
                    </template>
                </div>

                <!-- Warning when not eligible for any programme (e.g. GPA < 2.0) -->
                <template x-if="filteredProgrammes.length === 0">
                    <div class="p-6 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-500 space-y-2 text-center">
                        <span class="text-3xl">⚠️</span>
                        <h5 class="font-extrabold text-sm">Hauna Sifa za Udahili (Not Eligible)</h5>
                        <p class="text-xs">
                            Kiwango chako cha ufaulu (GPA) kiko chini ya kiwango cha chini kinachohitajika (GPA 2.0). Huna sifa za kujiunga na programu yoyote.
                        </p>
                    </div>
                </template>

                <div class="flex justify-between items-center pt-4">
                    <button type="button" @click="currentStep = 3" class="px-6 py-3.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Back</button>
                    <button type="button" @click="saveProgrammeSelectionAndNext()" :disabled="loading || filteredProgrammes.length === 0" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl">
                        <span x-show="!loading">Next: Malipo ya Ada ya Fomu (TZS 20,000) &rarr;</span>
                        <span x-show="loading" x-cloak>Saving Selection...</span>
                    </button>
                </div>
            </div>

            <!-- Step 6: Automatic Payment Detection (TZS 20,000) -->
            <div x-show="currentStep === 6" x-cloak class="space-y-6">
                <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Step 6: Malipo ya Ada ya Fomu ya Maombi (TZS 20,000/=)</h3>
                        <p class="text-xs text-slate-500">Lipia ada ya fomu ya maombi ya TZS 20,000 kwa kutumia NMB Control Number hapo chini. Mfumo utatambua malipo yako kiotomatiki mara tu utakapokamilisha malipo kupitia simu au benki bila kuhitaji kupakia risiti yoyote.</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('public.payment-guideline') }}" target="_blank" class="px-4 py-2 rounded-xl bg-white border border-slate-350 hover:border-slate-400 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all text-center flex items-center gap-1.5 cursor-pointer">
                            👁️ View PDF Guideline
                        </a>
                        <a href="{{ route('public.payment-guideline') }}?download=1" target="_blank" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white transition-all text-center flex items-center gap-1.5 cursor-pointer">
                            📥 Download PDF
                        </a>
                    </div>
                </div>

                <!-- Control Number & Payment Instructions Box -->
                <div class="p-8 rounded-3xl bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white border border-blue-950 shadow-2xl space-y-5">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400 block">NMB Control Number</span>
                            <div class="flex items-center gap-3">
                                <span class="text-3xl font-black text-white tracking-wide font-mono" x-text="payment.control_number || 'Inazalishwa...'"></span>
                                <button type="button" 
                                        x-show="payment.control_number && !payment.control_number.startsWith('PENDING-')"
                                        @click="navigator.clipboard.writeText(payment.control_number); toast('Namba ya Control Number imenakiliwa: ' + payment.control_number, 'success')"
                                        class="px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-[11px] font-extrabold flex items-center gap-1 transition-all"
                                        title="Copy Control Number">
                                    📋 Copy
                                </button>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider mt-1 block"
                                  :class="payment.singida_synced ? 'text-emerald-300' : 'text-amber-300'"
                                  x-text="payment.singida_synced ? '✓ Control Number Imethibitishwa' : 'Inatayarishwa kwenye mtandao wa NMB...'"></span>
                        </div>
                        <div class="text-right space-y-2">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200 block">Kiasi cha Ada ya Maombi</span>
                                <span class="text-2xl font-black text-amber-400">TZS 20,000/=</span>
                            </div>
                            <div class="flex items-center gap-2 justify-end">
                                <button type="button"
                                        @click="requestControlNumber(needsSingidaControlNumber())"
                                        :disabled="requestingControlNumber"
                                        class="px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-black shadow-md disabled:opacity-60">
                                    <span x-show="!requestingControlNumber" x-text="needsSingidaControlNumber() ? 'Pata Control Number' : 'Refresh Control No.'"></span>
                                    <span x-show="requestingControlNumber" x-cloak>Inapakia...</span>
                                </button>
                                <button type="button"
                                        @click="checkPaymentStatus(true)"
                                        :disabled="checkingPaymentStatus"
                                        class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-black shadow-md disabled:opacity-60 flex items-center gap-1.5">
                                    <span x-show="!checkingPaymentStatus">🔄 Angalia Hali Sasa</span>
                                    <span x-show="checkingPaymentStatus" x-cloak>Inakagua...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Step-by-Step Instructions Container -->
                <div x-data="{ activeTab: 'mobile' }" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-left space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span>📋</span> Maelekezo ya Hatua kwa Hatua (PDF Guidelines)
                        </h3>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="flex flex-wrap border-b border-slate-150 gap-1">
                        <button type="button" @click="activeTab = 'mobile'" :class="activeTab === 'mobile' ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="px-4 py-2.5 rounded-t-xl border-b-2 text-xs font-bold transition-all cursor-pointer">
                            📱 Mobile Money
                        </button>
                        <button type="button" @click="activeTab = 'nmb_mkononi'" :class="activeTab === 'nmb_mkononi' ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="px-4 py-2.5 rounded-t-xl border-b-2 text-xs font-bold transition-all cursor-pointer">
                            🏦 NMB Mkononi (USSD & App)
                        </button>
                        <button type="button" @click="activeTab = 'bank'" :class="activeTab === 'bank' ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="px-4 py-2.5 rounded-t-xl border-b-2 text-xs font-bold transition-all cursor-pointer">
                            🏢 Tawi la NMB / Wakala
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="text-xs text-slate-700 space-y-4 pt-1">
                        
                        <!-- Tab 1: Mobile Money -->
                        <div x-show="activeTab === 'mobile'" class="space-y-4" x-cloak>
                            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 text-emerald-800 text-[11px] leading-relaxed">
                                ℹ️ Namba ya Kampuni (Business Number) ni <strong>888999</strong> na Reference ni Namba ya Malipo (Control Number): <strong class="font-mono text-amber-600" x-text="payment.control_number || 'SASXXXXXXXXXXX'"></strong>.
                            </div>

                            <!-- Vodacom M-Pesa -->
                            <div class="space-y-1.5">
                                <h4 class="font-extrabold text-orange-600 text-[11px] uppercase tracking-wider">Vodacom M-Pesa</h4>
                                <ol class="list-decimal pl-4 space-y-1 text-slate-600 leading-normal">
                                    <li>Piga <strong>*150*00#</strong>, kisha chagua <strong>4 [Lipa kwa M-Pesa]</strong></li>
                                    <li>Chagua <strong>4 [Weka namba ya kampuni / Enter Business Number]</strong></li>
                                    <li>Ingiza Namba ya Kampuni: <strong>888999</strong></li>
                                    <li>Ingiza Kumbukumbu ya Malipo: Jaza Namba ya Malipo (Control Number) hapo juu</li>
                                    <li>Weka kiasi: <strong>TZS 20,000/=</strong>, kisha weka PIN na uthibitishe malipo.</li>
                                </ol>
                            </div>

                            <!-- Tigo Pesa -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                <h4 class="font-extrabold text-sky-600 text-[11px] uppercase tracking-wider">Tigo Pesa</h4>
                                <ol class="list-decimal pl-4 space-y-1 text-slate-600 leading-normal">
                                    <li>Piga <strong>*150*01#</strong>, kisha chagua <strong>4 [Lipia Bili / Pay Bills]</strong></li>
                                    <li>Chagua <strong>3 [Ingiza Namba ya Kampuni / Enter Business Number]</strong></li>
                                    <li>Ingiza Namba ya Kampuni: <strong>888999</strong></li>
                                    <li>Ingiza Kumbukumbu ya Malipo: Jaza Namba ya Malipo (Control Number) hapo juu</li>
                                    <li>Weka kiasi: <strong>TZS 20,000/=</strong>, kisha weka PIN na uthibitishe malipo.</li>
                                </ol>
                            </div>

                            <!-- Airtel Money -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                <h4 class="font-extrabold text-red-600 text-[11px] uppercase tracking-wider">Airtel Money</h4>
                                <ol class="list-decimal pl-4 space-y-1 text-slate-600 leading-normal">
                                    <li>Piga <strong>*150*60#</strong>, kisha chagua <strong>5 [Lipia Bili / Pay Bills]</strong></li>
                                    <li>Chagua <strong>4 [Ingiza Namba ya Kampuni / Enter Business Number]</strong></li>
                                    <li>Ingiza Namba ya Kampuni: <strong>888999</strong></li>
                                    <li>Ingiza Kumbukumbu ya Malipo: Jaza Namba ya Malipo (Control Number) hapo juu</li>
                                    <li>Weka kiasi: <strong>TZS 20,000/=</strong>, kisha weka PIN na uthibitishe malipo.</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Tab 2: NMB Mkononi -->
                        <div x-show="activeTab === 'nmb_mkononi'" class="space-y-4" x-cloak>
                            <!-- NMB Mkononi USSD -->
                            <div class="space-y-1.5">
                                <h4 class="font-extrabold text-blue-900 text-[11px] uppercase tracking-wider">NMB Mkononi (*150*66#)</h4>
                                <ol class="list-decimal pl-4 space-y-1 text-slate-600 leading-normal">
                                    <li>Dial/Piga <strong>*150*66#</strong> kwenye simu yako.</li>
                                    <li>Weka namba ya siri (PIN) ya NMB Mkononi.</li>
                                    <li>Chagua <strong>2 [LIPA BILI / PAY BILLS]</strong>.</li>
                                    <li>Chagua <strong>5 [CHAGUA BIASHARA / CHOOSE BUSINESS]</strong>.</li>
                                    <li>Chagua <strong>3 [WEKA NAMBA YA BIASHARA / ENTER BUSINESS NUMBER]</strong>.</li>
                                    <li>Weka namba ya biashara: <strong>999999</strong>.</li>
                                    <li>Weka kumbukumbu (Reference number): Ingiza Namba ya Malipo (Control Number) hapo juu.</li>
                                    <li>Ingiza kiasi: <strong>TZS 20,000/=</strong> kisha thibitisha kwa kuweka PIN yako.</li>
                                </ol>
                            </div>

                            <!-- NMB Mkononi App -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                <h4 class="font-extrabold text-blue-900 text-[11px] uppercase tracking-wider">NMB Mkononi App</h4>
                                <ol class="list-decimal pl-4 space-y-1 text-slate-600 leading-normal">
                                    <li>Fungua NMB Mkononi App na uingize PIN yako.</li>
                                    <li>Chagua <strong>Bill Payment (Malipo ya Bili)</strong>.</li>
                                    <li>Chagua <strong>Other Billers (Watoa Bili Wengine)</strong>.</li>
                                    <li>Kwenye sanduku la utafutaji (Search), tafuta na uchague <strong>SINGIDA TEACHERS COLLEGE</strong>.</li>
                                    <li>Weka Reference Number: Jaza Namba ya Malipo (Control Number) hapo juu.</li>
                                    <li>Ingiza kiasi cha malipo, thibitisha taarifa na ukamilishe malipo.</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Tab 3: NMB Branch / Wakala -->
                        <div x-show="activeTab === 'bank'" class="space-y-4" x-cloak>
                            <!-- NMB Branches -->
                            <div class="space-y-1.5">
                                <h4 class="font-extrabold text-blue-950 text-[11px] uppercase tracking-wider">Kupitia Tawi la NMB (Branch Counter)</h4>
                                <p class="text-slate-500 text-[10px] font-semibold leading-relaxed mb-1">
                                    Jaza karatasi ya malipo (Bills Payment Slip) inayopatikana katika matawi yote ya NMB kote nchini:
                                </p>
                                <ol class="list-decimal pl-4 space-y-1 text-slate-600 leading-normal">
                                    <li>Andika <strong>Bill Number</strong>: Jaza Namba ya Malipo (Control Number) hapo juu</li>
                                    <li>Andika <strong>Biller Name</strong>: Jaza <strong>SINGIDA TEACHERS COLLEGE</strong></li>
                                    <li>Jaza kiasi: <strong>TZS 20,000/=</strong></li>
                                    <li>Wasilisha karatasi ya malipo na fedha taslimu kwa keshia wa benki ili kukamilisha muamala.</li>
                                </ol>
                            </div>

                            <!-- NMB Wakala -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                <h4 class="font-extrabold text-blue-950 text-[11px] uppercase tracking-wider">Kupitia NMB Wakala (NMB Agent)</h4>
                                <p class="text-slate-500 text-[10px] font-semibold leading-relaxed mb-1">
                                    Hakikisha wakala anatumia mfumo sahihi wa NMB Bills Payment na anakupatia risiti rasmi ya benki:
                                </p>
                                <ol class="list-decimal pl-4 space-y-1 text-slate-600 leading-normal">
                                    <li>Mpatie wakala Namba ya Malipo (Control Number) hapo juu.</li>
                                    <li>Mwambie mlipwaji ni <strong>SINGIDA TEACHERS COLLEGE</strong>.</li>
                                    <li>Mpatie kiasi cha fedha taslimu (<strong>TZS 20,000/=</strong>).</li>
                                    <li>Wakala atakamilisha malipo na kukupatia risiti rasmi iliyochapishwa na benki.</li>
                                </ol>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Real-time Automatic Payment Detection Monitor -->
                <div class="p-6 rounded-3xl border transition-all duration-300 shadow-sm"
                     :class="payment.status === 'paid' ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-300 text-slate-900'">
                    
                    <!-- When Payment is Paid -->
                    <template x-if="payment.status === 'paid'">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-3xl shadow-lg shrink-0">
                                    ✓
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-700 block">Uthibitisho wa Malipo</span>
                                    <h4 class="text-lg font-black text-emerald-900">Malipo ya TZS 20,000 Yamethibitishwa Kikamilifu!</h4>
                                    <p class="text-xs text-emerald-800">
                                        Mfumo umepokea na kuthibitisha ada yako ya maombi. Unaweza kuendelea sasa kwenye hatua ya tamko la mwombaji.
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="currentStep = 7" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl shrink-0">
                                Next: Tamko la Mwombaji &rarr;
                            </button>
                        </div>
                    </template>

                    <!-- When Payment is Pending Auto-Detection -->
                    <template x-if="payment.status !== 'paid'">
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex items-center justify-center">
                                        <div class="w-4 h-4 rounded-full bg-amber-500 animate-ping absolute"></div>
                                        <div class="w-4 h-4 rounded-full bg-amber-600 relative"></div>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900">
                                            Mfumo Unasubiri Malipo Yako Kiotomatiki (Live Detection Active)
                                        </h4>
                                        <p class="text-xs text-slate-600">
                                            Lipa TZS 20,000 kupitia Control Number hapo juu. Mfumo unakagua mtandao wa NMB kila baada ya sekunde chache na utakupeleka kwenye hatua ya tamko kiotomatiki mara tu unapomaliza kulipa.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="checkPaymentStatus(true)" 
                                        :disabled="checkingPaymentStatus" 
                                        class="shrink-0 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition-all flex items-center gap-1.5">
                                    <span x-show="!checkingPaymentStatus">🔄 Thibitisha Malipo Sasa</span>
                                    <span x-show="checkingPaymentStatus" x-cloak class="flex items-center gap-1">
                                        <svg class="animate-spin h-3.5 w-3.5 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Inakagua Mtandao...
                                    </span>
                                </button>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-amber-100/70 border border-amber-200 text-amber-900 text-xs flex items-center gap-2">
                                <span class="text-base">💡</span>
                                <span><strong>Kumbuka:</strong> Huna haja ya kupakia risiti wala namba ya muamala. Fanya malipo kupitia simu au benki, na mfumo utafungua hatua inayofuata mara moja.</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <button type="button" @click="currentStep = (payment.status === 'paid' ? 5 : 4)" class="px-6 py-3.5 rounded-2xl bg-slate-200 text-xs font-extrabold" x-text="payment.status === 'paid' ? 'Back: Weka Vyeti' : 'Back: Uchaguzi wa Programu'"></button>
                    
                    <!-- If Payment Verified, Allow proceeding to Step 5 -->
                    <template x-if="payment.status === 'paid'">
                        <button type="button" @click="currentStep = 5" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl flex items-center gap-2">
                            <span>Next: Pakia Vyeti / Upload Certificates &rarr;</span>
                        </button>
                    </template>

                    <!-- If Payment NOT detected yet -->
                    <template x-if="payment.status !== 'paid'">
                        <button type="button" @click="checkPaymentStatus(true)" class="px-8 py-3.5 rounded-2xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-sm shadow-md flex items-center gap-2 transition-all">
                            <span class="animate-pulse">🔒 Inasubiri Malipo... (Bonyeza Kuhakiki)</span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Step 5: Document Upload & Checklist -->
            <div x-show="currentStep === 5" x-cloak class="space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">Step 5: Orodha ya Vyeti na Nyaraka (Certificates & Documents)</h3>
                            <p class="text-xs text-slate-500" x-text="academic.admission_type === 'Form Six' ? 'Sehemu ya 4: Pakia Cheti chako cha Kidato cha Sita (Form VI / ACSEE) na Matokeo (Transcript) ili kukamilisha maombi.' : 'Sehemu ya 4: Pakia vyeti na nyaraka zako zote zinazohitajika kukamilisha maombi ya udahili.'"></p>
                        </div>
                        <span class="px-3.5 py-1.5 rounded-full bg-blue-100 text-blue-800 text-xs font-extrabold uppercase shrink-0">
                            ⏳ Documents Upload
                        </span>
                    </div>
                </div>

                <!-- Individual Document Upload Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-bold">
                    
                    <!-- 1. Form IV Certificate (Only for Diploma applicants) -->
                    <div x-show="academic.admission_type !== 'Form Six'" x-cloak class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-extrabold text-slate-900 block text-sm">1. Cheti cha Form IV (CSEE)</span>
                                <span class="text-[10px] text-slate-500 font-semibold block truncate" x-text="uploadedDocs.form4.name || 'Hakuna faili iliyowekwa'"></span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase shrink-0"
                                  :class="!uploadedDocs.form4.status ? 'bg-amber-100 text-amber-800' : (uploadedDocs.form4.verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : (uploadedDocs.form4.verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))"
                                  x-text="!uploadedDocs.form4.status ? 'Pending Upload' : (uploadedDocs.form4.verification_status === 'verified' ? '✓ Verified' : (uploadedDocs.form4.verification_status === 'rejected' ? '⚠️ Rejected' : '⏳ Pending Review'))">
                            </span>
                        </div>

                        <template x-if="uploadedDocs.form4.status && uploadedDocs.form4.verification_status === 'rejected'">
                            <div class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold">
                                Reason: <span x-text="uploadedDocs.form4.rejection_comment"></span>
                            </div>
                        </template>

                        <div class="flex items-center space-x-2 pt-1">
                            <input type="file" accept="application/pdf,image/*" @change="handleDocUpload($event, 'form4')" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:bg-blue-600 file:text-white cursor-pointer">
                            <button type="button" @click="openPreviewDoc('Cheti cha Form IV (CSEE)', uploadedDocs.form4)" x-show="uploadedDocs.form4.status" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] shrink-0 transition-colors">
                                Verify & Preview
                            </button>
                        </div>
                    </div>

                    <!-- 2. Form VI / Diploma Certificate -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-extrabold text-slate-900 block text-sm" x-text="academic.admission_type === 'Form Six' ? '1. Cheti cha Form VI (ACSEE)' : '2. Cheti cha Stashahada / Diploma'"></span>
                                <span class="text-[10px] text-slate-500 font-semibold block truncate" x-text="uploadedDocs.form6_diploma.name || 'Hakuna faili iliyowekwa'"></span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase shrink-0"
                                  :class="!uploadedDocs.form6_diploma.status ? 'bg-amber-100 text-amber-800' : (uploadedDocs.form6_diploma.verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : (uploadedDocs.form6_diploma.verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))"
                                  x-text="!uploadedDocs.form6_diploma.status ? 'Pending Upload' : (uploadedDocs.form6_diploma.verification_status === 'verified' ? '✓ Verified' : (uploadedDocs.form6_diploma.verification_status === 'rejected' ? '⚠️ Rejected' : '⏳ Pending Review'))">
                            </span>
                        </div>

                        <template x-if="uploadedDocs.form6_diploma.status && uploadedDocs.form6_diploma.verification_status === 'rejected'">
                            <div class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold">
                                Reason: <span x-text="uploadedDocs.form6_diploma.rejection_comment"></span>
                            </div>
                        </template>

                        <div class="flex items-center space-x-2 pt-1">
                            <input type="file" accept="application/pdf,image/*" @change="handleDocUpload($event, 'form6_diploma')" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:bg-blue-600 file:text-white cursor-pointer">
                            <button type="button" @click="openPreviewDoc(academic.admission_type === 'Form Six' ? 'Cheti cha Form VI (ACSEE)' : 'Cheti cha Stashahada / Diploma', uploadedDocs.form6_diploma)" x-show="uploadedDocs.form6_diploma.status" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] shrink-0 transition-colors">
                                Verify & Preview
                            </button>
                        </div>
                    </div>

                    <!-- 3. Academic Transcript -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-extrabold text-slate-900 block text-sm" x-text="academic.admission_type === 'Form Six' ? '2. Academic Transcript / Matokeo ya Form VI' : '3. Academic Transcript Record'"></span>
                                <span class="text-[10px] text-slate-500 font-semibold block truncate" x-text="uploadedDocs.transcript.name || 'Hakuna faili iliyowekwa'"></span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase shrink-0"
                                  :class="!uploadedDocs.transcript.status ? 'bg-amber-100 text-amber-800' : (uploadedDocs.transcript.verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : (uploadedDocs.transcript.verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))"
                                  x-text="!uploadedDocs.transcript.status ? 'Pending Upload' : (uploadedDocs.transcript.verification_status === 'verified' ? '✓ Verified' : (uploadedDocs.transcript.verification_status === 'rejected' ? '⚠️ Rejected' : '⏳ Pending Review'))">
                            </span>
                        </div>

                        <template x-if="uploadedDocs.transcript.status && uploadedDocs.transcript.verification_status === 'rejected'">
                            <div class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold">
                                Reason: <span x-text="uploadedDocs.transcript.rejection_comment"></span>
                            </div>
                        </template>

                        <div class="flex items-center space-x-2 pt-1">
                            <input type="file" accept="application/pdf,image/*" @change="handleDocUpload($event, 'transcript')" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:bg-blue-600 file:text-white cursor-pointer">
                            <button type="button" @click="openPreviewDoc('Academic Transcript Record', uploadedDocs.transcript)" x-show="uploadedDocs.transcript.status" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] shrink-0 transition-colors">
                                Verify & Preview
                            </button>
                        </div>
                    </div>

                    <!-- 4. NIDA / Voter ID (Only for Diploma applicants) -->
                    <div x-show="academic.admission_type !== 'Form Six'" x-cloak class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-extrabold text-slate-900 block text-sm">4. Kitambulisho (NIDA / Kura)</span>
                                <span class="text-[10px] text-slate-500 font-semibold block truncate" x-text="uploadedDocs.nida_id.name || 'Hakuna faili iliyowekwa'"></span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase shrink-0"
                                  :class="!uploadedDocs.nida_id.status ? 'bg-amber-100 text-amber-800' : (uploadedDocs.nida_id.verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : (uploadedDocs.nida_id.verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))"
                                  x-text="!uploadedDocs.nida_id.status ? 'Pending Upload' : (uploadedDocs.nida_id.verification_status === 'verified' ? '✓ Verified' : (uploadedDocs.nida_id.verification_status === 'rejected' ? '⚠️ Rejected' : '⏳ Pending Review'))">
                            </span>
                        </div>

                        <template x-if="uploadedDocs.nida_id.status && uploadedDocs.nida_id.verification_status === 'rejected'">
                            <div class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold">
                                Reason: <span x-text="uploadedDocs.nida_id.rejection_comment"></span>
                            </div>
                        </template>

                        <div class="flex items-center space-x-2 pt-1">
                            <input type="file" accept="application/pdf,image/*" @change="handleDocUpload($event, 'nida_id')" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:bg-blue-600 file:text-white cursor-pointer">
                            <button type="button" @click="openPreviewDoc('Kitambulisho (NIDA / Kura)', uploadedDocs.nida_id)" x-show="uploadedDocs.nida_id.status" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] shrink-0 transition-colors">
                                Verify & Preview
                            </button>
                        </div>
                    </div>

                    <!-- 5. Passport Photos (Only for Diploma applicants) -->
                    <div x-show="academic.admission_type !== 'Form Six'" x-cloak class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-extrabold text-slate-900 block text-sm">5. Picha 2 za Passport Size</span>
                                <span class="text-[10px] text-slate-500 font-semibold block truncate" x-text="uploadedDocs.passport_photos.name || 'Hakuna faili iliyowekwa'"></span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase shrink-0"
                                  :class="!uploadedDocs.passport_photos.status ? 'bg-amber-100 text-amber-800' : (uploadedDocs.passport_photos.verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : (uploadedDocs.passport_photos.verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))"
                                  x-text="!uploadedDocs.passport_photos.status ? 'Pending Upload' : (uploadedDocs.passport_photos.verification_status === 'verified' ? '✓ Verified' : (uploadedDocs.passport_photos.verification_status === 'rejected' ? '⚠️ Rejected' : '⏳ Pending Review'))">
                            </span>
                        </div>

                        <template x-if="uploadedDocs.passport_photos.status && uploadedDocs.passport_photos.verification_status === 'rejected'">
                            <div class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold">
                                Reason: <span x-text="uploadedDocs.passport_photos.rejection_comment"></span>
                            </div>
                        </template>

                        <div class="flex items-center space-x-2 pt-1">
                            <input type="file" accept="image/*" @change="handleDocUpload($event, 'passport_photos')" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:bg-blue-600 file:text-white cursor-pointer">
                            <button type="button" @click="openPreviewDoc('Picha ya Passport', uploadedDocs.passport_photos)" x-show="uploadedDocs.passport_photos.status" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] shrink-0 transition-colors">
                                Verify & Preview
                            </button>
                        </div>
                    </div>

                </div>

                <!-- PREVIEW DOCUMENT VERIFICATION MODAL -->
                <div x-show="previewDocModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
                    <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                            <h3 class="text-base font-extrabold text-slate-900" x-text="'Verification Preview: ' + activePreviewDoc.title"></h3>
                            <button type="button" @click="previewDocModal = false" class="text-slate-400 hover:text-slate-600 font-black text-sm">✕</button>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 space-y-3 text-center">
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">✓ Document Verified</span>
                            <p class="text-xs font-bold text-slate-800" x-text="'Filename: ' + activePreviewDoc.name"></p>
                            
                            <template x-if="activePreviewDoc.url && (activePreviewDoc.url.startsWith('data:image') || activePreviewDoc.url.match(/\.(jpg|jpeg|png|gif|webp)$/i))">
                                <img :src="activePreviewDoc.url" alt="Document Preview" class="w-full h-48 rounded-xl object-contain border border-slate-300 mx-auto">
                            </template>

                            <template x-if="activePreviewDoc.url && !activePreviewDoc.url.startsWith('data:image') && !activePreviewDoc.url.match(/\.(jpg|jpeg|png|gif|webp)$/i)">
                                <div class="p-8 rounded-xl bg-blue-500/10 text-blue-500 text-center font-extrabold text-xs">
                                    📄 PDF Document Attached & Validated
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="button" @click="previewDocModal = false" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs">
                                Confirm & Close Preview
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <button type="button" @click="currentStep = 6" class="px-6 py-3.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Nyuma: Malipo ya Ada</button>
                    <button type="button" @click="currentStep = 7" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl">Hatua Inayofuata: Tamko la Mwombaji &rarr;</button>
                </div>
            </div>

            <!-- Step 7: Declaration & Digital Signature Submission -->
            <div x-show="currentStep >= 7 && !showClaimAccountForm" x-cloak class="space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-extrabold text-slate-900">Hatua ya 7: Tamko la Mwombaji & Kuwasilisha Maombi</h3>
                    <p class="text-xs text-slate-500">Saini ya tamko rasmi, idhini ya ulinzi wa taarifa binafsi, na kuwasilisha maombi kwenye Ofisi ya Udahili ya OUT / STTC SUPA.</p>
                </div>

                <!-- Privacy Notice Explanatory Box -->
                <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200 space-y-3 text-xs leading-relaxed text-slate-600">
                    <span class="font-extrabold uppercase text-blue-900 block">ILANI YA ULINZI WA TAARIFA BINAFSI:</span>
                    <div class="h-64 overflow-y-auto bg-white p-4.5 rounded-2xl border border-slate-200 space-y-4 text-[11px] text-slate-500">
                        <h4 class="font-extrabold text-slate-900 text-xs uppercase">FOMU YA RIDHAA YA UDAHILI WA CHUO</h4>
                        <p>Kwa kuwasilisha maombi haya mtandaoni, unakiri na kutambua kwamba <strong>{{ \App\Models\Setting::get('university_name', 'STTC / OUT') }}</strong> ("Chuo") kitakusanya, kutumia, kuhifadhi, na kuchakata taarifa zako binafsi kwa madhumuni ya kushughulikia maombi yako ya udahili na shughuli nyingine zinazohusiana na taaluma na utawala.</p>
                        <p>Chuo kinakusanya taarifa zako binafsi kwa mujibu wa <strong>Sheria ya Ulinzi wa Taarifa Binafsi ya Mwaka 2022 (Jamhuri ya Muungano wa Tanzania)</strong> na sheria nyingine zinazotumika.</p>
                        
                        <div>
                            <span class="font-bold text-slate-800 block mb-1">Taarifa Tunazokusanya:</span>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Jina Kamili</li>
                                <li>Tarehe ya Kuzaliwa</li>
                                <li>Jinsia</li>
                                <li>Namba ya Kitambulisho cha Taifa (NIDA)</li>
                                <li>Namba ya Pasipoti (kwa waombaji wa kimataifa)</li>
                                <li>Uraia</li>
                                <li>Anwani ya Posta na Makazi</li>
                                <li>Barua Pepe (Email)</li>
                                <li>Namba ya Simu ya Mkononi</li>
                                <li>Taarifa za Mzazi / Mlezi</li>
                                <li>Kumbukumbu za Kitaaluma na Vyeti</li>
                                <li>Matokeo ya Mitihani</li>
                                <li>Taarifa za Ajira (inapohusika)</li>
                                <li>Picha ya Pasipoti (Passport-size Photograph)</li>
                                <li>Nyaraka na Viambatisho vilivyopakiwa wakati wa mchakato wa maombi</li>
                                <li>Taarifa za Malipo</li>
                                <li>Kumbukumbu za Mfumo (Anwani ya IP, Kivinjari, Taarifa za Kifaa)</li>
                            </ul>
                        </div>
                        
                        <div>
                            <span class="font-bold text-slate-800 block mb-1">Madhumuni ya Kukusanya Taarifa Zako:</span>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Kuchakata maombi yako ya udahili</li>
                                <li>Kuhakiki utambulisho wako</li>
                                <li>Kuhakiki sifa na vyeti vya kitaaluma</li>
                                <li>Kutoa taarifa za uamuzi wa udahili</li>
                                <li>Kuchakata malipo ya ada ya maombi</li>
                                <li>Kutoa barua rasmi za udahili (Admission Letters)</li>
                                <li>Kutengeneza akaunti yako ya mwanafunzi</li>
                                <li>Kuzingatia matakwa ya kisheria na kikanuni</li>
                                <li>Kuandaa ripoti za takwimu kwa ajili ya mipango</li>
                                <li>Kutunza kumbukumbu za mwanafunzi katika kipindi chote cha masomo</li>
                            </ul>
                        </div>
                        
                        <div>
                            <span class="font-bold text-slate-800 block mb-1">Utoaji na Ushirikishaji wa Taarifa:</span>
                            <p>Taarifa zako zinaweza kushirikiwa pale tu inapobidi na:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Tume ya Vyuo Vikuu Tanzania (TCU)</li>
                                <li>Baraza la Mitihani la Tanzania (NECTA)</li>
                                <li>Baraza la Taifa la Elimu ya Ufundi na Mafunzo ya Ufundi Stadi (NACTVET), inapohusika</li>
                                <li>Mamlaka za Serikali pale inapolazimu kisheria</li>
                                <li>Watoa huduma za mifumo ya malipo</li>
                                <li>Watoa huduma za kiteknolojia walioingia mikataba ya usiri na Chuo</li>
                            </ul>
                            <p class="mt-1">Taarifa zako binafsi hazitauzwa wala kutolewa kwa madhumuni ya kibiashara au matangazo bila idhini yako ya wazi.</p>
                        </div>
                        
                        <div>
                            <span class="font-bold text-slate-800 block mb-1">Usalama wa Taarifa:</span>
                            <p>Chuo kinaweka hatua madhubuti za kiufundi na kiutawala ili kulinda taarifa zako binafsi dhidi ya ufikiaji usioidhinishwa, kubadilishwa, kufichuliwa au kuharibiwa.</p>
                        </div>
                        
                        <div>
                            <span class="font-bold text-slate-800 block mb-1">Uhifadhi wa Taarifa:</span>
                            <p>Taarifa zako binafsi zitahifadhiwa kwa muda unaohitajika tu ili kukamilisha madhumuni yaliyotajwa hapo juu au kama inavyotakiwa na sheria zinazotumika na sera za uhifadhi kumbukumbu za Chuo.</p>
                        </div>
                        
                        <div>
                            <span class="font-bold text-slate-800 block mb-1">Haki Zako:</span>
                            <p>Chini ya Sheria ya Ulinzi wa Taarifa Binafsi, unayo haki ya:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Kutaarifiwa kuhusu uchakataji wa taarifa zako binafsi.</li>
                                <li>Kuomba kuona au kupata nakala ya taarifa zako binafsi.</li>
                                <li>Kuomba kurekebishwa kwa taarifa zisizo sahihi.</li>
                                <li>Kuomba kufutwa kwa taarifa binafsi pale inapokubalika kisheria.</li>
                                <li>Kuzuia au kuweka mipaka ya uchakataji katika mazingira fulani.</li>
                                <li>Kupinga aina fulani za uchakataji wa taarifa.</li>
                                <li>Kusitisha au kuondoa idhini yako pale ambapo uchakataji unategemea ridhaa.</li>
                                <li>Kufungua malalamiko kwenye mamlaka husika iwapo unaamini haki zako zimekiukwa.</li>
                            </ul>
                            <p class="mt-1">Haki hizi zinaweza kuwa na mipaka kwa mujibu wa sheria.</p>
                        </div>
                    </div>
                </div>

                <!-- Applicant Declaration Section -->
                <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200 space-y-3 text-xs leading-relaxed text-slate-600">
                    <span class="font-extrabold uppercase text-slate-900 block text-xs">Tamko la Mwombaji:</span>
                    <p class="font-semibold text-slate-800">Ninatamka rasmi kwamba:</p>
                    <ul class="list-disc pl-4 space-y-1.5 text-slate-700 font-medium">
                        <li>Taarifa zote nilizotoa katika fomu hii ya maombi ni za kweli, kamili, na sahihi.</li>
                        <li>Ninaelewa kuwa kutoa taarifa za uongo au za kupotosha kunaweza kupelekea kukataliwa kwa maombi yangu au kufutwa kwa udahili wangu.</li>
                        <li>Nimesoma na kuelewa Sera ya Faragha ya Chuo na Ilani ya Ulinzi wa Taarifa Binafsi.</li>
                        <li>Ninatoa ridhaa yangu kwa hiari kwa ukusanyaji, uhifadhi, utumiaji, uhakiki, na uchakataji wa taarifa zangu binafsi kwa madhumuni yanayohusiana na maombi yangu na, iwapo nitadahiliwa, uendeshaji wa shughuli zangu za kitaaluma.</li>
                        <li>Ninaelewa kuwa taarifa zangu binafsi zinaweza kushirikishwa na taasisi zilizoidhinishwa pale inapolazimu kisheria au kwa ajili ya uhakiki.</li>
                        <li>Ninaelewa haki zangu chini ya Sheria ya Ulinzi wa Taarifa Binafsi ya Mwaka 2022.</li>
                    </ul>
                </div>

                <!-- Declarations & Consent Checklist -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="chk_consent" x-model="consentGiven" class="mt-0.5 rounded border-slate-300 text-amber-500 focus:ring-amber-500 cursor-pointer">
                        <label for="chk_consent" class="cursor-pointer select-none leading-normal text-xs font-bold text-slate-800">
                            Nimesoma, nimeelewa na nimekubaliana na <a href="{{ route('public.privacy') }}" target="_blank" class="text-blue-600 hover:underline">Sera ya Faragha ya Chuo</a>, na ninatoa ridhaa ya kukusanywa na kuchakatwa kwa taarifa zangu binafsi kwa ajili ya udahili na masuala ya kitaaluma kwa mujibu wa Sheria ya Ulinzi wa Taarifa Binafsi ya Mwaka 2022.
                        </label>
                    </div>

                    <!-- Parent / Guardian Consent (Only visible if under 18) -->
                    <div x-show="isUnder18()" class="p-5 rounded-3xl bg-amber-500/5 border border-amber-200 space-y-4" x-cloak>
                        <span class="font-extrabold uppercase text-amber-800 text-xs block">Ikiwa Mwombaji Yuko Chini ya Umri wa Miaka 18:</span>
                        
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="chk_parent_consent" x-model="parentConsentGiven" class="mt-0.5 rounded border-slate-300 text-amber-500 focus:ring-amber-500 cursor-pointer">
                            <label for="chk_parent_consent" class="cursor-pointer select-none leading-normal text-xs font-bold text-amber-900">
                                Mimi ni mzazi/mlezi halali wa mwombaji na ninatoa idhini ya kukusanywa na kuchakatwa kwa taarifa binafsi za mwombaji.
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-semibold">
                            <div>
                                <label class="block text-slate-700 uppercase mb-1">Jina Kamili la Mzazi/Mlezi</label>
                                <input type="text" x-model="parentName" placeholder="Jina Kamili la Mzazi au Mlezi" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-slate-700 uppercase mb-1">Saini ya Mzazi/Mlezi</label>
                                <input type="text" x-model="parentSignature" placeholder="Saini au Jina la Mzazi" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-slate-700 uppercase mb-1">Tarehe ya Idhini</label>
                                <input type="date" x-model="parentConsentDate" readonly class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-100 text-slate-500 outline-none cursor-not-allowed">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Digital Signature Input -->
                <div class="pt-2">
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Saini ya Mwombaji (Digital Signature)</label>
                    <input type="text" x-model="signatureData" placeholder="Andika Jina Lako Kamili kama Saini (k.m. {{ $user->name }})" 
                           class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="flex justify-between items-center pt-4">
                    <button type="button" @click="currentStep = 5" class="px-6 py-3.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Nyuma: Weka Vyeti</button>
                    <button type="button" @click="submitFinal()" :disabled="loading || !consentGiven || payment.status !== 'paid' || (isUnder18() && (!parentConsentGiven || !parentName || !parentSignature))" 
                            class="gradient-btn-gold px-10 py-4 rounded-2xl text-slate-950 font-black text-base shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 transition-transform">
                        <span x-show="!loading">Wasilisha Fomu ya Udahili &rarr;</span>
                        <span x-show="loading" x-cloak>Inawasilisha Maombi...</span>
                    </button>
                </div>
            </div>

            <!-- Claim Guest Account Screen -->
            <div x-show="showClaimAccountForm" x-cloak class="space-y-6">
                <div class="text-center space-y-4">
                    <span class="text-5xl">🎉</span>
                    <h3 class="text-2xl font-black text-slate-900">Maombi Yako Yamepokelewa kikamilifu!</h3>
                    <p class="text-xs text-slate-500 max-w-lg mx-auto leading-relaxed">
                        Hongera! Umefanikiwa kuwasilisha fomu ya maombi ya udahili. Ili uweze kufuatilia mrejesho na kupakua barua ya udahili (Admission Letter) baadaye, tafadhali weka nenosiri (password) la akaunti yako hapa chini.
                    </p>
                </div>

                <form @submit.prevent="claimGuestAccount()" class="max-w-md mx-auto space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Nenosiri Jipya (New Password)</label>
                        <input type="password" x-model="claimPassword.password" required class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Thibitisha Nenosiri (Confirm Password)</label>
                        <input type="password" x-model="claimPassword.password_confirmation" required class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" :disabled="loading" class="gradient-btn px-6 py-3.5 rounded-2xl text-white font-extrabold text-xs shadow-xl w-full">
                            <span x-show="!loading">Hifadhi Akaunti na Nenda kwenye Dashboard &rarr;</span>
                            <span x-show="loading" x-cloak>Saving...</span>
                        </button>
                        <button type="button" @click="logoutGuest()" class="px-6 py-3.5 rounded-2xl bg-slate-200 text-slate-700 font-extrabold text-xs w-full block hover:bg-slate-300 transition-colors">
                            Ondoka bila kuhifadhi (Logout & Exit)
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</x-app-layout>
