<!doctype html>
<html lang="en">

<head>
    <title>CHECKOUT.COM</title>
    <style>
        .card-number {
            color: black;
            font-size: 18px;
        }
        .card-number:-webkit-autofill {
            background-color: yellow;
        }
        .card-number--autofilled {
            background-color: yellow;
        }
        .card-number--hover {
            color: blue;
        }
        .card-number--focus {
            color: blue;
        }
        .card-number--valid {
            color: green;
        }
        .card-number--invalid {
            color: red;
        }
        input.card-number::-webkit-input-placeholder {
            color: gray;
        }
        input.card-number::-moz-placeholder {
            color: gray;
        }
        input.card-number:-ms-input-placeholder {
            color: gray;
        }
        input.card-number--focus::-webkit-input-placeholder {
            border: solid 1px blue;
        }
        input.card-number--focus::-moz-placeholder {
            border: solid 1px blue;
        }
        input.card-number--focus:-ms-input-placeholder {
            border: solid 1px blue;
        }

        .expiry-date {
            color: black;
            font-size: 18px;
        }
        .expiry-date:-webkit-autofill {
            background-color: yellow;
        }
        .expiry-date--autofilled {
            background-color: yellow;
        }
        .expiry-date--hover {
            color: blue;
        }
        .expiry-date--focus {
            color: blue;
        }
        .expiry-date--valid {
            color: green;
        }
        .expiry-date--invalid {
            color: red;
        }
        input.expiry-date::-webkit-input-placeholder {
            color: gray;
        }
        input.expiry-date::-moz-placeholder {
            color: gray;
        }
        input.expiry-date:-ms-input-placeholder {
            color: gray;
        }
        input.expiry-date--focus::-webkit-input-placeholder {
            border: solid 1px blue;
        }
        input.expiry-date--focus::-moz-placeholder {
            border: solid 1px blue;
        }
        input.expiry-date--focus:-ms-input-placeholder {
            border: solid 1px blue;
        }

        .cvv {
            color: black;
            font-size: 18px;
        }
        .cvv:-webkit-autofill {
            background-color: yellow;
        }
        .cvv--autofilled {
            background-color: yellow;
        }
        .cvv--hover {
            color: blue;
        }
        .cvv--focus {
            color: blue;
        }
        .cvv--valid {
            color: green;
        }
        .cvv--invalid {
            color: red;
        }
        input.cvv::-webkit-input-placeholder {
            color: gray;
        }
        input.cvv::-moz-placeholder {
            color: gray;
        }
        input.cvv:-ms-input-placeholder {
            color: gray;
        }
        input.cvv--focus::-webkit-input-placeholder {
            border: solid 1px blue;
        }
        input.cvv--focus::-moz-placeholder {
            border: solid 1px blue;
        }
        input.cvv--focus:-ms-input-placeholder {
            border: solid 1px blue;
        }
    </style>
    <script src="https://cdn.checkout.com/js/framesv2.min.js"></script>

</head>

<body>


<form id="payment-form" method="POST" action="{{route('checkout')}}">
    @csrf
    <div class="card-frame">

    </div>
    <button id="pay-button" disabled>PAY GBP 24.99</button>
</form>

<script>
    var payButton = document.getElementById('pay-button');
    var form = document.getElementById('payment-form');

    Frames.init('pk_sbox_5nwig6liqlwkntbn6g5tqvfx5eg');

    Frames.addEventHandler(Frames.Events.CARD_VALIDATION_CHANGED, function (event) {
        console.log('CARD_VALIDATION_CHANGED: %o', event);

        payButton.disabled = !Frames.isCardValid();
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        Frames.submitCard()
            .then(function (data) {
                console.log('tk : '+data.token);
                Frames.addCardToken(form, data.token);
                form.submit();
            })
            .catch(function (error) {
                // handle error
            });
    });
</script>
</body>

</html>
