<?php

namespace App\Http\Controllers\Crawler;
use Carbon\Carbon;
use App\City;
use App\Eczaneler;
use App\Http\Controllers\Controller;

include 'simple_html_dom.php';

class CrawlerController extends Controller
{
    public function index()
    {

        //taking the link of each page
        set_time_limit(900);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1) Gecko/20061024 BonEcho/2.0");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, "https://www.emlakjet.com/satilik-konut/");
        $html = curl_exec($curl);
        curl_close($curl);
        $html = str_get_html($html);

        $ilanlar = array();
        
        $counter = 0;
        
        foreach ($html->find('a.listing-url') as $link) {
            // taking images and the details of each House
            set_time_limit(900);
            $curl1 = curl_init();
            curl_setopt($curl1, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($curl1, CURLOPT_HEADER, true);
            curl_setopt($curl1, CURLOPT_NOBODY, false);
            curl_setopt($curl1, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl1, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl1, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1) Gecko/20061024 BonEcho/2.0 Chrome/26.0.1410.43 Safari/537.31");
            curl_setopt($curl1, CURLOPT_HEADER, 0);
            curl_setopt($curl1, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl1, CURLOPT_URL, "https://www.emlakjet.com" . $link->href);
            $html1 = curl_exec($curl1);
            curl_close($curl1);
            
            $html1 = str_get_html($html1);
            if (empty($html1)) {
                continue;
            }

            //title
            foreach ($html1->find('h1.announTitle') as $h1) {
                $ilanlar['title'] = $h1->plaintext;
            }

            //price
            foreach ($html1->find('li.priceBox') as $price) {
                $ilanlar['price'] = $price->plaintext;
            }

            //telefon
            foreach ($html1->find('ul li div.horizontal-center') as $phone) {
                var_dump( $ilanlar['phone'] = $phone->attr);
            }
            //images
            foreach ($html1->find('img.mainImage') as $img) {
                if ($img->parent->attr) {
                    $ilanlar['thumbnail'] = $img->src;
                } else {
                    $ilanlar['images'] = $img->src;
                }
            }
            //description
            foreach ($html1->find('div.infoDesc') as $div) {
                $ilanlar['aciklama'] = $div->children(1)->plaintext;
            }
            //details
            $divd = $html1->find('div.element');
            for ($d = 0; $d <sizeof($divd); $d++) {
                $ilanlar['details'][$d] = $divd[$d]->children(1)->plaintext;
            }

             if ($ilanlar['details'][4] = "Satlik") {
                 $category = 1;
             }
             $date = Carbon::now()->toDateTimeString();
                      
$ilan[] = $ilanlar;
            $t = 1;
            foreach ($ilan as $value) {
                $p = 1;
                City::create(
                    [
                        'Listing_ID' => $t++,
                        'Position' => $p++,
                        'Photo' => $value['images'],
                        'Thumbnail' => $value['thumbnail'],
                        'Thumbnail_x2' => $value['thumbnail'],
                        'Original' => $value['images'],
                    ]);
            }
         }
         $ilan[] = $ilanlar;
         foreach ($ilan as $value) {
             Eczaneler::create(
                 [
                     'Category_ID' => $category,
                     'Main_photo' => $value['images'],
                     'Main_photo_x2' => $value['thumbnail'],
                     'price' => $value['price'],
                     'Country' => "countries_turkey",
                     'address' =>"-", // address
                     'additional_information'=> $value['aciklama'],
                     'title' => $value['title'],
                     'sale_rent' => 1  ,
                     'bedrooms' => $value['details'][20],
                     'bathrooms' => $value['details'][21],
                     'square_feet'=> $value['details'][17] ,
                     'Loc_latitude' => "0",
                     'Loc_longitude' => "0",
                     'Featured_date' => $date,
                     'Pay_date' => $date,
                     'Date' => $date,
                     'time_frame_date_multi' => $date,
                     'Last_show' =>  $date ,
                     'Loc_address' => "0",
                     'Built_in'=> $value['details'][8],
                    'property_features' => 1  ,      //ozellikler
                    'lot_size' => $value['details'][16], 
                    ]);
         }
        dd($ilanlar);
    }
}
