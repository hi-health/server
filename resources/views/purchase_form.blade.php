<body onload="document.purchase.submit()">
<!--    <h3>訂單編號: {{ $order_number }}</h3>
    <h3>付款金額: {{ $amount }}</h3>-->
    <form name="purchase" method="post" action="{{ $action }}">
        @foreach($parameters as $key => $value)
            <div>
                <!--<label>{{ $key }}</label>-->
                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
            </div>
        @endforeach
        <!--<button type="submit">Submit</button>-->
    </form>
</body>
