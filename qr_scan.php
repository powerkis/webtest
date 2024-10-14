<!DOCTYPE html>
<html lang="ko">
<head>
    
   
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR 코드 스캔</title>
    <script src="./js/jsQR.js"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        video {
            width: 100%;
            max-width: 400px;
            border: 1px solid black;
        }
        #result {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<h1>QR 코드 스캔하기</h1>
    <video id="video" autoplay></video>
    <div id="result"></div>   
   
	<script>      

        var video = document.getElementById('video');
        var resultDiv = document.getElementById('result');

        // 비디오 스트림 설정
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function(stream) {
                video.srcObject = stream;
                video.setAttribute("playsinline", true); // iOS에서 전체 화면 방지
                video.play();
                scanQRCode();
            });
//            .catch(function(err) {
//                console.log("카메라 접근 오류: ", err);
//            });

        function scanQRCode() {
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');

            setInterval(function() {
                // 비디오에서 프레임 캡처
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                var code = jsQR(imageData.data, canvas.width, canvas.height);

                if (code) {
                    resultDiv.innerText = 'QR 코드 내용: ${code.data}';
                }
            }, 1000); // 1초마다 스캔
        }
    </script>
</body>
</html>
