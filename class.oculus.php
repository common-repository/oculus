<?php

class Oculus {
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
		Oculus::log("start init");
        add_action( 'comment_form', array( 'Oculus', 'inject_oculus_js' ) );

        add_action( 'wp_insert_comment', array( 'Oculus', 'auto_check_oclus_info' ), 10, 2 );
    }

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		Oculus::log("activation");
	}


	public static function inject_oculus_js( $fields ) {
		echo '<script data-app="'.base64_encode('{"uab": {"GPInterval": 50, "MaxFocusLog": 150, "GetAttrs": ["href", "src"], "MaxGPLog": 5, "Flag": 1965567, "useCustomToken": false, "MaxKSLog": 150, "SendMethod": 3, "LogVal": "ua_log", "FormId": "my_form", "ExTarget": ["pwdid"], "MaxMPLog": 150, "MPInterval": 50, "MaxMCLog": 150, "SendInterval": 20, "isSendError": 1, "ImgUrl": "//cfd.aliyun.com/collector/analyze.jsonp", "MaxTCLog": 150}, "common": {"appkey": "'.get_option('oculus_appkey','BABA').'", "useCustomToken": false, "scene": "register", "foreign": 0}, "umid": {"timeout": 3000, "appName": "$!appName", "timestamp": "$!timestamp", "token": "$!token", "serviceUrl": "https://ynuf.alipay.com/service/um.json", "containers": {"flash": "container", "dcp": "container"}}}').'" src="//g.alicdn.com/sd/pointman/js/pt.js"></script>';
        echo '<script>function oculus_inject(){var f = document.getElementById("commentform");var i = document.createElement("input");i.setAttribute("type", "hidden"); i.setAttribute("name","o_tk"); i.setAttribute("value", pointman.getToken()); f.appendChild(i)};document.getElementById("submit").onclick= oculus_inject;</script>';
     


	}

    
    private static function percent_encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
            

    private static function computeSignature($parameters, $accessKeySecret)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach($parameters as $key => $value)
        {
            $canonicalizedQueryString .= '&' . self::percent_encode($key). '=' . self::percent_encode($value);
        }
        $stringToSign = 'GET&%2F&' . self::percent_encode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign , $accessKeySecret."&" , true));
    
        return $signature;
    }

	/**
	 * Make a GET request to the Oculus API.
	 *
	 * @param string $ty_token The token to judge risk .
	 * @return bool Whether the comment should be treated as spam comment.
	 */
	public static function check_oculus_risk( $ty_token ) {

        $query_params = array();

//        self::log( get_option('oculus_appkey')."|".get_option('oculus_appsecret')."|".get_option('oculus_accesskeyid')."|".get_option('oculus_accesskeysecret') );
        //get_option('oculus_api_key');
        $query_params["TokenId"] = $ty_token ;
        $query_params["SceneId"] = "register";
        $query_params["TimeStamp"] = time();
        $query_params["AppKey"] = get_option('oculus_appkey');
        $query_params["SerialNo"] = md5($query_params["AppKey"].get_option('oculus_appsecret').$query_params["TokenId"].$query_params["TimeStamp"]);
 //       self::log($query_params["AppKey"].get_option('oculus_appsecret').$query_params["TokenId"].$query_params["TimeStamp"]);
 //       self::log($query_params["SerialNo"]);
        $query_params["RegionId"] = "cn-hangzhou";
        $query_params["AccessKeyId"] = get_option('oculus_accesskeyid');
        $query_params["Format"] = "JSON";
        $query_params["SignatureMethod"] = "HMAC-SHA1";
        $query_params["SignatureVersion"] = "1.0";
        $query_params["SignatureNonce"] = uniqid();
        date_default_timezone_set("GMT");
        $query_params["Timestamp"] = date('Y-m-d\TH:i:s\Z');
        $query_params["Action"] = "QuerySimple";
        $query_params["Version"] = "2015-11-27";
        $query_params["Signature"] = self::computeSignature($query_params , get_option('oculus_accesskeysecret') );

        $request_url = "https://cf.aliyuncs.com/?";

        foreach ($query_params as $query_param_key => $query_param_value)
        {
                $request_url .= "$query_param_key=" . urlencode($query_param_value) . "&";
        }
    
        self::log( $request_url );
        $response = wp_remote_get($request_url);

        $response_json = json_decode( $response['body'] );

        if( array_key_exists("CollinadataQueryResult",$response_json) && 
            array_key_exists( "Score" , $response_json->CollinadataQueryResult ) && 
            $response_json->CollinadataQueryResult->Score < 200 ){
            return true;
        }
        return false;

	}



	// this fires on wp_insert_comment.  	
public static function auto_check_oclus_info( $id, $comment ) {

        self::log("start check commet");
        if(!array_key_exists("o_tk",$_POST)  ||
             self::check_oculus_risk( $_POST["o_tk"] )){
            wp_spam_comment( $id );
            return;
        }
	}



	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		Oculus::log("deactivation");
		//tidy up
	}

	public static function view( $name ) {

		$file = OCULUS__PLUGIN_DIR . 'views/'. $name . '.php';

		include( $file );
	}



	/**
	 * Log debugging info to the error log.
	 *
	 * Enabled when WP_DEBUG_LOG is enabled, but can be disabled via the oculus_debug_log filter.
	 *
	 * @param mixed $oculus_debug The data to log.
	 */
	public static function log( $oculus_debug ) {
		if ( apply_filters( 'oclus_debug_log', defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ) {
			error_log( print_r( compact( 'oculus_debug' ), true ) );
		}
	}

}
