@echo off
REM Double-click this file to refresh the "similar items" table.
REM XAMPP MySQL must be running (green in the XAMPP Control Panel).
cd /d "%~dp0"
python recommend.py
echo.
pause