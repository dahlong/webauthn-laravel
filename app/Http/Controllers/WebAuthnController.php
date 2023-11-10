<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\MyTest\MyCustomClass;
use App\WebAuthn\WebAuthn;
use \stdClass;
use Session;
use Illuminate\Support\Facades\Log;

Class WebAuthnController extends Controller{

    public function getArgs(Request $request)
    {
        $fn = $request->input("fn");
        $myClass = new MyCustomClass();
        $rpName = "iservice web";
        $rpId = "webauthn-test.nchc.org.tw";
        
        $userId= \bin2hex("dahlong");
        $userName="Dahlong Yang";
        $userDisplayName="Dahlong";
        $requireResidentKey="False";
        $userVerification="discourage";
        $crossPlatformAttachment="False";

        $webauthn_ = new WebAuthn( $rpName, $rpId, $allowedFormats = null, $useBase64UrlEncoding = false );
        $createArgs = $webauthn_->getCreateArgs(
                \hex2bin($userId), $userName, $userDisplayName, 60*4, $requireResidentKey,
                 $userVerification, $crossPlatformAttachment);
        
        Session::put('challenge',$webauthn_->getChallenge());
        
        return response()->json($createArgs);
        //return View::make('pages.test');

    }

    public function processCreate(Request $request){
        
        $userVerification="discourage";

        //$post = $request->getContent()->json();  
        $post = json_decode($request->getContent(),true); // when the second param is True, the json_decode will return array object type. 
        
        //Log::info("type of post variable : ".gettype($post));
        
        $clientDataJSON = base64_decode($post['clientDataJSON']);
        $attestationObject = base64_decode($post['attestationObject']);
        
        $challenge = Session::get('challenge');
        Log::info("challenge is : ".$challenge );

        $rpName = "iservice web";
        $rpId = "webauthn-test.nchc.org.tw";
        $webauthn_ = new WebAuthn( $rpName, $rpId, $allowedFormats = null, $useBase64UrlEncoding = false );
        $data = $webauthn_->processCreate($clientDataJSON, $attestationObject, $challenge, $userVerification === 'required', true, false);

        // add user infos
        $data->userId = \bin2hex("dahlong");;
        $data->userName = "Dahlong Yang";
        $data->userDisplayName = "Dahlong";

        // processCreate returns data to be stored for future logins.
        // in this example we store it in the php session.
        // Normaly you have to store the data in a database connected
        // with the user name.
        // if (!isset( Session::get('registrations') ) || !array_key_exists('registrations', Session) || !is_array( Session::get('registrations') )) {
        //     Session::get('registrations') = [];
        // }
        // //$_SESSION['registrations'][] = $data;
        
        Session::put('registrations',$data);
        //Log::info("webauthn data that will be stored in DB is : ".var_dump($data));

        $msg = 'registration success.';
        if ($data->rootValid === false) {
            $msg = 'registration ok, but certificate does not match any of the selected root ca.';
        }

        $return = new stdClass();
        $return->success = true;
        $return->msg = $msg;
        
        return response()->json($return);
    }

    public function test419(Request $request){
        $post = json_decode($request->getContent(),true);
        return $post['a'];
        
        //$return = "{'a':'aa','b':'bb'}";
        //return response()->json($return);
    }
}
