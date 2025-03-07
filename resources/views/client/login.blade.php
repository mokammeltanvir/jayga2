<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
  <title>Jayga | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Epilogue">
  <style>
    body{
      background-image: url({{asset('assets/img/login-bg.jpg')}});
      background-size: cover;
      background-repeat: no-repeat;
      font-family: "Epilogue";
      overflow-y: hidden;
    }
   .container{
      display: flex; /* Use flexbox */
      justify-content: center; /* Center horizontally */
      align-items: center; /* Center vertically */
      height: 100vh; /* Set height of container, adjust as needed */
   }
    .row{
     
      width: 100%;
      align-items: center;
    }

    .card-text{
      color: azure;
    }

    .col{
      padding: 0 0px;
      font-size: 18px;
      
    }

    #exampleFormControlInput1::placeholder{
      color: #999;
    }
  </style>
</head>
<body>
  
  <div class="container" >
    <div class="row justify-content-between">
      <div class="col-md-5 col-lg-5">
        <div class="card p-4 text-center" style="border: 0; background-color: rgba(255, 255, 255, 0.01); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-blend-mode: overlay,normal; backdrop-filter: blur(1px);">
          <img src="{{asset('assets/img/logo/jayga-01.png')}}" class="m-auto" style="width: 100px; height: 100px;"  alt="">
          <!--card header-->
          <h1 class="card-text" style="font-weight: 700; font-size: 50px;">Welcome</h1>
          <div class="card-text mb-3" style="font-size: 20px;">
            Explore stays, experiences, <br> and storage spaces easily
          </div>
          <div class="card-text my-3">
            <div class="row mb-3">
              <div class="col">Rooms</div>
              <div class="col">Apartments</div>
              <div class="col">Hotels</div>
            </div>
            <div class="row mb-3">
              <div class="col">Parking</div>
              <div class="col">Experience</div>
              <div class="col">Storage</div>
            </div>
          </div>

          <a class="card-text" style="text-decoration: none; font-size: 24px;"><span class="px-3"><img src="{{asset('assets/img/QR Code.png')}}" style="width: 42px; height: 42px;" alt=""></span>Get the app</a>
          <!--card header-->

        </div>
      </div>
      <div class="col-md-5 col-lg-5 col-sm-12">
        <div class="card p-4" style="background-color: rgba(0, 0, 0, 0.332); background-blend-mode: overlay, normal;
        backdrop-filter: blur(40px);">
          <div class="card-title">
            <h2 class="card-text">Sign in</h2>
            </div>
            <form action="{{route('clientotp')}}" method="POST" >
              @csrf
             
              <div class="input-group mb-3">
                <!--<span class="input-group-text" style="background-color: rgb(0, 0, 1); color: azure; " id="basic-addon1">🇧🇩 +880</span> -->
                
                <input type="text" class="form-control p-3" style="background-color: rgb(0, 0, 1); color: azure; " id="exampleFormControlInput1" placeholder="phone number or email" name="txt">
              </div>
              <div class="mb-3">
                <button class="form-control" style="background-color: rgba(0, 0, 0, 0.332); color: azure;" type="submit">Sign in</button>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>