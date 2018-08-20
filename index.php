<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
    <style>
        #photoshop {
            border: 1px solid black;
            width: 50%;
            margin: 1rem auto;
            display: block;
        }

        button {
            border: 2px solid #f3f3f3;
            background: #eee;
            color: #e07;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            font-size: 1.4rem;
            text-transform: uppercase;
            font-weight: bold;
        }
        #trash {
            background: red;
            color: white;
            padding: 1rem;
            width: 65px;
            position: fixed;
            height: 65px;
            right: 10px;
            bottom: 10px;
        }
        #trash h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<video id="screenshot-video" autoplay></video>
<img id="screenshot-img" src="">
<p><button id="capture-button">Say Cheese</button></p>
<canvas id="photoshop" draggable="true"></canvas>
<button class="blur">Blur</button>
<button class="fade">Fade</button>
<p><button id="save-button">Save Picture</button></p>
<div id="trash"><h2>Trash</h2></div>
<script>
    const saveButton = document.querySelector('#save-button');
    const captureButton = document.querySelector('#capture-button');
    const img = document.querySelector('#screenshot-img');
    const video = document.querySelector('#screenshot-video');

    const blurButton = document.querySelector('button.blur');
    const fadeButton = document.querySelector('button.fade');
        
    blurButton.onclick = function(){
        if(canvas.style.webkitFilter.indexOf('blur') !== -1) {
            canvas.style.webkitFilter = canvas.style.webkitFilter.replace('blur(3px)','');    
        } else {
            canvas.style.webkitFilter += ' blur(3px) ';
        }
    }
    
    fadeButton.onclick = function(){
        if(canvas.style.webkitFilter.indexOf('opacity') !== -1) {
            canvas.style.webkitFilter = canvas.style.webkitFilter.replace('opacity(30%)','');    
        } else {
            canvas.style.webkitFilter += "opacity(30%)";
        }
    }

    const canvas = document.querySelector('#photoshop');

    captureButton.onclick = video.onclick = function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        var context = canvas.getContext('2d');
        context.drawImage(video, 0, 0);
    }

    saveButton.onclick = video.onclick = function() {
        async function supportsWebp() {
            if (!self.createImageBitmap) return false;

            const webpData = 'data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA=';
            const blob = await fetch(webpData).then(r => r.blob());

            return createImageBitmap(blob).then(() => true, () => false);
        }

        (async () => {
            let imgData = '';
            let pictureFormat = '.jpg';
            if(await supportsWebp()) {
                imgData = canvas.toDataURL('image/webp');
                pictureFormat = '.webp';
            } else {
                imgData = canvas.toDataURL('image/jpeg');
            }
            axios({
                method: 'post',
                url: '/storage/take-picture.php',
                data: {
                    pictureFormat: pictureFormat,
                    pictureInfo: imgData
                }
            })
            .then(function(response){
                console.log(response);
                // img.src = 'https://s3-us-west-1.amazonaws.com/juddfranklin-bucket1/' + response.data;
                img.src = '/images/' + response.data;
            })
            .catch(function(error) {
                console.warn(error);
            });
            

        })();

    };

    const trash = document.querySelector('#trash')

    trash.addEventListener("dragover", dragover);
    trash.addEventListener("dragenter", dragenter);
    trash.addEventListener("drop", drop);

    function dragover(e) {
        e.preventDefault()
    }

    function dragenter(e) {
        e.preventDefault()
    }

    function drop() {
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    }
    trash.onclick = drop;

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
