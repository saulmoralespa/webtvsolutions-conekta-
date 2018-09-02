<?php

include_once ('config.php');
require_once ('conekta-php/lib/Conekta.php');

\Conekta\Conekta::setApiKey($keyPrivate);
\Conekta\Conekta::setApiVersion("2.0.0");
\Conekta\Conekta::setLocale('es');


if(isset($_GET['action']) && $_GET['action'] == 'rp_cancel'){
    $profile_id    = (isset($_GET["profile_id"]))? $_GET["profile_id"] : "";
    $signature     = (isset($_GET["signature"]))? rawurldecode($_GET["signature"]) : "";

    if ( $profile_id=="" ) { echo json_encode( array("error"=>"invalid profile ID..." ) ); exit; }
    if ( $signature=="" )  { echo json_encode( array("error"=>"invalid signature..." )  ); exit; }

    $data = array(
        "action"        => "rp_cancel",
        "profile_id"    => (string)$profile_id
    );
    $computed_signature = base64_encode(hash_hmac('sha256', json_encode($data), $key_signature, true));

    if ( $computed_signature!=$signature) { echo json_encode( array("error"=>"hmmm, invalid signature" ) ); exit; }


    try {
        $customer = \Conekta\Customer::find($profile_id);
        $subscription = $customer->subscription->cancel();

    } catch (\Conekta\ProcessingError $error){
        echo json_encode( array("error"=> $error->getMessage())); exit;
    } catch (\Conekta\ParameterValidationError $error){
        echo json_encode( array("error"=> $error->getMessage())); exit;
    } catch (\Conekta\Handler $error){
        echo json_encode( array("error"=> $error->getMessage())); exit;
    }

    echo json_encode( array(
        "status"            => "Cancelled"
    ));

    die();
}