@echo off
setlocal EnableDelayedExpansion
title Ascend — Generate Submission ZIP & PDFs

echo =====================================================================
echo  EF234301 Web Programming (D) Midterm Submission Generator
echo =====================================================================
echo.

set /p S1_ID="Enter Student 1 ID/NRP (default 5025251024): "
if "!S1_ID!"=="" set S1_ID=5025251024

set /p S1_NAME="Enter Student 1 Name (default RinaltraNabasaS): "
if "!S1_NAME!"=="" set S1_NAME=RinaltraNabasaS

set /p S2_ID="Enter Student 2 ID/NRP (default 5025251019): "
if "!S2_ID!"=="" set S2_ID=5025251019

set /p S2_NAME="Enter Student 2 Name (default FakhrianElanta): "
if "!S2_NAME!"=="" set S2_NAME=FakhrianElanta

set /p S3_ID="Enter Student 3 ID/NRP (default 5025521121): "
if "!S3_ID!"=="" set S3_ID=5025521121

set /p S3_NAME="Enter Student 3 Name (default HaidarAbiyyuatTaqy): "
if "!S3_NAME!"=="" set S3_NAME=HaidarAbiyyuatTaqy

set ZIPNAME=EF234301_WebPro_MID_!S1_ID!_!S1_NAME!_!S2_ID!_!S2_NAME!_!S3_ID!_!S3_NAME!.ZIP

echo.
echo Compiling Report.PDF...
powershell -Command "Start-Process -FilePath 'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe' -ArgumentList '--headless', '--disable-gpu', '--no-sandbox', '--print-to-pdf=%~dp0Report.PDF', 'http://127.0.0.1:8000/report.html' -Wait"

echo Compiling Declaration.PDF...
powershell -Command "Start-Process -FilePath 'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe' -ArgumentList '--headless', '--disable-gpu', '--no-sandbox', '--print-to-pdf=%~dp0Declaration.PDF', 'http://127.0.0.1:8000/declaration.html' -Wait"

echo Packaging into !ZIPNAME!...
powershell -Command "Compress-Archive -Path '%~dp0Report.PDF', '%~dp0Declaration.PDF' -DestinationPath '%~dp0!ZIPNAME!' -Force"

echo.
echo =====================================================================
echo  DONE! Submission ready:
echo  File: !ZIPNAME!
echo  Contains: Report.PDF and Declaration.PDF
echo =====================================================================
pause
