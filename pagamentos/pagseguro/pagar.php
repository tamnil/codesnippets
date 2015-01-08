<?php

//


/*
 * ***********************************************************************
  Copyright [2011] [PagSeguro Internet Ltda.]

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
 * ***********************************************************************
 */
define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
require_once ABS_PATH . 'oc-load.php';


require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'PagSeguroLibrary/PagSeguroLibrary.php';

/**
 * Class with a main method to illustrate the usage of the domain class PagSeguroPaymentRequest
 */
class CreatePaymentRequest {

    public static function main() {

  
        //       var_dump();
        $item = $_GET['item'];
        $extra = $_GET['extra'];
        $plano = $_GET['plano'];
        $modeloPlano = ModelPayment::newInstance()->getPremiunsPlans($plano);
        // $modeloPlano=ModelPayment::getPremiunsPlans($plano);
        var_dump($modeloPlano);
		$modeloPlano=$modeloPlano[0];
		
        $price1 = $modeloPlano['f_premium_value1'];
        $price6 = $modeloPlano['f_premium_value6'];
        $price12 = $modeloPlano['f_premium_value12'];
        $titulo=    $modeloPlano['s_premium_title'];
        $descr=   'Plano: '.$titulo;


// Instantiate a new payment request
        $paymentRequest = new PagSeguroPaymentRequest();

        // Set the currency
        $paymentRequest->setCurrency("BRL");

        // Add an item for this payment request
        $paymentRequest->addItem($plano, $descr, 1.00 , number_format((float)$price1, 2, '.', ''));

        // Set a reference code for this payment request. It is useful to identify this payment
        // in future notifications.
        $paymentRequest->setReference("REF123");

        /* Set shipping information for this payment request
        $sedexCode = PagSeguroShippingType::getCodeByType('SEDEX');
        $paymentRequest->setShippingType($sedexCode);
        $paymentRequest->setShippingAddress(
                '01452002', 'Av. Brig. Faria Lima', '1384', 'apto. 114', 'Jardim Paulistano', 'São Paulo', 'SP', 'BRA'
        );
*/
        
        
        // Set your customer information.

        // Set the url used by PagSeguro to redirect user after checkout process ends
        $paymentRequest->setRedirectUrl("http://www.comeor.com.br/retorno");

        // Add checkout metadata information
   

        // Another way to set checkout parameters

        try {

            /*
             * #### Credentials #####
             * Replace the parameters below with your credentials (e-mail and token)
             * You can also get your credentials from a config file. See an example:
             * $credentials = PagSeguroConfig::getAccountCredentials();
              // */
            $credentials = new PagSeguroAccountCredentials("xxxx@gmail.com", "Cdfsdfsdfgsdfsdfsdfs1");

            // Register this payment request in PagSeguro to obtain the payment URL to redirect your customer.
            $url = $paymentRequest->register($credentials);

            self::printPaymentUrl($url);
        } catch (PagSeguroServiceException $e) {
            die($e->getMessage());
        }
    }

    public static function printPaymentUrl($url) {
        if ($url) {
            echo "<h2>Criando requisi&ccedil;&atilde;o de pagamento</h2>";
            echo "<p>URL do pagamento: <strong>$url</strong></p>";
            echo "<p><a title=\"URL do pagamento\" href=\"$url\">Ir para URL do pagamento.</a></p>";
			header("Location: ".$url);
die();
        }
    }

}

CreatePaymentRequest::main();
