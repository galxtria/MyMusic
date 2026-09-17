<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Song;

class SongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $songs = [
            [
                'title' => "Don't Look Back in Anger",
                'artist' => 'Oasis',
                'album_art' => '/images/oasis_morningglory.jpg',
                'artist_image' => '/images/artist/oasis.jpg',
                'file_path' => 'oasis_dontlookback.mp3', 
                'duration' => '4:48',
                'lyrics' => "
                [00:11.50] Slip inside the eye of your mind
                [00:15.50] Don't you know you might find
                [00:19.00] A better place to play
                [00:23.50] You said that you'd never been
                [00:27.40] But all the things that you've seen
                [00:31.00] Slowly fade away
                [00:35.00] So I start a revolution from my bed
                [00:40.50] 'Cause you said the brains I had went to my head
                [00:46.70] Step outside, summertime's in bloom
                [00:52.00] Stand up beside the fireplace
                [00:55.40] Take that look from off your face
                [00:58.50] You ain't ever gonna burn my heart out
                [01:09.50] And so Sally can wait
                [01:14.00] She knows it's too late as we're walking on by
                [01:21.50] Her soul slides away
                [01:27.00] But don't look back in anger
                [01:30.20] I heard you say
                [01:42.50] Take me to the place where you go
                [01:46.50] Where nobody knows
                [01:50.30] If it's night or day
                [01:54.10] Please don't put your life in the hands
                [01:58.20] Of a rock and roll band
                [02:01.70] Who'll throw it all away
                [02:06.20] I'm gonna start a revolution from my bed
                [02:11.40] 'Cause you said the brains I had went to my head
                [02:17.50] Step outside, summertime's in bloom
                [02:23.50] Stand up beside the fireplace
                [02:26.10] Take that look from off your face
                [02:29.00] 'Cause you ain't ever gonna burn my heart out
                [02:40.60] And so Sally can wait
                [02:45.00] She knows it's too late as she's walking on by
                [02:52.00] My soul slides away
                [02:57.50] But don't look back in anger
                [03:00.70] I heard you say
                [03:39.70] And so Sally can wait
                [03:43.20] She knows it's too late as we're walking on by
                [03:51.20] Her soul slides away
                [03:56.10] But don't look back in anger
                [03:59.10] I heard you say
                [04:02.90] So Sally can wait
                [04:07.00] She knows it's too late as she's walking on by
                [04:14.20] My soul slides away
                [04:19.80] But don't look back in anger
                [04:23.00] Don't look back in anger
                [04:28.90] I heard you say
                [04:37.50] At least not today"
            ],
            [
                'title' => 'Everything you are',
                'artist' => 'Hindia',
                'album_art' => '/images/hindia_doves.jpg',
                'artist_image' => '/images/artist/hindia.jpg',
                'file_path' => 'hindia_everythingyouare.mp3',
                'duration' => '3:50',
                'lyrics' => "
                [00:12.00] Wajahmu
                [00:16.50] Kuingat selalu Lupakan
                [00:22.50] Hal-hal yang menggangguku
                [00:25.70] Karena hari ini 
                [00:28.50] Mata kita beradu
                [00:31.50] Kita saling bantu
                [00:34.50] Melepas perasaan
                [00:37.90] Tinggi ke angkasa
                [00:41.00] Menantang dunia
                [00:44.00] Merayakan muda 
                [00:47.00] Tuk satu jam saja
                [00:49.20] Kita hampir mati 
                [00:51.90] Dan kau selamatkan aku
                [00:54.50] Dan ku menyelamatkanmu 
                [00:57.50] Dan sekarang aku tahu
                [01:01.00] Cerita kita tak jauh berbeda
                [01:08.50] Got beat down by the world
                [01:11.80] Sometimes I wanna fold
                [01:13.70] Namun suratmu 'kan kuceritakan
                [01:16.20] Ke anak-anakku nanti
                [01:19.50] Bahwa aku pernah dicintai
                [01:24.20] With everything you are
                [01:30.50] Fully as I am 
                [01:33.20] With everything you are
                [01:38.00] Wajahmu yang beragam rupa pastikan
                [01:48.50] Ku tak sendirian jalani derita
                [01:55.00] Kau bawakan kisahmu, aku mendengarkan
                [02:01.00] Oh, kita bergantian bertukar nestapa
                [02:07.20] Menawar trauma
                [02:10.50] Datang seadanya
                [02:13.50] Terasku terbuka
                [02:15.50] Kita hampir mati 
                [02:18.00] Dan kau selamatkan aku
                [02:21.00] Dan ku menyelamatkanmu 
                [02:24.00] Dan sekarang aku tahu
                [02:27.50] Cerita kita tak jauh berbeda
                [02:35.00] Got beat down by the world
                [02:38.00] Sometimes I wanna fold
                [02:39.70] Namun suratmu 'kan kuceritakan
                [02:42.80] Ke anak-anakku nanti
                [02:45.50] Bahwa aku pernah dicintai
                [02:50.50] Seada-adanya
                [02:53.50] Sekurang-kurangnya
                [02:56.10] Walau sulit utarakan hatiku dengan indah
                [03:02.20] Walau jarang ku bernyanyi dengan cara yang indah
                [03:08.80] Tapi tak sekali pun kisahku pernah kaubantah
                [03:15.80] Cerita kita tak jauh berbeda
                [03:24.00] Got beat down by the world (got beat down by the world)
                [03:27.00] Sometimes I wanna fold (sometimes I wanna fold)
                [03:29.20] Namun suratmu 'kan kuceritakan
                [03:31.90] Ke anak-anakku nanti
                [03:34.50] Bahwa aku pernah dicintai
                [03:39.50] With everything you are
                [03:46.00] Fully as I am 
                [03:48.90] With everything you are"
            ],
            [
                'title' => 'Mejikuhibiniu',
                'artist' => 'Tenxi,Suisei,Jemsii',
                'album_art' => '/images/tenxi_mejiku.jpg',
                'artist_image' => '/images/artist/tenxi.jpg',
                'file_path' => 'tenxi_mejikuhibiniu.mp3',
                'duration' => '3:16',
                'lyrics' => "
                [00:16.50] Mejikuhibiniu ku lihat kamu
                [00:20.00] Hidup penuh warna warni saat ku bersamamu
                [00:23.90] Satu untuk semua dan semua untuk satu
                [00:27.50] Kenapa ada dia di antara kau dan aku
                [00:32.50] Di antara kau dan a-
                [00:34.50] Di antara kau dan a-
                [00:36.50] Di antara kau dan a- (ku)
                [00:40.00] Di antara kau dan a-
                [00:41.80] Di antara kau dan a-
                [00:44.00] Cuma kamu tiada yang lain
                [00:47.90] Kubilang aku gak main main
                [00:51.20] Apa yang kau mau kuturutin
                [00:54.90] Sekarang kamu sama yang lain
                [00:58.70] Awalnya ku cuma cobain
                [00:59.80] Tapi ku ketagihan
                [01:03.50] Ku bilang amin sampai ke pelaminan
                [01:07.00] Jangan disamain, semua mantanmu bajingan
                [01:11.00] Ulang lagi ku cari yang lain
                [01:28.50] Tak sepantasnya
                [01:30.50] Bilang cinta kalau nggak bisa tahan
                [01:34.00] Putar fakta, kau bilang aku yang salah
                [01:38.00] Yang tau semuanya ya cuma Tuhan, kenapa masih kurang?
                [01:42.40] Sungguh keterlaluan, ku kira takkan terulang
                [01:46.00] Saat ku nggak karuan, ku tau kamu bersulang
                [01:49.80] Memang sekarang semuanya telah hancur melebur
                [01:53.50] Tapi seenggaknya aku nggak pernah palsu
                [01:59.00] Kayak kamu, kayak kamu, kayak kamu
                [02:02.70] Cuma kamu, cuma kamu, cuma kamu
                [02:06.50] Tapi cuma ada dia di hatimu
                [02:10.00] Tapi untukku
                [02:13.50] Cuma kamu tiada yang lain
                [02:17.00] Kubilang aku gak main main
                [02:20.70] Apa yang kau mau kuturutin
                [02:24.00] Sekarang kamu sama yang lain
                [02:27.80] Awalnya ku cuma cobain
                [02:30.50] Tapi ku ketagihan
                [02:32.40] Ku bilang amin sampai ke pelaminan
                [02:35.90] Jangan disamain, semua mantanmu bajingan
                [02:40.00] Ulang lagi ku cari yang lain
                [02:49.40] Kucari yang lain
                [02:56.80] Kucari yang lain
                [03:00.50] Kucari yang lain
                [03:04.20] Kucari yang lain
                [03:07.90] Kucari yang lain
                [03:11.50] Kucari yang lain
                [03:21.00] Kucari yang lain
                [03:24.80] Kucari yang lain"
            ],
            [
                'title' => 'Super Shy',
                'artist' => 'NewJeans',
                'album_art' => '/images/newjeans_getup.jpg',
                'artist_image' => '/images/artist/newjeans.jpg',
                'file_path' => 'newjeans_supershy.mp3',
                'duration' => '2:34',
                'lyrics' => "[00:12.50] I'm super shy, super shy
                [00:14.80] But wait a minute while I make you mine, make you mine
                [00:17.90] Tteollineun jigeumdo You're on my mind all the time
                [00:21.00] I wanna tell you but I'm super shy, super shy
                [00:25.40] I'm super shy, super shy
                [00:27.50] But wait a minute while I make you mine, make you mine
                [00:30.90] Tteollineun jigeumdo You're on my mind all the time
                [00:34.00] I wanna tell you but I'm super shy, super shy
                [00:38.10] And I wanna go out with you
                [00:39.50] Where you wanna go?
                [00:40.20] Find a lil spot, just sit and talk
                [00:41.80] Looking pretty, follow me
                [00:43.40] Uri dul i naranhi
                [00:45.00] Boiji? (Nae nun i)
                [00:46.80] Gapjagi (Binnaji)
                [00:48.30] When you say I'm your dream
                [00:51.50] You don't even know my name
                [00:54.10] Do ya?
                [00:57.90] You don't even know my name
                [01:00.50] Do ya-a?
                [01:02.50] Nugu boda do
                [01:04.00] I'm super shy, super shy
                [01:06.10] But wait a minute while I make you mine, make you mine
                [01:09.10] Tteollineun jigeumdo You're on my mind all the time
                [01:12.50] I wanna tell you but I'm super shy, super shy
                [01:16.90] I'm super shy, super shy
                [01:19.00] But wait a minute while I make you mine, make you mine
                [01:22.00] Tteollineun jigeumdo You're on my mind all the time
                [01:25.10] I wanna tell you but I'm super shy, super shy
                [01:29.50] Na wollae maldo jalhago geureonde
                [01:31.50] Wae ireonji I don't like that
                [01:36.20] Something odd about you
                [01:37.50] Yeah you're special and you know it
                [01:39.10] You're the top babe
                [01:42.50] I'm super shy, super shy
                [01:44.50] But wait a minute while I make you mine, make you mine
                [01:47.70] Tteollineun jigeumdo You're on my mind all the time
                [01:50.90] I wanna tell you but I'm super shy, super shy
                [01:55.20] I'm super shy, super shy
                [01:57.20] But wait a minute while I make you mine, make you mine
                [02:00.20] Tteollineun jigeumdo You're on my mind all the time
                [02:03.70] I wanna tell you but I'm super shy, super shy
                [02:08.00] You don't even know my name
                [02:11.00] Do ya?
                [02:14.50] You don't even know my name
                [02:17.50] Do ya-a?
                [02:19.70] Nugu boda do
                [02:21.00] You don't even know my name
                [02:23.90] Do ya?
                [02:27.50] You don't even know my name
                [02:30.20] Do ya-a?"
            ],
            [
                'title' => 'FaSHioN',
                'artist' => 'Cortis',
                'album_art' => '/images/cortis.jpg',
                'artist_image' => '/images/cortis.jpg',
                'file_path' => 'cortis_fashion.mp3',
                'duration' => '3:00',
                'lyrics' => "[00:05.60] Nae ti, 5 bucks, bajineun manwon
                [00:08.70] My vision, myeot eoks, myeot jos, Bezos
                [00:11.70] Dongmyo, Wassup! Hongdae, Wassup!
                [00:15.00] I make them famous, I call that, Fashion
                [00:18.00] Fashion, Fashion, Fashion, Fashion
                [00:20.90] Fashion, Fashion, Fashion, Fashion
                [00:24.30] Nae ti, 5 bucks, bajineun manwon
                [00:27.50] Let's get it, Let's go, Fashion, Fashion
                [00:30.00] Angeonho naega san ot bogo mworago malhaedo jikyeo nae gojip
                [00:33.20] Hureucheu jjimhaenoheun sangpume isseotdeon belteuneun now on my heori
                [00:36.30] Sorry, my granny, yojeum wae an oni?
                [00:38.20] Seopasin dongmyo halmeoni
                [00:39.70] Yeogin bihaenggi, LAeseo aelbeomeul kkeunnaego
                [00:41.80] Meositge dorawa, back on my swag
                [00:44.20] Oehwa talk, hwanyul olla maeil
                [00:47.00] Guje pan, got me looking fresh
                [00:50.10] Pull up boys, syaksyak geulgeonae
                [00:53.50] Bintijijyeoseu
                [00:55.30] Dongmyoeseo moyeo machi semina
                [00:58.50] Hongdaeeseo moyeo urin set it off
                [01:01.50] Cheongdamdong hangaundekkaji spreading out
                [01:04.70] Squad is on the way, but we can't wrap it up
                [01:07.70] Nae ti, 5 bucks, bajineun manwon
                [01:10.80] My vision, myeot eoks, myeot jos, Bezos
                [01:13.80] Dongmyo, Wassup! Hongdae, Wassup!
                [01:16.70] I make them famous, I call that, Fashion
                [01:19.80] Fashion, Fashion, Fashion, Fashion
                [01:23.00] Fashion, Fashion, Fashion, Fashion
                [01:26.20] Nae ti, 5 bucks, bajineun manwon
                [01:29.00] Let's get it, Let's go, Fashion, Fashion
                [01:32.50] Simjangi pop out, cheonnune baro cop-cop
                [01:36.00] Guje jjambap sammanwonjjari jamba
                [01:39.20] Feel like rockstar, hwak met galaro galla let's go
                [01:42.00] Top designers, hongdae matbogo hwanjang fashion
                [01:45.80] Come and try, dongmyo saenghwareseo nan
                [01:47.90] Cheryeogeul mani dakkanwa, mosh pit haneun beop baewonwa
                [01:51.00] Baewobwa baewobwa neodo ppalli baewobwa
                [01:53.90] Ot mudeom sok dasi taeeona bintijijyeoseu came alive
                [01:57.20] Dongmyoeseo moyeo machi semina
                [02:00.30] Hongdaeeseo moyeo urin set it off
                [02:03.30] Cheongdamdong hangaundekkaji spreading out
                [02:06.50] Squad is on the way, but we can't wrap it up
                [02:09.70] Nae ti, 5 bucks, bajineun manwon
                [02:12.50] My vision, myeot eoks, myeot jos, Bezos
                [02:15.50] Dongmyo, Wassup! Hongdae, Wassup!
                [02:18.80] I make them famous, I call that, Fashion
                [02:21.90] Fashion, Fashion, Fashion, Fashion
                [02:25.00] Fashion, Fashion, Fashion, Fashion
                [02:28.00] Fashion, Fashion, Fashion, Fashion
                [02:31.20] Fashion, Fashion, Fashion, Fashion
                [02:34.10] Fashion, Fashion, Fashion, Fashion
                [02:37.50] Fashion, Fashion, Fashion, Fashion
                [02:40.30] Fashion, Fashion, Fashion, Fashion
                [02:43.80] Fashion, Fashion, Fashion, Fashion"
            ],
            [
                'title' => 'Spaghetti',
                'artist' => 'LE SSERAFIM',
                'album_art' => '/images/lesserafim.jpg',
                'artist_image' => '/images/lesserafim.jpg',
                'file_path' => 'lesserafim_spaghetti.mp3',
                'duration' => '2:50',
                'lyrics' => "[00:04.50] This is a hot spot
                [00:06.60] Sum swideut chatneun ne bapsang
                [00:08.70] Dangori doen neon fall in love
                [00:11.20] Chameul su eomneun mat
                [00:13.50] Urin in the kitchen
                [00:15.10] Cookin' it up, what you're craving?
                [00:17.50] Naui ipmatdaero saucin'
                [00:19.80] And now the world's gone mad
                [00:22.30] You don't need to think too much
                [00:25.10] Ssipgo tteutgo matbogo jeulgida gamyeon dwae (yeah, yeah, yeah)
                [00:30.10] Gipi seumyeodeureo in your mouth (ooh)
                [00:33.50] Guilty pleasure never killed nobody, deusyeobwa
                [00:39.00] Ippal sai kkin spaghetti
                [00:41.10] Ppaego sipni? Bon appétit
                [00:43.00] Geunyang pogihae, eochapi
                [00:45.30] Eat it up, eat it, eat it up (Woo)
                [00:47.60] Meorissok kkin SSERAFIM
                [00:49.50] Bad bitch in between your teeth
                [00:52.00] Geunyang pogihae, eochapi
                [00:53.90] Eat it up, eat it, eat it up
                [00:55.80] Eat it up, eat it, eat it (woah)
                [00:57.90] Eat it up, eat it (up)
                [01:00.00] Eat it up, eat it, eat it
                [01:02.50] Eat it up, eat it, eat it up
                [01:04.50] Yeah, this is the sweet spot
                [01:06.80] Heat up the scene, it's that big shot
                [01:08.90] Chef's choice, neol wihan kigiya
                [01:10.50] You lost in the sauce, no turnin' back
                [01:13.00] Deo chwihal geoya bamsae, giullyeo jeollo
                [01:14.90] Nan bin janeun batgo, ijen pinjaneun geolleo
                [01:17.20] Joeun ge joeun geoji jotaneun ge, wae mwo?
                [01:19.30] Neukkyeobwa eoseo, kkeokji malgo, beolkeok
                [01:22.20] Snap it up like a Getty
                [01:23.50] Nae gimchi pojeuneun yeojeonhi priceless
                [01:26.40] Whatever I'm cookin', stargachi
                [01:28.00] Eotteon koseudeun wonhamyeon marajuneun sweki
                [01:30.40] Naega ne chwihyang deurama juingongigo
                [01:32.20] Tto sum, deut, myeongigo, gokallori Hershey choko
                [01:34.80] Modeun jutdaereul hollineun taro, neon eummihae naro
                [01:37.50] Ja ije kkaeseo pparo, brr
                [01:39.70] Yeah, this is a hot spot
                [01:41.10] Don't give a fuck, nega mworadeon
                [01:43.60] Ssibeobosyeo masi joa
                [01:45.90] Pillyo eopseo three star
                [01:48.20] Yeah, malloman hate it
                [01:50.10] Eodi gasseo neoui diet?
                [01:52.10] Nammollae sseuk haneun eummi
                [01:54.70] You better stop lying
                [01:57.00] Don't care what you talk about
                [01:59.50] Oneuldo je ballo dallyeoon geon neojana (Yeah, yeah, yeah)
                [02:04.50] Gipi seumyeodeureo in your mouth (Ooh)
                [02:08.20] Jinjja saranginji aninji gopssibeo bwa
                [02:13.50] Ippal sai kkin spaghetti
                [02:16.00] Ppaego simni? Bon appétit
                [02:17.90] Geunyang pogihae eochapi
                [02:20.10] Eat it up, eat it, eat it up
                [02:22.10] Meorisok kkin SSERAFIM
                [02:24.70] Bad bitch in between your teeth
                [02:26.50] Geunyang pogihae eochapi
                [02:28.50] Eat it up, eat it, eat it up
                [02:30.50] Eat it up, eat it, eat it (Woah)
                [02:32.50] Eat it up, eat it up
                [02:34.80] Eat it up, eat it, eat it
                [02:37.10] Eat it up, eat it, eat it up
                [02:38.90] Eat it up, eat it, eat it (Woah)
                [02:41.10] Eat it up, eat it up
                [02:43.50] Eat it up, eat it, eat it
                [02:45.90] Eat it up, eat it, eat it up"
            ],
            [
                'title' => 'Sky',
                'artist' => 'Playboi Carti',
                'album_art' => '/images/playboicarti-sky.jpg',
                'artist_image' => '/images/artist/playboicarti.jpg',
                'file_path' => 'playboicarti_sky.mp3',
                'duration' => '3:10',
                'lyrics' => "[00:01.50] What? What? What? What?
                [00:03.22] I'm so high, man, I can't even feel shit
                [00:04.86] I told my boy, \"Go roll like ten blunts for me\" 
                [00:08.10] I told my boy, \"Go roll like ten blunts for me\" 
                [00:11.45] I'm tryna get high 'til I can't feel nothin' 
                [00:14.86] I'm tryna get high 'til I can't feel nothin' 
                [00:18.34] I could fall out the sky and I still won't feel nothin' 
                [00:21.75] I could fall out the sky and I still won't feel nothin' 
                [00:25.23] I'm way too high Whoa whoa 
                [00:28.59] I'm way too high Whoa, whoa
                [00:32.05] I'm way too high Whoa whoa
                [00:35.54] I'm way too high whoa whoa 
                [00:38.99] I'm way too high 
                [00:40.90] Wake up
                [00:42.40] It's the first of the month 
                [00:45.84] I brush my teeth and count up 
                [00:49.22] I let my bitch roll my blunt 
                [00:52.80] I'm 'bout to dirty my cup 
                [00:55.92] Pour up some lean and get stuck 
                [00:59.87] I make her scream when we fuck 
                [01:02.37] I don't drive R8s, I don't like those 
                [01:05.53] I drive the Daytona and I tinted the windows 
                [01:09.14] Can't fuck with nobody Not even my shadow 
                [01:12.39] I got on Ed Hardy She got on stilettos 
                [01:15.92] She my best friend 
                [01:17.44] Yeah, we not a couple
                [01:19.37] She a rockstar
                [01:20.72] She a sex symbol
                [01:22.79] The way she do that shit
                [01:24.26] She make it look simple
                [01:26.23] The way she do that shit
                [01:27.57] She make it look simple
                [01:30.05] Baby, tell me what you wanna do
                [01:33.50] Baby, tell me everybody you screw 
                [01:36.91] Tell me everybody you took to this room 
                [01:40.38] I gotta know who you fuck, fucked in this room 
                [01:43.82] I gotta know who you fucked in this room 
                [01:47.23] I gotta know who you fucked in this room 
                [01:49.90] Can't trust no bitch can't trust these niggas 
                [01:53.08] Yeah, in love with my money 
                [01:55.20] In love with my pistols 
                [01:57.05] In love with my bitch 
                [01:58.40] I think she my bitch 
                [02:00.10] I know she suck dick 
                [02:01.66] I know she not shit 
                [02:03.39] I been thinkin' 'bout it 
                [02:05.20] Finna cut off that bitch 
                [02:06.82] She don't cook, she don't clean 
                [02:08.81] But she want Ruth Chris 
                [02:10.42] I don't even like to hug 
                [02:11.86] I don't even like to kiss 
                [02:13.75] I just pat her on the ass And tell her, Good shit 
                [02:16.94] I just walked in my pad  paparazzi at the fence 
                [02:19.91] I'm 'bout to pour up some red 
                [02:22.14] And shawty gon' roll up some shit 
                [02:25.43] And shawty gon' roll up some shit 
                [02:27.47] I'm 'bout to pour up some red  And shawty gon' roll up some shit 
                [02:28.83] I told my boy, \"Go roll like ten blunts for me\" 
                [02:32.33] I told my boy, \"Go roll like ten blunts for me\"
                [02:35.61] I'm tryna get high 'til I can't feel nothin' 
                [02:39.00] I'm tryna get high 'til I can't feel nothin' 
                [02:42.46] I could fall out the sky and I still won't feel nothin' 
                [02:45.80] I could fall out the sky and I still won't feel nothin' 
                [02:49.34] I'm way too high Whoa  whoa 
                [02:52.68] I'm way too high whoa, whoa, whoa 
                [02:56.26] I'm way too high whoa, whoa, whoa 
                [02:59.69] I'm way too high Whoa, whoa, whoa 
                [03:03.13] I'm way too high"

            ],
            [
                'title' => 'Supernova',
                'artist' => 'Aespa',
                'album_art' => '/images/aespa_supernova.jpg',
                'artist_image' => '/images/artist/aespa.jpg',
                'file_path' => 'aespa_supernova.mp3',
                'duration' => '2:58',
                'lyrics' => "[00:02.60] I'm like some kind of supernova
                [00:06.45] Watch out
                [00:07.43] Look at me go
                [00:09.49] Jaemi jom bol
                [00:11.49] Bichui core
                [00:12.75] So hot, hot
                [00:15.45] Muni yeollyeo
                [00:17.43] Seoroui jonjaereul neukkyeo
                [00:19.66] Machi Discord
                [00:20.72] Nal daleun neo neo nuguya (Drop)
                [00:24.16] Sageoneun dagawa ah, oh, ayy
                [00:26.21] Geosege keojyeoga ah, oh, ayy
                [00:28.23] That tick, that tick, tick bomb
                [00:30.35] That tick, that tick, tick bomb
                [00:32.41] Gamhi geondeuriji mothal geol
                [00:35.07] Nugudo mariya
                [00:36.81] Jigeum nae aneseon
                [00:38.15] Su-su-su-supernova
                [00:39.92] Nova
                [00:41.79] Can't stop hyperstellar
                [00:45.68] Woncho geugeol chaja
                [00:48.41] Bring the light of a dying star
                [00:50.21] Bulleonaen nae ujureul bwa bwa
                [00:53.78] Supernova
                [00:57.82] Ah, body bang (Bang, bang, bang, bang, bang, bang)
                [01:02.37] Make it feel too right
                [01:04.40] Hwipsseullin eneoji it's so special
                [01:08.15] Janinhan queen imyeo scene ija jonggyeol
                [01:11.92] Itorok geodaehan nae anui explosion
                [01:16.24] Nae modeun sepo byeollobuteo mandeureojyeo (Under my control, ah)
                [01:20.27] Jilmuneun gyesokdwae ah, oh, ayy
                [01:22.18] Urin eodiseo wanna oh, ayy
                [01:24.32] Neukkyeo nae aneseon (Huh)
                [01:26.10] Su-su-su-supernova
                [01:27.86] Nova
                [01:29.75] Can't stop hyperstellar
                [01:33.77] Woncho geugeol chaja
                [01:36.40] Bring the light of a dying star
                [01:38.30] Bulleonaen nae ujureul bwa bwa
                [01:41.70] Supernova
                [01:44.27] Boiji anneun himeuro
                [01:48.15] Nege son naemireo bolkka
                [01:51.96] Ganeunghan modeun ganeungseong
                [01:54.32] Muhan sogui neoreul manna
                [01:55.91] It's about to bang-bang
                [01:57.90] Don't forget my name
                [02:00.12] Su-su-su-supernova
                [02:02.19] Sageoneun dagawa ah, oh, ayy
                [02:04.17] Geosege keojyeoga ah, oh, ayy
                [02:06.17] Jilmuneun gyesokdwae ah, oh, ayy
                [02:08.20] Urin eodiseo wanna oh, ayy
                [02:10.20] Sageoneun dagawa ah, oh, ayy
                [02:12.32] Geosege keojyeoga ah, oh, ayy
                [02:14.21] Tell me, tell me, tell me, oh, ayy
                [02:16.17] Urin eodiseo wanna oh, ayy
                [02:18.18] Urin eodiseo wanna oh, ayy
                [02:21.81] Nova (Nova)
                [02:23.83] Can't stop hyperstellar (Hyperstellar)
                [02:27.96] Woncho geugeol chaja
                [02:30.50] Bring the light of a dying star (Light of a dying star)
                [02:32.19] Bulleonaen nae ujureul bwa bwa (All the way)
                [02:35.89] Supernova (Hey-huh)
                [02:38.35] Sageoneun dagawa ah, oh, ayy (New star)
                [02:40.28] Geosege keojyeoga ah, oh, ayy
                [02:42.26] Jilmuneun gyesokdwae ah, oh, ayy (Nova)
                [02:44.39] Urin eodiseo wanna oh, ayy
                [02:46.40] Sageoneun dagawa ah, oh, ayy (Yeah-yeah-yeah-yeah)
                [02:48.41] Geosege keojyeoga ah, oh, ayy (Nova)
                [02:50.37] Jilmuneun gyesokdwae ah, oh, ayy (Bring the light of a dying star)
                [02:52.54] Supernova"
                ],
            [
                'title' => 'Dalam Hitungan',
                'artist' => '.Feast',
                'album_art' => '/images/feast_m&m.jpg',
                'artist_image' => '/images/artist/feast.jpg',
                'file_path' => 'feast_dalamhitungan.mp3',
                'duration' => '4:04',
                'lyrics' => "[00:37.14] Aku tak berguna jika tak diukur angka
                [00:41.62] Bertobat di media Tuhan pasti salah sangka
                [00:46.07] Mimpi butuh dana engagement rate mu berapa
                [00:50.64] Terka jarak berita tragedi milik siapa
                [00:55.20] Memahat citra sesuai standar Nya
                [00:59.73] Surga buka cabang kita semua pialang
                [01:04.21] Seratus ribu per sepuluh giga
                [01:08.74] Akhirat yang adil semua orang berwenang
                [01:13.24] Sekarang semua
                [01:15.92] Tidur tenang
                [01:17.89] Cek lagi besok pagi
                [01:19.79] Keselamatan
                [01:21.26] Kita membangun bersama
                [01:24.88] Tuhan yang baru
                [01:27.12] Dunia yang berani
                [01:28.85] Semua orang berseru
                [01:31.64] Taman Eden dengan wifi dan kamera depan
                [01:36.12] Malaikat yang hadir mengukur dalam hitungan
                [01:49.69] Kehidupan dibenahi arahan redaksi
                [01:54.26] Kematian disiarkan Instagram tv
                [01:58.73] Nyanyian pujian yang termatrikulasi
                [02:03.31] Sangkakala seindah bunyi notifikasi
                [02:06.76] Kita membangun bersama
                [02:10.19] Tuhan yang baru
                [02:12.42] Dunia yang berani
                [02:14.17] Semua orang berseru
                [02:16.96] Taman Eden dengan wifi dan kamera depan
                [02:21.44] Malaikat yang hadir mengukur dalam hitungan
                [02:25.97] Membangun bersama
                [02:28.30] Tuhan yang baru
                [02:30.45] Dunia yang berani
                [02:32.26] Semua orang berseru
                [02:35.01] Taman Eden dengan wifi dan kamera depan
                [02:39.44] Malaikat yang hadir mengukur dalam hitungan
                [02:43.24] Semoga rohku selalu kekal dalam hitungan
                [02:47.75] Tidak diretas oleh umat luar jaringan
                [02:52.25] Semoga rohku selalu kekal dalam hitungan
                [02:56.85] Tidak diretas oleh umat luar jaringan
                [03:01.50] Semoga rohku selalu kekal dalam hitungan
                [03:05.96] Tidak diretas oleh umat luar jaringan
                [03:10.48] Semoga rohku selalu kekal dalam hitungan
                [03:14.92] Tidak diretas oleh umat luar jaringan
                [03:42.12] Semoga rohku selalu kekal dalam hitungan
                [03:46.75] Tidak diretas oleh umat luar jaringan
                [03:51.93] Semoga rohku selalu kekal dalam hitungan
                [03:56.65] Tidak diretas oleh umat luar jaringan"
            ],
            [
                'title' => 'Bintang Masa Aksi',
                'artist' => '.Feast',
                'album_art' => '/images/feast_abdilarainsani.jpg',
                'artist_image' => '/images/artist/feast.jpg',
                'file_path' => 'feast_bintangmasaaksi.mp3',
                'duration' => '3:50',
                'lyrics' => "[00:45.30] Bawa temanmu yang centang biru
                [00:49.24] Lawyer sponsor partaimu mana aku tak takut
                [00:55.07] Kuberikan tantangan untukmu
                [00:59.08] Coba buatku kalut ayo sini pengecut
                [01:03.88] Skarang mana paham usangmu dasar maskulin rapuh
                [01:09.68] Lemah permisif angkuh juara titik jenuh
                [01:15.56] Kalengan hanya melek jualan
                [01:19.36] Otak encer oplosan jalan ke pengasingan
                [01:23.83] Sekarang api bintang jatuh aku yang terpilih
                [01:29.28] Aku kan taklukkan jalanan
                [01:34.20] Aku kan taklukkan angkasa
                [01:38.41] Aku yang dipercaya untuk menggembala
                [01:43.86] Aku adalah median semua massa
                [01:51.33] Sini mana teman-temanmu hilang satu per satu
                [01:56.81] Perlahan jadi batu mulai enggan membantu
                [02:02.14] Putuskan menyerahlah sekarang
                [02:05.68] Tutup akun sekarang menyerahlah dan pulang
                [02:10.57] Bela dan perjuangkan berbagai hal yang usang
                [02:15.69] Keropos dan berkarang mundurlah dan menghilang
                [02:40.37] Aku kan taklukkan jalanan
                [02:45.27] Aku kan taklukkan angkasa
                [02:49.54] Aku yang dipercaya untuk menggembala
                [02:54.97] Aku adalah median semua massa
                [02:59.94] Aku kan taklukkan jalanan
                [03:04.86] Aku kan taklukkan pikiran
                [03:09.04] Aku yang dipercaya untuk menggembala
                [03:14.37] Aku adalah median semua massa"
            ],
            [
                'title' => 'New Jeans',
                'artist' => 'NewJeans',
                'album_art' => '/images/newjeans_getup.jpg',
                'artist_image' => '/images/artist/newjeans.jpg',
                'file_path' => '/music/newjeans_newjeans.mp3',
                'duration' => '1:49',
                'lyrics' => "[00:14.22] Look it's a new me
                [00:16.11] Switched it up, who's this?
                [00:17.87] Uril bwa NewJeans
                [00:19.60] So fresh, so clean
                [00:22.10] Eolmana
                [00:23.78] Gidaryeotdeon nal
                [00:25.66] Deudieo
                [00:27.41] Time to step out
                [00:29.06] Tto han beon deo
                [00:30.76] Ready for sure
                [00:32.56] To have some more
                [00:35.84] New hair
                [00:36.77] New tee
                [00:37.67] NewJeans
                [00:38.24] Do you see
                [00:39.59] New hair
                [00:40.48] New tee
                [00:41.34] NewJeans
                [00:42.05] Do you see
                [00:43.07] New hair
                [00:43.94] New tee
                [00:44.83] NewJeans
                [00:45.59] Do you see
                [00:46.59] New hair
                [00:47.52] New tee
                [00:48.40] NewJeans
                [00:49.09] Do you see
                [00:49.90] Make it feel
                [00:50.79] Like a game
                [00:51.71] Look at us, we go
                [00:52.65] On & on again
                [00:53.47] We'll go on to the end
                [00:55.22] What we wanna do
                [00:56.11] On & on again
                [00:57.03] Make it feel
                [00:57.90] Like a game
                [00:58.88] Look at us, we go
                [00:59.74] On & on again
                [01:00.59] We'll go on to the end
                [01:02.39] What we wanna do
                [01:03.28] On & on again
                [01:04.58] Look it's a new me
                [01:06.21] Switched it up, who's this?
                [01:08.01] Deureobwa NewJeans
                [01:09.75] So fresh, so clean
                [01:12.23] Eolmana
                [01:13.91] Gidaryeotdeon nal
                [01:15.81] Deudieo
                [01:17.62] Feeling's so right
                [01:19.30] Tto han beon deo
                [01:21.02] I need to know
                [01:22.90] You want some more
                [01:25.92] New hair
                [01:26.88] New tee
                [01:27.76] NewJeans
                [01:28.49] You & me
                [01:29.60] New hair
                [01:30.47] New tee
                [01:31.30] NewJeans
                [01:32.00] You & me
                [01:33.19] New hair
                [01:34.08] New tee
                [01:34.93] NewJeans
                [01:35.49] You & me
                [01:36.70] New hair
                [01:37.58] New tee
                [01:38.40] NewJeans
                [01:39.12] You & me"
            ],
            [
                'title' => 'Rich Man',
                'artist' => 'Aespa',
                'album_art' => '/images/aespa_richman.jpg',
                'artist_image' => '/images/artist/aespa.jpg',
                'file_path' => 'aespa_richman.mp3',
                'duration' => '3:17',
                'lyrics' => "[00:00.00] My mom said to me, \"Find someone who can give you everything.\"
                [00:03.03] And I said, \"Mom, I already have everything.\"
                [00:05.63] I am a rich man
                [00:06.73] I am a rich man
                [00:07.81] I am a rich man (I'ma carry myself)
                [00:10.04] I am a rich man (I'ma carry myself)
                [00:12.16] I am a rich man
                [00:13.61] I'm my own biggest fan, and I'm high in demand
                [00:16.53] I am a rich man
                [00:18.00] Don't care about what they say
                [00:19.63] Nal mireo neoko meotdaero gul ttae
                [00:21.76] Nae geoseul tamnae see? My name is
                [00:24.02] Where my name is? (I am a rich man)
                [00:26.95] That's me, naneun reckless (Yeah)
                [00:28.41] Gudeun mental geugeotjjeum unne (What?)
                [00:30.50] Naega nal ikkeureo ga, boyeo daeum sign
                [00:32.87] Byeolgeo anya, exit is my next step
                [00:35.18] Ttan saenggak malgo, self-belief
                [00:37.33] 'Cause geuge hwolssin jaemiitji, ooh-ah-oh
                [00:42.63] I am a rich man
                [00:43.78] So I am standin', where you lookin'?
                [00:46.18] Matchun deuthan my perfect fit, baby
                [00:51.43] I am a rich man (I'ma carry myself)
                [00:53.62] I am a rich man (I'ma carry myself)
                [00:55.83] I am a rich man
                [00:57.29] I'm that one, nan naro gadeukae by myself
                [01:00.15] I am a rich man (I'ma carry myself)
                [01:02.36] I am a rich man (I'ma carry myself)
                [01:04.53] I am a rich man 
                [01:06.05] I am my own biggest fan and I'm high in demand 
                [01:08.94] I am a rich man
                [01:10.11] La-la, la-la, la, ah (I am what I am)
                [01:14.43] La-la, la-la, la, ah (I am a rich man)
                [01:18.80] La-la, la-la, la, ah (I am what I am)
                [01:23.08] La-la, la-la, la, ah (I am a rich man)
                [01:27.87] Don't care about what they say
                [01:29.50] Nal mireonaego hamburo gul ttae
                [01:31.70] Ohiryeo, okay, see? My name is
                [01:33.73] What my name is (I am a rich man)
                [01:36.52] Don't need the money, yeah, I see it
                [01:38.69] In my closet, my ideas
                [01:40.94] Nae malbeoreut, nae georeum, nae ireum
                [01:43.24] You know when I'm serving them looks, I'ma feed 'em
                [01:44.93] Ttan saenggak malgo, self-belief
                [01:47.19] 'Cause geuge jom deo jaemitjana, ooh-ah-oh
                [01:52.40] I am a rich man
                [01:53.62] So I am standin', where you lookin'?
                [01:55.60] Matchun deuthan my perfect fit, baby
                [02:00.96] I am a rich man (I'ma carry myself)
                [02:03.33] I am a rich man (I'ma carry myself)
                [02:05.60] I am a rich man
                [02:07.21] I'm that one, nan naro gadeukae by myself
                [02:09.99] I am a rich man (I'ma carry myself)
                [02:12.22] I am a rich man (I'ma carry myself)
                [02:14.34] I am a rich man
                [02:15.97] I am my own biggest fan and I'm high in demand (Say, ooh)
                [02:19.16] I am a rich man
                [02:20.77] Twenty four, moduga same shade
                [02:22.68] You already know what the tag say
                [02:25.00] Make it better on my own, my tag
                [02:27.20] I won't double back, hyungnae an nae
                [02:29.25] If you blame it, cameo
                [02:30.83] I carry the load, run the show
                [02:32.87] I'm like a diamond ring, already got my thing
                [02:34.86] Cannot put a price on it, this is the real deal, yeah
                [02:38.68] I am a rich man (I'ma carry myself)
                [02:40.58] I am a rich man (I'ma carry myself)
                [02:42.68] I am a rich man
                [02:44.23] I'm that one, nan naro gadeukae by myself (Say what?)
                [02:47.12] I am a rich man (I'ma carry myself)
                [02:49.33] I am a rich man (I'ma carry myself)
                [02:51.43] I am a rich man
                [02:53.14] I am my own biggest fan and I'm high in demand (Woo)
                [02:55.81] I am a rich man
                [02:56.99] La-la, la-la, la, ah (I am what I am)
                [03:01.32] La-la, la-la, la, ah (So good, so good, so good)
                [03:04.81] I am a rich man
                [03:05.73] La-la, la-la, la, ah (I am what I am)
                [03:10.09] La-la, la-la, la, ah (I am a rich man)"
            ],
            [
                'title' => 'Peradaban',
                'artist' => '.Feast',
                'album_art' => '/images/feast_peradaban.jpg',
                'artist_image' => '/images/artist/feast.jpg',
                'file_path' => 'feast_peradaban.mp3',
                'duration' => '5:43',
                'lyrics' => "[00:24.61] Bawa pesan ini ke persekutuanmu
                [00:29.76] Tempat ibadah terbakar lagi
                [00:34.59] Bawa pesan ini lari ke k'luargamu
                [00:39.64] Nama kita diinjak lagi
                [00:44.47] Bagai keset \"Selamat datang\"
                [00:49.56] Masuk kencang tanpa diundang
                [00:54.58] Ambil minum, lepas dahaga
                [00:59.53] Rampas galon, dispenser pula
                [01:04.41] Yang jadi saksi harus kuat
                [01:09.44] Tak terbutakan dunia, akhirat
                [01:14.29] Yang patah tumbuh, yang hilang berganti
                [01:19.32] Gapura hancur dibangun lagi
                [01:24.07] Kar'na peradaban takkan pernah mati
                [01:28.99] Walau diledakkan, diancam 'tuk diobati
                [01:33.88] Kar'na peradaban berputar abadi
                [01:38.86] Kebal luka bakar, tusuk, atau caci-maki
                [01:43.73] Kar'na peradaban takkan pernah mati
                [01:48.68] Walau diledakkan, diancam 'tuk diobati
                [01:53.50] Kar'na peradaban berputar abadi
                [01:58.58] Kebal luka bakar, tusuk, atau caci-maki
                [02:03.54] Beberapa orang menghakimi lagi
                [02:08.47] Walaupun diludahi zaman seribu kali
                [02:13.35] Beberapa orang memaafkan lagi
                [02:18.38] Walau sudah ditindas habis berkali-kali
                [02:23.30] Kar'na peradaban takkan pernah mati
                [02:28.27] Walau diledakkan, diancam 'tuk diobati
                [02:33.30] Kar'na peradaban berputar abadi
                [02:38.28] Kebal luka bakar, tusuk, atau caci-maki
                [02:43.18] Kar'na peradaban takkan pernah mati
                [02:48.16] Walau diledakkan, diancam 'tuk diobati
                [02:52.96] Kar'na peradaban berputar abadi
                [02:57.93] Kebal luka bakar, tusuk, atau caci-maki
                [03:02.83] Kar'na kehidupan tidak ternodai
                [03:07.83] Maknanya jika kau tak sepaham dengan kami
                [03:12.79] Kar'na kematian tanggungan pribadi
                [03:17.83] Bukan milik siapa pun untuk disudahi
                [03:22.61] Budaya, bahasa berputar abadi
                [03:27.63] Jangan coba atur tutur kata kami
                [03:32.56] Hidup tak sependek penis laki-laki
                [03:37.53] Jangan coba atur gaya berpakaian kami
                [03:41.83] Suatu saat nanti tanah air kembali berdiri
                [03:46.72] Suatu saat nanti kita memimpin diri sendiri
                [03:51.86] Suatu saat nanti kita meninggalkan sidik jari
                [03:56.61] Suatu saat nanti semoga semua berbesar hati
                [04:01.58] Suatu saat nanti tanah air kembali berdiri
                [04:06.56] Suatu saat nanti kita memimpin diri sendiri
                [04:11.51] Suatu saat nanti kita meninggalkan sidik jari
                [04:16.43] Suatu saat nanti semoga semua berbesar hati
                [04:21.48] Suatu saat nanti tanah air kembali berdiri
                [04:26.31] Suatu saat nanti kita memimpin diri sendiri
                [04:31.33] Suatu saat nanti kita meninggalkan sidik jari
                [04:36.38] Suatu saat nanti semoga semua berbesar hati
                [04:41.22] Suatu saat nanti tanah air kembali berdiri
                [04:46.20] Suatu saat nanti kita memimpin diri sendiri
                [04:51.07] Suatu saat nanti kita meninggalkan sidik jari
                [04:56.07] Suatu saat nanti semoga semua berbesar hati
                [05:00.97] Suatu saat nanti tanah air kembali berdiri
                [05:05.98] Suatu saat nanti kita memimpin diri sendiri
                [05:10.81] Suatu saat nanti kita meninggalkan sidik jari
                [05:15.78] Suatu saat nanti semoga semua berbesar hati
                [05:20.82] Suatu saat nanti tanah air kembali berdiri
                [05:25.64] Suatu saat nanti kita memimpin diri sendiri
                [05:30.59] Suatu saat nanti kita meninggalkan sidik jari
                [05:35.65] Suatu saat nanti semoga semua berbesar hati"
            ],
            [
                'title' => 'Arteri',
                'artist' => '.Feast',
                'album_art' => '/images/feast_m&m.jpg',
                'artist_image' => '/images/artist/feast.jpg',
                'file_path' => 'feast_arteri.mp3',
                'duration' => '4:35',
                'lyrics' => "[00:41.78] Telanjang, ku telanjang
                [00:43.97] Menyicipi dunia
                [00:46.23] Hatiku berkata
                [00:49.58] “Selamat datang di dua puluh!”
                [00:52.92] Kau tambal kegagalanku
                [00:57.28] Kau masuk ke dalam darah
                [00:58.78] Berdansa dan berserah
                [01:00.12] Untuk sekian jam saja
                [01:03.02] Sembunyikanmu dari dunia
                [01:05.73] Hilang akal saat kau ada
                [01:08.30] Berputar, mana ujungnya?
                [01:13.21] Menangisku di pundakmu
                [01:19.99] Kau bilang muntahkan semua pilu
                [01:23.70] Aku pura pura tak tahu
                [01:26.26] Aku pura pura tak sadar
                [01:29.15] Kau hanya trauma
                [01:31.89] (Meluncur di Arteri)
                [01:34.85] Aku ingin tak menghiraukan masa depan
                [01:39.08] Namun hidup apa hanya delapan kali sebulan?
                [01:45.42] Salahku memikirkan
                [01:48.03] Untuk menyelamatkan
                [01:50.70] Saat kau lah titik perkara
                [01:55.49] Menangisku di pundakmu
                [02:02.16] Kau bilang muntahkan semua pilu
                [02:05.84] Aku pura pura tak tahu
                [02:08.51] Aku pura pura tak sadar
                [02:11.27] Kau hanya trauma
                [02:14.03] (di Arteri Pondok Indah)
                [02:16.72] Aku berlari lari lari lari mengejar dirimu
                [02:27.19] Cinta macam apa yang dijaga ketat oleh perantara?
                [02:37.74] Indraku mati rasa, kubuang jauh dalam tempat sampah
                [03:11.20] Kau hanya trauma
                [03:15.95] Meluncur di Arteri
                [03:21.58] Hanya lagu lama
                [03:26.64] Bernyanyi di Arteri
                [03:30.37] Menangisku di pundakmu
                [03:36.99] Kau bilang muntahkan semua pilu
                [03:40.81] Aku pura pura tak tahu
                [03:43.60] Aku pura pura tak sadar
                [03:46.30] Kau hanya trauma
                [03:48.89] (Meluncur di arteri)
                [03:51.66] Setetes bahagia
                [03:54.18] Yang selalu kau cari
                [03:56.78] Mengalir berkelana
                [03:59.50] Meluncur di arteri
                [04:02.07] Setetes bahagia
                [04:04.69] Yang selalu kau cari
                [04:07.33] Mengalir berkelana
                [04:10.02] Meluncur di arteri
                [04:12.87] Setetes bahagia
                [04:15.39] Yang selalu kau cari
                [04:17.73] Mengalir berkelana
                [04:20.72] Meluncur di arteri"
            ],
            [
                'title' => 'Armageddon',
                'artist' => 'Aespa',
                'album_art' => '/images/aespa_armageddon.jpg',
                'artist_image' => '/images/artist/aespa.jpg',
                'file_path' => 'aespa_armageddon.mp3',
                'duration' => '3:16',
                'lyrics' => "[00:02.19] Armageddon
                [00:04.13] Shoot
                [00:07.18] I'ma get 'em
                [00:09.76] Shoot
                [00:10.47] Watch, uh
                [00:11.65] I'ma bite back, uh
                [00:13.08] Jiteun eodumi magaseol ttaen, uh
                [00:15.65] Han georeum apeuro naradeun it's bad
                [00:17.56] Sarajin feedback
                [00:18.94] Sijakdoen code black, huh
                [00:20.75] Gipeoga
                [00:21.75] Hollanseureoun bam
                [00:23.11] Angmongeun tto jitge beonjyeoga
                [00:25.10] Mwongal sumgiryeogo hae
                [00:26.98] I got it, I got it
                [00:28.72] Hondoneul tago deopchyeo killing like
                [00:31.30] Bang, chitty, bang, bang, chitty, bang, bang
                [00:33.71] 'Cause I wanna see, I wanna see truly
                [00:36.50] Bang, chitty, bang, bang, chitty, bang, bang
                [00:38.80] Naege dagawa dagawa
                [00:40.18] I'ma get it done (Aw, wayo, wayo)
                [00:44.61] Neol hyanghae gyeonwo get it, gone (Aw, wayo, wayo)
                [00:49.88] Ijen neol kkeunnae better run
                [00:52.19] Kkeuteul moreuneun neowa na you gonna, gonna
                [00:55.40] Kkaeteuryeo geochimeopsi done (Go way up, way up)
                [01:00.32] Full shot, pull it up, Armageddon
                [01:04.54] I'ma get 'em
                [01:07.44] Shoot
                [01:09.77] I'ma get 'em
                [01:13.03] Hey, ya (Yeah)
                [01:15.04] Tto dareun na (Ah-ah, ah-ah)
                [01:17.56] Uril makji ma (Oh)
                [01:20.04] We never play nice
                [01:22.79] Shoot
                [01:23.85] Wanbyeokan pair
                [01:24.86] Neon ttokgateun soul
                [01:26.12] Three to get ready
                [01:27.34] Urin shoot and go
                [01:28.62] Geop eopsi nubyeo
                [01:30.47] Nal ikkeuneun way
                [01:33.89] Bang, chitty, bang, bang, chitty, bang, bang
                [01:36.36] Yes, I'm gonna see
                [01:37.52] I'm gonna see, want it
                [01:39.12] Bang, chitty, bang, bang, chitty, bang, bang
                [01:41.45] Dabi deullyeowa deullyeowa
                [01:42.77] I'ma get it done (Aw, wayo, wayo)
                [01:47.21] Neol hyanghae gyeonwo get it, gone (Aw, wayo, wayo)
                [01:52.51] Ijen neol kkeunnae better run
                [01:54.75] Kkeuteul moreuneun neowa na you gonna, gonna
                [01:57.91] Kkaeteuryeo geochimeopsi done (Go way up, way up)
                [02:02.83] Full shot, pull it up, Armageddon
                [02:07.16] (I'ma get 'em)
                [02:08.24] Tto eodumeul moranaego
                [02:11.56] Sijageul kkotpiun neowa naui story
                [02:15.24] Deo wanbyeokaejin uri (Armageddon)
                [02:18.02] Jeonguihae ijen
                [02:20.08] Namanui complete
                [02:23.13] Nae modeun geol ikkeureo
                [02:25.14] Do it all myself
                [02:26.57] Wanjeonhan nareul irwonae
                [02:30.94] Drop
                [02:32.12] Throw it back, throw it back, throw it back
                [02:34.32] Born like a queen
                [02:35.24] Born like a king, ya
                [02:37.11] Throw it back, throw it back, throw it back
                [02:39.39] Bulleo
                [02:40.16] I'ma get 'em done (Aw, wayo, wayo)
                [02:44.71] Neol hyanghae gyeonwo get it, gone (Aw, wayo, wayo)
                [02:50.03] Ijen neol kkeunnae better run
                [02:52.18] Kkeuteul moreuneun neowa na you gonna, gonna
                [02:55.18] Kkaeteuryeo geochimeopsi done (Go way up, way up)
                [03:00.31] Full shot, pull it up, Armageddon
                [03:03.24] Armageddon
                [03:04.16] (Aw, wayo, wayo, wayo, warning all night long)
                [03:07.64] Armageddon
                [03:08.90] (Aw, wayo, wayo)
                [03:10.75] Kkeutgwa sijagui Armageddon"
            ],
            [
                'title' => 'Dirty Work',
                'artist' => 'Aespa',
                'album_art' => '/images/aespa_dirtywork.jpg',
                'artist_image' => '/images/artist/aespa.jpg',
                'file_path' => 'aespa_dirtywork.mp3',
                'duration' => '3:00',
                'lyrics' => "[00:09.37] World domination
                [00:10.66] I don't gotta say it
                [00:11.83] Jeonen eopdeon
                [00:13.04] Dollyeonbyeoni gata
                [00:14.21] Jeojuya nan
                [00:15.51] Dasuro bol ttaen
                [00:16.58] Set 'em on fire
                [00:18.00] Seuseuro balkyeo
                [00:19.11] And I don't really care if you
                [00:20.32] Like me, like me
                [00:21.57] I don't really wanna play
                [00:22.76] Nicely, nicely
                [00:23.98] Odabeul goreun ge
                [00:25.11] Jeongdabin seontaek
                [00:26.33] Open your eyes
                [00:27.40] Come and
                [00:28.01] Bite me
                [00:28.90] Sharp teeth
                [00:30.09] Bite first
                [00:31.40] Real bad business
                [00:32.37] That's Dirty Work
                [00:33.67] Real bad business
                [00:34.77] That's Dirty Work
                [00:36.02] Real bad business
                [00:37.22] That's Dirty Work
                [00:38.54] Bold eyes
                [00:39.81] Cold stare
                [00:41.03] Real bad business
                [00:42.04] That's Dirty Work
                [00:43.35] Real bad business
                [00:44.57] That's Dirty Work
                [00:45.70] Real bad business
                [00:46.98] That's Dirty Work
                [00:48.24] Work, work, work, work
                [00:51.54] Work, work, work
                [00:53.24] Real bad business
                [00:54.36] That's Dirty Work
                [00:55.65] Real bad business
                [00:56.96] That's Dirty Work
                [00:58.49] I'm not an it girl
                [00:59.57] More like a hit girl
                [01:00.81] Mafia ties going
                [01:02.11] Back to the old world
                [01:03.28] Fear in their eyes
                [01:04.59] I'm always watching
                [01:05.52] Call me the reaper
                [01:06.87] I'm knock, knock, knocking
                [01:08.23] It's me, it's me
                [01:09.48] A little baddie
                [01:10.75] Just 'cause I'm pretty doesn't mean
                [01:12.13] I don't do hard things
                [01:13.38] Hard things
                [01:14.14] Make me feel like a thunder
                [01:15.33] Let me think mworeul hadeun
                [01:16.42] I will like it, like it
                [01:17.89] Sharp eyes
                [01:19.06] Fierce look
                [01:20.27] Real bad business
                [01:21.38] That's Dirty Work
                [01:22.69] Real bad business
                [01:23.77] That's Dirty Work
                [01:25.05] Real bad business
                [01:26.10] That's Dirty Work
                [01:27.57] Hold tight
                [01:28.80] Get tough
                [01:30.04] Real bad business
                [01:31.09] That's Dirty Work
                [01:32.47] Real bad business
                [01:33.55] That's Dirty Work
                [01:34.91] Real bad business
                [01:36.08] That's Dirty Work
                [01:37.62] We don't see you as a threat
                [01:39.62] Yalpakan Rule ttawin
                [01:40.83] Han gyeobui Glass
                [01:41.77] Nae mamdaero hae
                [01:42.91] Kkaeteuryeo nae
                [01:44.17] Swipge nan du ballo
                [01:45.56] Geu wireul Pass, yeah
                [01:47.36] Kick up the dust
                [01:48.59] Let 'em talk about it
                [01:49.68] Crawl out the mud
                [01:50.88] Let 'em know about it
                [01:52.06] Beonnoereul kkaeuneun eumnyul soge
                [01:54.30] Du nuneul bwa
                [01:55.47] Well you already found it
                [01:57.34] Drop it low, low, low, low, low, low
                [01:59.49] Work it out, work it out
                [02:02.17] Drop it low, low, low, low, low, low
                [02:04.38] Work it out, work it out
                [02:07.23] Drop it low, low, low, low, low, low
                [02:09.28] Work it out, work it out
                [02:11.81] Drop it low, low, low, low, low, low
                [02:14.07] Work it out, work it out (Work It)
                [02:16.60] Sharp teeth
                [02:17.80] Bite first
                [02:19.01] Real bad business
                [02:20.10] That's Dirty Work
                [02:21.42] Real bad business
                [02:22.52] That's Dirty Work
                [02:23.92] Real bad business
                [02:25.03] That's Dirty Work
                [02:26.33] Bold eyes
                [02:27.51] Cold stare
                [02:28.74] Real bad business
                [02:29.87] That's Dirty Work
                [02:31.30] Real bad business
                [02:32.33] That's Dirty Work
                [02:33.70] Real bad business
                [02:34.88] That's Dirty Work
                [02:36.16] Work, work, work, work
                [02:39.21] Work, work, work
                [02:41.05] Real bad business
                [02:42.36] That's Dirty Work
                [02:43.49] Real bad business
                [02:44.65] That's Dirty Work
                [02:45.95] Work, work, work, work
                [02:49.02] Work, work, work
                [02:50.82] Real bad business
                [02:51.98] That's Dirty Work
                [02:53.26] Real bad business
                [02:54.59] That's Dirty Work"
            ],[
                'title' => '20 Min',
                'artist' => 'Lil Uzi Vert',
                'album_art' => '/images/liluzi_lur2.jpg',
                'artist_image' => '/images/artist/liluzi.jpg',
                'file_path' => 'liluzi_20min.mp3',
                'duration' => '3:40',
                'lyrics' => "[00:00.00] I said, Girl, why you keep callin?
                [00:01.68] I said, Girl, why you keep callin?, yeah
                [00:04.02] She said, I need a new whip, yeah
                [00:05.96] Cause I know that you still ballin
                [00:07.89] She just wanna go back to the future, so I bought that girl a DeLorean
                [00:11.85] Twenty one minutes until I got go, so I told that girl Im gonna slaughter it
                [00:30.87] I met that girl right up at my show
                [00:32.83] Left her man in the crowd on the floor (Floor)
                [00:34.74] Out of town never saw her before
                [00:36.64] Told her, Baby, we dont got that long
                [00:38.53] Listen, this not my city show, but, I treat it like my city show
                [00:42.53] Twenty more minutes until Im on, twenty more minutes until Im on (Yeah)
                [00:46.39] Twenty more minutes until Im on, twenty more minutes until Im on
                [00:50.27] Twenty more minutes until Im on, twenty more minutes until Im on (Yeah)
                [00:54.65] I am not slow, these girls just want me cause I got the dough
                [00:57.54] Pass that girl right on my friend, give and go
                [00:59.56] He pass me her friend, so they switchin roles (Yeah)
                [01:02.41] I hit it fast (F-f-f-fast), yeah, I hit it slow (Slow)
                [01:05.43] But by the mornin, girl, I gotta go (Yeah)
                [01:07.56] Gotta get ready, tonight is my show
                [01:09.28] If you okay, you might open my show
                [01:11.19] Got the big pointers right under my nose
                [01:13.07] These niggas mad cause I got all the dough
                [01:15.00] These niggas mad cause I got all the dough (Yeah)
                [01:17.01] Changin my style cause I got every flow
                [01:19.01] Got every girl, aint no toppin my hoes (Yeah)
                [01:20.70] I understand that is your girlfriend, bro
                [01:22.87] But you know I gotta keep her close
                [01:24.87] Open your legs and I keep em closed
                [01:26.69] Livin life on the edge, on a tightrope
                [01:28.66] I am so clean, I might start movin soap (Yeah)
                [01:30.61] Drive a new Rari, I dont need a note (Skrrt)
                [01:32.59] Drive a Bugatti like its a Volvo
                [01:34.53] I got your girl and you already know (Skrrt)
                [01:36.42] Dont really like her, we friends for the most
                [01:38.35] Cuban link tri-color, all on my choker (Skrrt)
                [01:40.39] Dont leave the crumb, got the bread, then you toast
                [01:42.26] If you start touchin my gun, got no holst
                [01:44.23] I put a coat on top of my coat
                [01:46.18] Only twenty minutes before the show (Show)
                [01:48.61] I met that girl right up at my show
                [01:50.63] Left her man in the crowd on the floor
                [01:52.54] Out of town, never saw her before
                [01:54.38] Told her, Baby, we dont got that long
                [01:56.33] Listen, this not my city show, but, I treat it like my city show
                [02:00.35] Twenty more minutes until Im on, twenty more minutes until Im on
                [02:04.16] Twenty more minutes until Im on, twenty more minutes until Im on
                [02:07.99] Twenty more minutes until Im on, twenty more minutes until Im on (Yeah)
                [02:12.36] I am not slow, these girls just want me, cause I got the dough
                [02:15.34] Pass that girl right on my friend, give and go (What?)
                [02:17.32] He pass me her friend, so they switchin roles (Yeah)
                [02:20.54] Damn, I just started it (Started), man, I just started it
                [02:23.75] Oh my God, please, do not bother me
                [02:25.78] Dont got enough just to order me (Yeah)
                [02:27.62] Im on that tree like an ornament
                [02:29.51] Money so long like accordion (Woo)
                [02:31.52] You lied to me, wasnt sorry then
                [02:33.46] On the weekend, you was partyin
                [02:35.37] It was just me, you was targetin (Yeah)
                [02:37.28] It was just me, you was targetin (Target)
                [02:39.40] Jump in the Porsche, I might target it (Skrr)
                [02:41.18] Jump in the Lamb, I aint parkin it (Skrr)
                [02:43.15] Ice is so cold, I snowboard in it (Blaow)
                [02:45.14] Go to my show, they applaud me in
                [02:47.02] She call my phone with emergency (Ayy)
                [02:48.88] She call my phone with that urgency (Ayy)
                [02:50.92] I said, Girl, why you keep callin? (Ayy)
                [02:52.82] I said, Girl, why you keep callin?, yeah
                [02:55.13] She said, I need a new whip, yeah (Skrr)
                [02:57.16] Cause I know that you still ballin (Ball!)
                [02:59.12] She just wanna go back to the future, so, I brought that girl a DeLorean (Yah)
                [03:02.97] Twenty more minutes until I got go, so, I told that girl that Im gonna slaughter it (Yeah)
                [03:06.75] I met that girl right up at my show
                [03:08.47] Left her man in the crowd on the floor
                [03:10.35] Out of town, never saw her before
                [03:12.32] Told her, Baby, we dont got that long
                [03:14.10] Listen, this not my city show, but, I treat it like my city show
                [03:18.27] Twenty more minutes until Im on, twenty more minutes until Im on
                [03:21.92] Twenty more minutes until Im on, twenty more minutes until Im on
                [03:25.86] Twenty more minutes until Im on, twenty more minutes until Im on (Yeah)
                [03:30.27] I am not slow, these girls just want me, cause I got the dough
                [03:33.34] Pass that girl right on my friend, give and go (What?)
                [03:35.10] He pass me her friend, so they switchin roles (Yeah), yah"
            ],
            [
                'title' => 'Tanam Saja',
                'artist' => 'Nosstress',
                'album_art' => '/images/nosstress.jpg',
                'artist_image' => '/images/.jpg',
                'file_path' => 'nosstress_tanamsaja.mp3',
                'duration' => '5:05',
                'lyrics' => null
            ],
            [
                'title' => 'LosT',
                'artist' => 'Bring Me The Horizon',
                'album_art' => '/images/bmth.jpg',
                'file_path' => '/music/lost.mp3',
                'duration' => '3:25',
                'lyrics' => null
            ],
            [
                'title' => 'Perayaan Patah Hati',
                'artist' => 'For Revenge',
                'album_art' => '/images/perayaanpatahhati.jpg',
                'file_path' => '/music/perayaan.mp3',
                'duration' => '5:05',
                'lyrics' => null
            ],
            [
                'title' => 'Penyangkalan',
                'artist' => 'For Revenge',
                'album_art' => '/images/penyangkalan.jpg',
                'file_path' => '/music/penyangkalan.mp3',
                'duration' => '4:00',
                'lyrics' => null
            ],
        ];

        foreach ($songs as $song) {
            Song::create($song);
        }
    }
}