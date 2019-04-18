<?php foreach($errors as $error): ?>
<div class="error"><p><strong>Torque Gateway Error</strong>: <?php echo $error; ?></p></div>
<?php endforeach; ?>

<h1>Torque Gateway Settings</h1>

<?php if($confirm_type === 'torque-wallet-rpc'): ?>
<div style="border:1px solid #ddd;padding:5px 10px;">
    <?php
         echo 'Wallet height: ' . $balance['height'] . '</br>';
         echo 'Your balance is: ' . $balance['balance'] . '</br>';
         echo 'Unlocked balance: ' . $balance['unlocked_balance'] . '</br>';
         ?>
</div>
<?php endif; ?>

<table class="form-table">
    <?php echo $settings_html ?>
</table>

<h4><a href="https://github.com/contribute-torque/torquewp">Learn more about using the Torque payment gateway</a></h4>

<script>
function torqueUpdateFields() {
    var confirmType = jQuery("#woocommerce_torque_gateway_confirm_type").val();
    if(confirmType == "torque-wallet-rpc") {
        jQuery("#woocommerce_torque_gateway_torque_address").closest("tr").hide();
        jQuery("#woocommerce_torque_gateway_viewkey").closest("tr").hide();
        jQuery("#woocommerce_torque_gateway_daemon_host").closest("tr").show();
        jQuery("#woocommerce_torque_gateway_daemon_port").closest("tr").show();
    } else {
        jQuery("#woocommerce_torque_gateway_torque_address").closest("tr").show();
        jQuery("#woocommerce_torque_gateway_viewkey").closest("tr").show();
        jQuery("#woocommerce_torque_gateway_daemon_host").closest("tr").hide();
        jQuery("#woocommerce_torque_gateway_daemon_port").closest("tr").hide();
    }
    var useTorquePrices = jQuery("#woocommerce_torque_gateway_use_torque_price").is(":checked");
    if(useTorquePrices) {
        jQuery("#woocommerce_torque_gateway_use_torque_price_decimals").closest("tr").show();
    } else {
        jQuery("#woocommerce_torque_gateway_use_torque_price_decimals").closest("tr").hide();
    }
}
torqueUpdateFields();
jQuery("#woocommerce_torque_gateway_confirm_type").change(torqueUpdateFields);
jQuery("#woocommerce_torque_gateway_use_torque_price").change(torqueUpdateFields);
</script>

<style>
#woocommerce_torque_gateway_torque_address,
#woocommerce_torque_gateway_viewkey {
    width: 100%;
}
</style>
