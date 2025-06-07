<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <br><link rel="stylesheet" href="https://yookassa.ru/integration/simplepay/css/yookassa_construct_form.css?v=1.24.0">
<form class="yoomoney-payment-form" action="https://yookassa.ru/integration/simplepay/payment" method="post" accept-charset="utf-8" >

    
    

    

    <div class="ym-hidden-inputs">
       <input name="shopSuccessURL" type="hidden" value="e-shop/checkout.php">
       
       
    </div>

    
    
    <div class="ym-payment-btn-block">
        <div class="ym-input-icon-rub ym-display-none">
            <input name="sum" placeholder="0.00" class="ym-input ym-sum-input ym-required-input" type="number" step="any" value="100">
        </div>
        <button data-text="Оплатить" class="ym-btn-pay ym-result-price"><span class="ym-text-crop">Оплатить</span> <span class="ym-price-output"> 100,00&nbsp;₽</span></button><img src="https://yookassa.ru/integration/simplepay/img/iokassa-gray.svg?v=1.24.0" class="ym-logo" width="114" height="27" alt="ЮKassa">
    </div>
<input name="shopId" type="hidden" value="1103663"></form>
<script src="https://yookassa.ru/integration/simplepay/js/yookassa_construct_form.js?v=1.24.0"></script>
</body>
</html>