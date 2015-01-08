<?php

class PagseguroPayment {

    public function __construct() {
        Pagseguro_Configuration::environment('sandbox');
        Pagseguro_Configuration::merchantId('6sghjghjcj7w');
        Pagseguro_Configuration::publicKey('b8f2hgjgh');
        Pagseguro_Configuration::privateKey('8839084354235660065f');
    }

    public static function button($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null,$plano='') {
        $extra = payment_prepare_custom($extra_array);
       
        $r = rand(0, 1000);
        $extra .= 'random,' . $r;

        $URL_PAGAMENTO = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'pagar.php?extra=' . $extra;
        //echo $amount;
        /*
         * inserir aqui o procedimento para o pagseguro
         */



        echo '<a href="'.$URL_PAGAMENTO.'&valor=' . $amount . '&item=' . $itemnumber .'&plano='.$plano.'" >' . __('Pague com Pagseguro', 'payment') . '</a>';

       
    }

    //style="display: none"
    public static function dialogJS() {
        ?>
        <div id="pagseguro-dialog" >
            <div id="pagseguro-info">
                <div id="pagseguro-data">
                    <p id="pagseguro-desc"></p>
                    <p id="pagseguro-price"></p>
                </div>
                <form action="<?php echo osc_base_url(true); ?>" method="POST" id="pagseguro-payment-form" >
                    <input type="hidden" name="page" value="ajax" />
                    <input type="hidden" name="action" value="runhook" />
                    <input type="hidden" name="hook" value="pagseguro" />
                    <input type="hidden" name="extra" value="" id="pagseguro-extra" />
                    <p>
                        <label><?php _e('Card number', 'payment'); ?></label>
                        <input type="text" size="20" autocomplete="off" data-encrypted-name="pagseguro_number" />
                    </p>
                    <p>
                        <label><?php _e('CVV', 'payment'); ?></label>
                        <input type="text" size="4" autocomplete="off" data-encrypted-name="pagseguro_cvv" />
                    </p>
                    <p>
                        <label><?php _e('Expiration (MM/YYYY)', 'payment'); ?></label>
                        <input type="text" size="2" data-encrypted-name="pagseguro_month" /> / <input type="text" size="4" data-encrypted-name="pagseguro_year" />
                    </p>
                    <input type="submit" id="submit" />
                </form>
            </div>
            <div id="pagseguro-results" style="display:none;" ><?php _e('Processing payment, please wait.', 'payment'); ?></div>
        </div>
        <script type="text/javascript" src="https://js.pagsegurogateway.com/v1/pagseguro.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#pagseguro-dialog").dialog({
                    autoOpen: false,
                    modal: true
                });
            });

            var ajax_submit = function (e) {
                form = $('#pagseguro-payment-form');
                e.preventDefault();
                $("#submit").attr("disabled", "disabled");
                $("#pagseguro-info").hide();
                $("#pagseguro-results").html('<?php _e('Processing the payment, please wait', 'payment'); ?>');
                $("#pagseguro-results").show();
                $.post(form.attr('action'), form.serialize(), function (data) {
                    console.log(data);
                    $("#pagseguro-results").html(data);
                });
            };
            var pagseguro = Pagseguro.create('<?php echo payment_decrypt(osc_get_preference('pagseguro_encryption_key', 'payment')); ?>');
            pagseguro.onSubmitEncryptForm('pagseguro-payment-form', ajax_submit);

            function pagseguro_pay(amount, description, itemnumber, extra) {
                $("#pagseguro-extra").prop('value', extra);
                $("#pagseguro-desc").html(description);
                $("#pagseguro-price").html(amount + " <?php echo osc_get_preference("currency", "payment"); ?>");
                $("#pagseguro-results").html('');
                $("#pagseguro-results").hide();
                $("#submit").removeAttr('disabled');
                $("#pagseguro-info").show();
                $("#pagseguro-dialog").dialog('open');
            }

        </script>
        <?php
    }

    public static function ajaxPayment() {
        $status = PagseguroPayment::processPayment();
        if ($status == PAYMENT_COMPLETED) {
            printf(__('Success! Please write down this transaction ID in case you have any problem: %s', 'payment'), Params::getParam('pagseguro_transaction_id'));
        } else if ($status == PAYMENT_ALREADY_PAID) {
            _e('Warning! This payment was already paid', 'payment');
        } else {
            _e('There were an error processing your payment', 'payment');
        }
    }

    /* public static function processPayment() {
      require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'lib/Pagseguro.php';

      Pagseguro_Configuration::environment(osc_get_preference('pagseguro_sandbox', 'payment'));
      Pagseguro_Configuration::merchantId(payment_decrypt(osc_get_preference('pagseguro_merchant_id', 'payment')));
      Pagseguro_Configuration::publicKey(payment_decrypt(osc_get_preference('pagseguro_public_key', 'payment')));
      Pagseguro_Configuration::privateKey(payment_decrypt(osc_get_preference('pagseguro_private_key', 'payment')));

      $data = payment_get_custom(Params::getParam('extra'));

      $tmp = explode('x', $data['product']);
      if(count($tmp)>1) {
      $amount = $tmp[1];
      } else {
      return PAYMENT_FAILED;
      }

      $result = Pagseguro_Transaction::sale(array(
      'amount' => $amount,
      'creditCard' => array(
      'number' => Params::getParam('pagseguro_number'),
      'cvv' => Params::getParam('pagseguro_cvv'),
      'expirationMonth' => Params::getParam('pagseguro_month'),
      'expirationYear' => Params::getParam('pagseguro_year')
      ),
      'options' => array(
      'submitForSettlement' => true
      )
      ));

      print_r($result);

      if($result->success==1) {
      Params::setParam('pagseguro_transaction_id', $result->transaction->id);
      $exists = ModelPayment::newInstance()->getPaymentByCode($result->transaction->id, 'BRAINTREE');
      if(isset($exists['pk_i_id'])) { return PAYMENT_ALREADY_PAID; }
      $product_type = explode('x', $data['product']);
      // SAVE TRANSACTION LOG
      $payment_id = ModelPayment::newInstance()->saveLog(
      $data['concept'], //concept
      $result->transaction->id, // transaction code
      $result->transaction->amount, //amount
      $result->transaction->currencyIsoCode, //currency
      $data['email'], // payer's email
      $data['user'], //user
      $data['itemid'], //item
      $product_type[0], //product type
      'BRAINTREE'); //source

      if ($product_type[0] == '101') {
      ModelPayment::newInstance()->payPublishFee($product_type[2], $payment_id);
      } else if ($product_type[0] == '201') {
      ModelPayment::newInstance()->payPremiumFee($product_type[2], $payment_id);
      } else {
      ModelPayment::newInstance()->addWallet($data['user'], $result->transaction->amount);
      }

      return PAYMENT_COMPLETED;
      } else {
      return PAYMENT_FAILED;
      }
      }

     */

    public static function processPayment() {


        require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'PagSeguroLibrary/PagSeguroLibrary.php';
        // Instantiate a new payment request
        $paymentRequest = new PagSeguroPaymentRequest();

        // Set the currency
        $paymentRequest->setCurrency("BRL");

        // Add an item for this payment request
        $paymentRequest->addItem('0001', 'Notebook prata', 2, 430.00);

        // Add another item for this payment request
        $paymentRequest->addItem('0002', 'Notebook rosa', 2, 560.00);

        // Set a reference code for this payment request. It is useful to identify this payment
        // in future notifications.
        $paymentRequest->setReference("REF123");

        // Set shipping information for this payment request
        $sedexCode = PagSeguroShippingType::getCodeByType('SEDEX');
        $paymentRequest->setShippingType($sedexCode);
        $paymentRequest->setShippingAddress(
                '01452002', 'Av.', '16784', 'apto. 14', 'Jardim ', 'São Paulo', 'SP', 'BRA'
        );

        // Set your customer information.
        $paymentRequest->setSender(
                'João Comprador', 'email@comprador.com.br', '11', '56273440', 'CPF', '156.009.442-76'
        );

        // Set the url used by PagSeguro to redirect user after checkout process ends
        $paymentRequest->setRedirectUrl("http://www.lojamodelo.com.br");

        // Add checkout metadata information
        $paymentRequest->addMetadata('PASSENGER_CPF', '15600944276', 1);
        $paymentRequest->addMetadata('GAME_NAME', 'DOTA');
        $paymentRequest->addMetadata('PASSENGER_PASSPORT', '23456', 1);

        // Another way to set checkout parameters
        $paymentRequest->addParameter('notificationURL', 'http://www.loja.com.br/nas');
        $paymentRequest->addParameter('senderBornDate', '07/05/1981');
        $paymentRequest->addIndexedParameter('itemId', '0003', 3);
        $paymentRequest->addIndexedParameter('itemDescription', 'Notebook Preto', 3);
        $paymentRequest->addIndexedParameter('itemQuantity', '1', 3);
        $paymentRequest->addIndexedParameter('itemAmount', '200.00', 3);

        try {

            /*
             * #### Credentials #####
             * Replace the parameters below with your credentials (e-mail and token)
             * You can also get your credentials from a config file. See an example:
             * $credentials = PagSeguroConfig::getAccountCredentials();
              // */
            $credentials = new PagSeguroAccountCredentials("vendedor@loja.com.br", "E231B2C9BC60B6C8CF60D3");

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
        }
    }

}
?>