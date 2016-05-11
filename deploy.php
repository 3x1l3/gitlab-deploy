<?php


/*
$current = $configObj->deployments[0];
$ftpResource = ftp_connect($current->host);
ftp_login($ftpResource, $current->user, $current->password);
*/

class Deploy
{
    private $_dataObj;
    private $_configObj;

    //::If found stores the selected config
    private $_selectedConfig = null;

    private $_connection;

    public function __construct(ConnectionInterface $connection = null)
    {
        $this->_connection = $connection;
    }

    public function getConfiguration()
    {
        $config = '
      {
        "deployments": [
            {
              "url": "http://192.168.0.140/exile/test.git",
              "type": "FTP",
              "user": "",
              "password": "",
              "host": "tanglemedia.net",
              "path": "/public_html/"
            }
        ]
      }
    ';

        $this->_configObj = json_decode($config);
    }

    public function getDeploymentData()
    {
        $this->_dataObj = json_decode(file_get_contents('php://input'));
    }
    public function setDeploymentData(stdClass $data)
    {
        $this->_dataObj = $data;
    }

    private function _findDeployment()
    {

    /*
      Find a deployment configuration that matches the commit that just game through.
     */
    foreach ($this->_configObj->deployments as $key => $current) {
        if ($this->_dataObj->repository->url == $current->url || $this->_dataObj->repository->git_http_url == $current->url || $this->_dataObj->repository->git_ssh_url == $current->url) {
            $this->_selectedConfig = json_decode($_dataObj[$key]);

            return true;
        }
    }

        return false;
    }

    private function _transfer()
    {
    }

    private function _setupConnection()
    {
        switch ($this->_selectedConfig->type) {
        case 'FTP':
          $this->_connection = new FTPConnection($this->_selectedConfig->host, $this->_selectedConfig->user, $this->_selectedConfig->password);
        break;
      }
        $this->_connection->initialize();
    }

/**
 * [_getFile description]
 * @return mixed Filepath to file or resource to file.
 */
    private function _getFile($file)
    {
        $contents = file_get_contents($projectObj->web_url.'/'.$commit->id.'/'.$file);
        $tmpfile = fopen('php://memory', 'r+');
        fputs($tmpfile, $contents);
        rewind($tmpfile);
        return $tmpfile;
    }

    public function run()
    {
        if ($this->_findDeployment()) {
            if (count($this->_dataObj->commits) > 0) {
                $this->_setupConnection();

                foreach ($this->_dataObj->commits as $commit) {
                    var_dump($commit);

                    //::Added files and modified files. Seems like the same thing.
                    $copy = array_merge($commit->added, $commit->modified);

                    if (count($copy) > 0) {
                        foreach ($copy as $file) {
                            $result = $this->_connection->put($this->_selectedConfig->path.$file, $this->_getFile());

                            if ($result) {
                                echo 'transfered file '.$file;
                            }
                        }
                    }

              //::Remove files.
                }
            }
        }
    }
}
