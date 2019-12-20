# Multibanco extension for Opencart 1.5

### Customise Success.tpl
Add this to ($_['text_customer'] ) on /catalog/language/portuguese-pt/checkout/success.tpl :

<h2>Consulte o seu email caso tenha usado Comprafacil</h2>

### Customise order_info.tpl

Add this (after <b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br />) on catalog/view/theme/[default]/template/account/order_info.tpl :

<?php if (strtolower($payment_method) == 'comprafacil'){ ?>
<?php $comprafacil = $this->db->query("SELECT reference, entity, value FROM comprafacil WHERE orderID = ".$_GET['order_id']); ?>
Reference: <?php echo $comprafacil->row["reference"]; ?><br>
Entity: <?php echo $comprafacil->row["entity"]; ?><br>
Value: <?php echo $comprafacil->row["value"]; ?><br>
<?php } ?>


### Add to the email template

Add this (after // HTML Mail $template = new Template();) on catalog/model/checkout/order.php :
//MAIL CompraFacil TMPL Data
$template->data['comprafacil'] = $this->db->query("SELECT reference, entity, value FROM comprafacil WHERE orderID = ".$order_id);

and this (after <b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br />) on catalog/view/theme/[default]/template/mail/order.tpl :
<?php if (strtolower($payment_method) == 'comprafacil'){ ?>
<b>Reference:</b> <?php echo $comprafacil->row["reference"]; ?><br>
<b>Entity:</b> <?php echo $comprafacil->row["entity"]; ?><br>
<b>Value:</b> <?php echo $comprafacil->row["value"]; ?><br>
<?php } ?>

### Configure the Payment Method

Login into backend and configure the payment module with the correct values.
* The lines of the files depends on the version of opencart (this one is 1.5.1) or the template used
* replace [default] if you are using a template


v1.2 @Mindshaker
