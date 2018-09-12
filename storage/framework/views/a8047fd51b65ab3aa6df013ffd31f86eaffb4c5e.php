<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>發票輸出</title>

        <!-- Bootstrap -->
        <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            @import  url(http://fonts.googleapis.com/earlyaccess/notosanstc.css);
            body {
                background: #E8F1F2;
                font-family: 'Noto Sans TC', sans-serif;
                font-weight: 300;
            }
            h1, h2, h3{
                font-weight: 300;
                color: #132A3C;
                font-size: 27px;
                line-height: 27px;
                margin-top: 35px;
                margin-bottom: 15px;
                text-align: center;
            }
            h2{
                font-size: 21px;
                line-height: 30px;
                margin-top: 25px;
                margin-bottom: 10px;
            }
            p, li{
                font-size: 17px;
                line-height: 27px;
                color: #666666;
                margin-bottom: 10px;
                margin-top: 5px;
                text-align: justify;
            }
            li{
                margin-top: 5px
            }
            ol{
                margin-left: -25px
            }
            ol ol{
                margin-left: -25px;
                list-style-type: lower-alpha
            }
            .container{
                /*padding: 0 25px 20px 25px;*/
            }
            .bigger{
                font-size: 19px;
                font-weight: 500;
            }
            .bigger-b{
                font-size: 21px;
                font-weight: 500;
            }
            .center{
                text-align: center;
            }
            svg{
                display: block;
                margin: auto;
                margin-top: 25px;
            }
            .title{
                color: #333;
                font-weight: 500;
                margin-bottom: 5px;
            }
            .col-6{
                float: left;
                width: 50%;
                display: block;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                background: #FFF;
            }
            .col-6:first-child{
                border-right: 1px solid #CCC;
            }
            .smaller{
                font-size: 14px;
                margin-bottom: 5px;
            }
            .row:after{
                content: ".";
                display: block;
                height: 0;
                clear: both;
                visibility: hidden;
            }
            .row{
                margin-top: 25px;
                border-top: 1px solid #CCC;
                border-bottom: 1px solid #CCC;
            }
            .list{
                border-top: 1px solid #CCC;
                background: #FFF;
                padding-left: 15px;
                padding-right: 15px;
                padding-top: 10px;
            }
            .list:last-child{
                border-bottom: 1px solid #CCC;
            }
            .list:after{
                content: ".";
                display: block;
                height: 0;
                clear: both;
                visibility: hidden;
            }
            .list .title{
                font-size: 22px;
                font-weight: 300;
            }
            .list .status{
                font-size: 17px;
                color: #979797;
            }
            .list .price{
                float: right;
                margin: 0;
                line-height: 37px;
                font-size: 22px;
                font-weight: 500;
            }
            body{
                margin: 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>電子發票通知信</h1>
            <!-- <div class="row">
              <div class="col-6">
                <p class="smaller center">本月治療收入 (70%)</p>
                <p class="bigger center">NT 7,700</p>
              </div>
              <div class="col-6">
                <p class="smaller center">每月會費回補</p>
                <p class="bigger center">NT 1,500</p>
              </div>  
            </div> -->
            <p class="center" style="margin: 15px auto 5px auto">消費金額</p>
            <p class="bigger-b center" style="margin-bottom: 20px">NT <?php echo e(number_format($invoice->TotalAmt, 0, '', ',')); ?></p>
            
            <div class="list">
                <p class="price">您好健康網路科技股份有限公司</p>
                <p class="title">開立店家/公司</p>
                <p class="status"></p>
            </div>

            <div class="list">
                <p class="price"><?php echo e($invoice->CreateTime); ?></p>
                <p class="title">發票日期</p>
                <p class="status"></p>
            </div>

            <div class="list">
                <p class="price"><?php echo e($invoice->InvoiceNumber); ?></p>
                <p class="title">發票號碼</p>
                <p class="status"></p>
            </div>

            <div class="list">
                <p class="price"><?php echo e($invoice->RandomNum); ?></p>
                <p class="title">發票隨機碼</p>
                <p class="status"></p>
            </div>

            <div class="list">
                <p class="price">NT <?php echo e(number_format($invoice->TotalAmt, 0, '', ',')); ?></p>
                <p class="title">消費金額</p>
                <p class="status"></p>
            </div>
           
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <!--<script src="js/bootstrap.min.js"></script> -->
    </body>
</html>
