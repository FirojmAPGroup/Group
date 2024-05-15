<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Focus - Bootstrap Admin Dashboard </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! siteLogo() !!}">
    <link href="{{ pathAssets('css/style.css') }}" rel="stylesheet">

</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-5">
                    <div class="form-input-content text-center">
                        <div class="mb-5">
                            <a class="btn btn-primary" href="{{ routePut('app.dashboard') }}">Back to Home</a>
                        </div>
                        <h1 class="error-text font-weight-bold">500</h1>
                        <h4 class="mt-4"><i class="fa fa-exclamation-triangle text-danger"></i> Something went wrong</h4>
                        <p>Our engineers are currently fixing something.
                            We expect them to be done soon.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
