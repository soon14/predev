@echo off
if "%OS%"=="Windows_NT" @setlocal
set SCRIPT_DIR=%~dp0
set PHP_COMMAND=php.exe
if "%SCRIPT_DIR%" == "" (
  %PHP_COMMAND% "shell" %*
) else (
  %PHP_COMMAND% "%SCRIPT_DIR%\shell" %*
)
if "%OS%"=="Windows_NT" @endlocal
