<?php

namespace hasnainali9\CoreComponentRepository;
use App\Models\Addon;
use Cache;

class CoreComponentRepository
{
    public static function instantiateShopRepository() {
       
    }

    protected static function serializeObjectResponse($zn) {
     }

    protected static function finalizeRepository($rn) {
      }

    public static function initializeCache() {
        foreach(Addon::all() as $addon){
            if ($addon->purchase_code == null) {
                self::finalizeCache($addon);
            }
    
            if(Cache::get($addon->unique_identifier.'-purchased', 'no') == 'no'){
                try {
                    $gate = "https://activeitzone.com/activation/check/".$addon->unique_identifier."/".$addon->purchase_code;
        
                    $stream = curl_init();
                    curl_setopt($stream, CURLOPT_URL, $gate);
                    curl_setopt($stream, CURLOPT_HEADER, 0);
                    curl_setopt($stream, CURLOPT_RETURNTRANSFER, 1);
                    $rn = curl_exec($stream);
                    curl_close($stream);
        
                    if($rn == 'no') {
                        self::finalizeCache($addon);
                    }
                    else{
                        Cache::rememberForever($addon->unique_identifier.'-purchased', function () {
                            return 'yes';
                        });
                    }
                } catch (\Exception $e) {
        
                }
            }
        }
    }

    public static function finalizeCache($addon){
        $addon->activated = 0;
        $addon->save();

        flash('Please reinstall '.$addon->name.' using valid purchase code')->warning();
        return redirect()->route('addons.index')->send();
    } 
}
