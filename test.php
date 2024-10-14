<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NFC Reader</title>
</head>
<body>
    <h1>NFC 태그 읽기</h1>
    <button id="readNfcButton">NFC 태그 읽기</button>
    <p id="output"></p>

    <script>
        document.getElementById('readNfcButton').addEventListener('click', async () => {
            try {
                const nfc = new NDEFReader();
                await nfc.scan();

                nfc.onreading = event => {
                    const message = event.message;
                    let output = '';

                    for (const record of message.records) {
                        output += `Record type: ${record.recordType}\n`;
                        output += `MIME type: ${record.mediaType}\n`;
                        output += `Data: ${new TextDecoder().decode(record.data)}\n`;
                    }

                    document.getElementById('output').innerText = output;
                };
            } catch (error) {
				alert(error);
                console.error("NFC 읽기 실패:", error);
            }
        });
    </script>
</body>
</html>
