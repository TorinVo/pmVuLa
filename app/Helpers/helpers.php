<?php 

function convertLink($message)
{
    preg_match_all('/##i\d+$|##i\d+\s/', $message, $listImg);
    if(!empty($listImg[0])){
        foreach ($listImg[0] as $key => $value) {
            $temp = explode('##i', trim($value));
            if(!empty($temp)){
                $img = App\Image::find($temp[1]);
                if($img)
                    $message = str_replace(trim($value), '##vuelinki'.$img->id.'i'.substr($img->name, 1).' ', $message);
            }
        }
    }
    preg_match_all('/##\d+$|##\d+\s/', $message, $listLink);
    if(!empty($listLink[0])){
        foreach ($listLink[0] as $key => $value) {
            $temp = explode('##', trim($value));
            $message = str_replace(trim($value), '##vuelink'.$temp[1].' ', $message);
        }
    }

    return $message;
}