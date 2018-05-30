<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
</head>
<body>
<video id="screenshot-video" autoplay></video>
<img id="screenshot-img" src="">
<p><button id="screenshot-button">Take Screenshot</button></p>

<script>
  const button = document.querySelector('#screenshot-button');
  const img = document.querySelector('#screenshot-img');
  const video = document.querySelector('#screenshot-video');

  const canvas = document.createElement('canvas');

  button.onclick = video.onclick = function() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    var context = canvas.getContext('2d');
    context.drawImage(video, 0, 0);
    // Other browsers will fall back to image/png
    img.src = canvas.toDataURL('image/webp');

    var imgData = img.src;

    axios({
        method: 'post',
        url: '/storage/take-picture.php',
        data: {
            pictureInfo: imgData
        }
    })
    .then(function(response){
        console.log(response);
        img.src = 'https://s3-us-west-1.amazonaws.com/juddfranklin-bucket1/' + response.data;
    })
    .catch(function(error) {
        console.warn(error);
    })

  };


function hasGetUserMedia() {
  return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

if (hasGetUserMedia()) {
    // Good to go!
    const constraints = {
        video: true
    };

    const video = document.querySelector('video');

    function handleSuccess(stream) {
        stream.onactive = function(e){
            console.log(active,e);
        }
        video.srcObject = stream;
    }

    function handleError(error) {
        console.error('Reeeejected!', error);
    }

    navigator.mediaDevices.getUserMedia(constraints).
    then(handleSuccess).catch(handleError);
} else {
  alert('getUserMedia() is not supported by your browser');
}

</script>
</body>
</html>
