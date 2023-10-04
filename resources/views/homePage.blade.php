<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>

<style>
   .box{
        height: 70vh !important;
        display: flex;
        align-items: center;
    }
    .DashInfo{
        margin-left: 20px;
    }
    .praava-color{
        color: #8A2061;
    }
    .info-box{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .asset-title{
        font-weight: bold;
        margin: 20px 0px;
    }
    .btn-primary, .btn-info:hover{
        background-color: #8A2061;
        border: 3px solid #8A2061;
        border-radius: 10px;
    }
    .btn-info, .btn-primary:hover{
        background-color: #fff;
        color: #8A2061;
        border: 3px solid #8A2061;
        border-radius: 5px;
    }
</style>
<body>
    <div class="box col-8 mx-auto ">
        <div class="info-box">
        <div class="wc border-end">
            <img src="" alt="">
            {{-- <lottie-player src="https://assets7.lottiefiles.com/packages/lf20_dDFzRzv97x.json"  background="#fff"  speed="1"  style="width: 400px; height: 400px;"  loop autoplay></lottie-player> --}}
        </div>
        <div class="DashInfo border-start">
            <h1 class="asset-title fw-bold mx-auto praava-color" >Welcome To Praava Health <br> Lab TAT</h1>
            <div class="asset-btns">
                <a href="admin/lab-tat" class="asset-btn btn-primary btn btn-lg ">LAB TAT</a>
            </div>
        </div>
        </div>
    </div>
</body>
</html>