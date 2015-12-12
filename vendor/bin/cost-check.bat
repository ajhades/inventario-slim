@ECHO OFF
SET BIN_TARGET=%~dp0/../jeremykendall/password-validator/bin/cost-check
php "%BIN_TARGET%" %*
