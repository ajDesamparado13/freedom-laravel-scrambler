<?php
namespace Freedom\Scrambler\Impl;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;

class Scrambler {

    const SESSION_INDEX  = 0;
    const APP_INDEX  = 1;
    const IP_INDEX  = 2;
    const DELIMITER = '#xrx#';

    public function encrypt($value){
        if (!$value) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $body = $this->makeBody();
        $body[] = $value;

        return Crypt::encrypt(implode(self::DELIMITER,$body));
    }

    public function decrypt($encrypted){
        if ($encrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        $body = explode(self::DELIMITER, Crypt::decrypt($encrypted));
        foreach($this->makeBody() as $key => $value){
            if($body[$key] !== $value){
                abort(404);
            }
        }

        return Arr::last($body);
    }

    protected function makeBody(){
        return [
            self::SESSION_INDEX => $this->getSessionId(),
            self::APP_INDEX => $this->getAppKey(),
            self::IP_INDEX => $this->getIP(),
        ];
    }

    protected function getSessionId(){
        return Session::getId();
    }

    protected function getAppKey(){
        return \Config::get('app.key');
    }

    protected function getIP(){
        //TODO ADD config if do add ip
        return "ip_placeholder";
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        $ip = \Request::getClientIps();
        if(is_array($ip)){
            $ip = end($ip);
        }
        return $ip;
    }
}
