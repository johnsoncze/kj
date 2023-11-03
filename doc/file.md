Mazání souboru pomocí ajaxu
-----
- Nastavit tlačítku pro mazání třídu ".delete-file"
- Obalit soubor pomocí div.file-wrapper
- Zpracování požadavku na straně serveru pomocí ajaxu
- Vytvořit response pomocí objektu DeleteFileResponse
- Odeslat response pomocí {presenter}->sendJson(DeleteFileResponse::getResponseArray())