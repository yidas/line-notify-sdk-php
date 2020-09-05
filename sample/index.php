<?php

require __DIR__ . '/_config.php';

// Route process
$route = isset($_GET['route']) ? $_GET['route'] : null;
switch ($route) {
  case 'clear':
    session_destroy();
    // Redirect back
    header('Location: ./index.php');
    break;

  case 'index':
  default:
    # code...
    break;
}

// Get last form data if exists
$config = isset($_SESSION['config']) ? $_SESSION['config'] : [];

$credential = Credential::get();
if ($credential) {
    $config['clientId'] = $credential['clientId'];
    $config['clientSecret'] = '************************';
}

$accessTokenList = isset($_SESSION['config']['accessTokens']) ? $_SESSION['config']['accessTokens'] : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" class="js-site-favicon" href="https://github.com/fluidicon.png">
    <title>Sample - yidas/line-notify-sdk-php</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
      pre.log {
        word-break: break-all; 
        white-space: pre-wrap; 
        font-size: 9pt;
        background-color: #f5f5f5;
        padding: 5px;
      }
    </style>
    <script>
      /**
       * OnFormSubmit for multiple action
       */
      function OnFormSubmit(form) {
        switch (document.formAction) {
          case "notify":
            if (!document.getElementsByName('accessTokens[]')[0].value) {
              alert('AccessToken input is empty');
              return false;
            }
            form.action = 'notify.php';
            break;
            
          case "authorized":
          default:
            form.action = 'authorize.php';
            break;
        }
        return true;
      }
    </script>
</head>
<body>
<div style="padding:30px 10px; max-width: 600px; margin: auto;">
  <h3>LINE Notify API Tool <a href="https://github.com/yidas/line-notify-sdk-php"><img src="https://github.com/favicon.ico" height="20" width="20"></a></h3>

  <form method="POST" onsubmit="return OnFormSubmit(this);">
    <div class="merchant-block" data-block-id="custom">
      <div class="form-group">
        <label for="inputClientId">ClientId</label>
        <input type="text" class="form-control" id="inputClientId" name="clientId" placeholder="Enter ClientId" value="<?=(!isset($config['credential']) && isset($config['clientId'])) ? $config['clientId'] : ''?>" required>
      </div>
      <div class="form-group">
        <label for="inputClientSecret">ClientSecret</label>
        <input type="text" class="form-control" id="inputClientSecret" name="clientSecret" placeholder="Enter ClientSecret" value="<?=(!isset($config['credential']) && isset($config['clientSecret'])) ? $config['clientSecret'] : ''?>" required>
      </div>
    </div>
    <div class="form-group">
      <label for="inputMessage">Message</label>
      <input type="text" class="form-control" id="inputMessage" name="message" placeholder="Notify Message Text"  value="Hello Text">
    </div>
    <div class="form-group">
      <label for="inputAccessToken">AccessToken</label>
      <input type="text" class="form-control" id="inputAccessToken" name="accessTokens[]" placeholder="User's AccessToken" value="<?=isset($accessTokenList[0]) ? $accessTokenList[0] : ''?>" value="255">
      <?php foreach ((array)$accessTokenList as $key => $accessToken): if ($key > 0): ?>
      <input type="text" class="form-control" name="accessTokens[]" placeholder="User's AccessToken" value="<?=($accessToken) ? $accessToken : ''?>" value="255">
      <?php endif; endforeach; ?>
    </div>
    <hr>
    <div class="row">
      <div class="col col-12 col-md-4" style="padding-bottom:5px;">
        <button type="submit" class="btn btn-primary btn-block" onclick="document.formAction=this.value" value="authorize">Authorize</button>
      </div>
      <div class="col col-12 col-md-4" style="padding-bottom:5px;">
        <button type="submit" class="btn btn-info btn-block" onclick="document.formAction=this.value" value="notify">Send Notify</a>
      </div>
      <div class="col col-12 col-md-2" style="padding-bottom:5px;">
        <button type="reset" class="btn btn-success btn-block">Reset</button>
      </div>
      <div class="col col-12 col-md-2" style="padding-bottom:5px;">
        <button type="button" class="btn btn-danger btn-block" onclick="if(confirm('Confirm to clear saved form data?')){location.href='?route=clear'}">Clear</button>
      </div>
    </div>
  </form>

</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>

</script>
</body>
</html>