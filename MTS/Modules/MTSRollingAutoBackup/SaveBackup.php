<?php

    if ($clientRequest->doBackup ) {
        // create new backup
        createBackup($clientRequest->sourceFile, $sessionManager->user, $sessionManager->user."_auto.html");

        // create copies of backup
        createMultipleBackups($clientRequest->sourceFile, $sessionManager->user);
    }

    function createBackup($source,$backupSubDirName="noname", $backupName="noname.html") {
        global $clientRequest, $serverInfo, $serverResponse;

        $sourceName = $clientRequest->sourceName;
        $backupDir = $serverInfo->BackupDirectory;

        $mySourceBackupDir = $backupDir.$sourceName ."/";
        $myBackupDir = $mySourceBackupDir . $backupSubDirName . "/";
        $backupPath = $myBackupDir.$backupName;

        if (is_dir($backupDir) === FALSE) {
            if( mkdir($backupDir, 0755) === false )
                $serverResponse->throwCriticalError("Could not create directory ($backupDir)");
        }

        if (is_dir($mySourceBackupDir) === FALSE) {
            if( mkdir($mySourceBackupDir, 0755) === false )
                $serverResponse->throwCriticalError("Could not create directory ($mySourceBackupDir)");
        }

        if (is_dir($myBackupDir) === FALSE) {
            if( mkdir($myBackupDir, 0755) === false )
                $serverResponse->throwCriticalError("Could not create directory ($myBackupDir)");
        }

        if ( copy($source, $backupPath))
            $serverResponse->setString("backup",$backupName);
        else
            $serverResponse->throwCriticalError("Copy failed on backup : ($myBackupDir) ($source) ($backupName)");

    }

    function createMultipleBackups($source, $backupUserName="noname") {
        global $clientRequest, $serverInfo, $serverResponse;

        $sourceName = $clientRequest->sourceName;
        $backupDir = $serverInfo->BackupDirectory;

        $myBackupDir = $backupDir.$sourceName . "/" . $backupUserName . "/";

        // subfolder => date() - string to save to
        $subfolders = array(
            'minutely' => 'i',
            'hourly' =>   'H',
            'daily'     => 'd_D',
            'weekly'    => 'W',
            'monthly'   => 'm_M',
            'yearly'    => 'Y'
        );

        foreach (array_keys( $subfolders ) as $subfolder) {
            if (!is_dir($myBackupDir . $subfolder)) {
                   if( mkdir($myBackupDir . $subfolder, 0755) === false ) {
                       $serverResponse->throwCriticalError("Could not create directory ($myBackupDir . $subfolder)");
                   }
            }
        }

        foreach ($subfolders as $subfolder => $dateString) {
            $backupPathAndFilename = $myBackupDir . $subfolder . "/" . date($dateString) . '.html';
            if ( copy($source, $backupPathAndFilename) ) {
                $serverResponse->setString("backup",$backupUserName);
            } else {
                $serverResponse->throwCriticalError("Copy failed on backup : ($backupPathAndFilename)");
            }
        }
    }
?>