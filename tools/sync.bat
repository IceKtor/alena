@echo off
setlocal EnableExtensions DisableDelayedExpansion
set "SYNC_ROOT=C:\OSPanel\home\alena.local"
set "SYNC_META=%LOCALAPPDATA%\AlenaSync\alena.local.git"
if not "%~1"=="" set "SYNC_ROOT=%~f1"
if not "%~1"=="" set "SYNC_META=%~f1.sync.git"
set "SYNC_URL=https://github.com/IceKtor/alena.git"
set "SYNC_BASE=234ad8231174b2959a8c574b1737a1de8982d454"
set "SYNC_REF=refs/remotes/origin/development"
set "SYNC_GIT=git.exe"
where git.exe >nul 2>&1
if errorlevel 1 set "SYNC_GIT=%ProgramFiles%\Git\cmd\git.exe"
"%SYNC_GIT%" --version >nul 2>&1
if errorlevel 1 (
    echo Git for Windows is required.
    goto :fail
)

pushd "%SYNC_ROOT%" 2>nul
if errorlevel 1 (
    echo Project directory is unavailable: "%SYNC_ROOT%"
    goto :fail
)
set "SYNC_IN_ROOT=1"
if not exist "index.php" (
    echo index.php was not found. Check the project directory.
    goto :fail
)

set "GIT_DIR="
set "GIT_WORK_TREE="
if exist ".git" goto :check_repository
set "GIT_DIR=%SYNC_META%"
set "GIT_WORK_TREE=%SYNC_ROOT%"
if exist "%SYNC_META%\HEAD" goto :check_repository
"%SYNC_GIT%" init --quiet --initial-branch=development
if errorlevel 1 goto :fail
"%SYNC_GIT%" config --local alena-sync.managed true
if errorlevel 1 goto :fail
"%SYNC_GIT%" config --local core.autocrlf true
if errorlevel 1 goto :fail
"%SYNC_GIT%" remote add origin "%SYNC_URL%"
if errorlevel 1 goto :fail

:check_repository
set "SYNC_ORIGIN="
for /f "delims=" %%a in ('call "%SYNC_GIT%" config --local --get remote.origin.url') do set "SYNC_ORIGIN=%%a"
if /i not "%SYNC_ORIGIN%"=="%SYNC_URL%" if /i not "%SYNC_ORIGIN%"=="https://github.com/IceKtor/alena" if /i not "%SYNC_ORIGIN%"=="git@github.com:IceKtor/alena.git" (
    echo The origin remote does not point to IceKtor/alena.
    goto :fail
)
set "SYNC_BRANCH="
for /f "delims=" %%a in ('call "%SYNC_GIT%" symbolic-ref --quiet HEAD') do set "SYNC_BRANCH=%%a"
if not "%SYNC_BRANCH%"=="refs/heads/development" (
    echo Select the development branch before syncing.
    goto :fail
)

echo Fetching development from GitHub...
"%SYNC_GIT%" fetch --no-tags origin "refs/heads/development:%SYNC_REF%"
if errorlevel 1 goto :fail
"%SYNC_GIT%" rev-parse --verify --quiet HEAD >nul
if not errorlevel 1 goto :update

rem The current OSPanel copy matches this commit. Set the index without writing site files.
if not defined GIT_DIR goto :fail
set "SYNC_MANAGED="
for /f "delims=" %%a in ('call "%SYNC_GIT%" config --local --bool --get alena-sync.managed') do set "SYNC_MANAGED=%%a"
if not "%SYNC_MANAGED%"=="true" goto :fail
"%SYNC_GIT%" merge-base --is-ancestor "%SYNC_BASE%" "%SYNC_REF%"
if errorlevel 1 (
    echo The expected starting commit is absent from development.
    goto :fail
)
"%SYNC_GIT%" reset --mixed --quiet "%SYNC_BASE%"
if errorlevel 1 goto :fail

:update
"%SYNC_GIT%" merge-base --is-ancestor HEAD "%SYNC_REF%"
if errorlevel 1 (
    echo The local branch has commits missing from origin/development.
    goto :fail
)
"%SYNC_GIT%" merge --ff-only --no-overwrite-ignore "%SYNC_REF%"
if errorlevel 1 goto :fail
echo.
echo Updated to:
"%SYNC_GIT%" log -1 --format=oneline --abbrev-commit
popd
if not "%ALENA_SYNC_NO_PAUSE%"=="1" pause
exit /b 0

:fail
if defined SYNC_IN_ROOT popd
echo.
echo Sync stopped. See the Git message above.
if not "%ALENA_SYNC_NO_PAUSE%"=="1" pause
exit /b 1
