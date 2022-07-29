@extends('layouts.app')

@section('content')
<div class="container">
@if(@Auth::user()->hasRole('cliente'))
    <h2>Eres un cliente</h2>
@endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>LECTOR QR</h1>
            <div id="loadingMessage">🎥 Incapaz de acceder a la cámara web (asegúrate de tener la cámara activada)</div>
            <canvas id="canvas" hidden></canvas>
            <div id="output" hidden>
                <div id="outputMessage">No QR code detected.</div>
                <form action="/form" method="post">
                    <div hidden><b>Data:</b> <span id="outputData"></span></div>
                    <button type="submit">Enviar</button>
                </form>
            </div>
            <script>
                var video = document.createElement("video");
                var canvasElement = document.getElementById("canvas");
                var canvas = canvasElement.getContext("2d");
                var loadingMessage = document.getElementById("loadingMessage");
                var outputContainer = document.getElementById("output");
                var outputMessage = document.getElementById("outputMessage");
                var outputData = document.getElementById("outputData");

                function drawLine(begin, end, color) {
                    canvas.beginPath();
                    canvas.moveTo(begin.x, begin.y);
                    canvas.lineTo(end.x, end.y);
                    canvas.lineWidth = 4;
                    canvas.strokeStyle = color;
                    canvas.stroke();
                }

                // Use facingMode: environment to attemt to get the front camera on phones
                navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment"
                    }
                }).then(function(stream) {
                    video.srcObject = stream;
                    video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                    video.play();
                    requestAnimationFrame(tick);
                });

                function tick() {
                    loadingMessage.innerText = "⌛ Loading video..."
                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                        loadingMessage.hidden = true;
                        canvasElement.hidden = false;
                        outputContainer.hidden = false;

                        canvasElement.height = video.videoHeight;
                        canvasElement.width = video.videoWidth;
                        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                        var code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "dontInvert",
                        });
                        if (code) {
                            drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                            drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                            drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                            drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                            outputMessage.hidden = true;
                            outputData.parentElement.hidden = false;
                            outputData.innerText = code.data;
                        }
                        // } else {
                        //   outputMessage.hidden = false;
                        //   outputData.parentElement.hidden = true;
                        // }
                    }
                    requestAnimationFrame(tick);
                }
            </script>
        </div>
    </div>
</div>
@endsection