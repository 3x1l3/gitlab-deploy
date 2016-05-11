<?php

class FTPConnection implements ConnectionInterface
{
    private $_resource;
    private $_host;
    private $_password;
    private $_user;

    public function __construct($host, $user, $password)
    {
      $this->_host = $host;
      $this->_password = $password;
      $this->_user = $user;
    }

    public function initialize()
    {
        $this->_resource = ftp_connect($this->_host);
        return ftp_login($this->_resource, $this->_user, $this->_password);
    }

    public function put($remotePath, $localPath) {
      return ftp_fput($this->_resource, $remotePath, $localPath, FTP_BINARY);
    }
}
