#!/bin/bash
set -e

if ! command -v lftp &> /dev/null
then
    echo "lftp not found, installing..."
    sudo apt-get update
    sudo apt-get install -y lftp
fi

echo "Deploying files..."

if [ -f "$LOCAL_DIR" ]; then
    echo "Uploading single file..."
    lftp -c "
    set ssl:verify-certificate no
    open -u \"$FTP_USER,$FTP_PASS\" \"$FTP_HOST\"
    put \"$LOCAL_DIR\" -o \"$REMOTE_DIR\"
    bye
    "
else
    echo "Uploading directory..."
    lftp -c "
    set ftp:list-options -a
    set ssl:verify-certificate no
    open -u \"$FTP_USER,$FTP_PASS\" \"$FTP_HOST\"
    mirror -R \"$LOCAL_DIR\" \"$REMOTE_DIR\" --only-newer --parallel=5 --verbose
    bye
    "
fi

echo "Deployment completed!"
