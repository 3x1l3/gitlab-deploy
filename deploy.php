<?php

$message = file_get_contents('php://input');
$date = new DateTime();
//file_put_contents('gitlab deployment '.$date->getTimestamp().'.json', $message );

$message = '{"object_kind":"push","event_name":"push","before":"34e5dc70b8f57cae878c97b50aeb513f324ac6d2","after":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","ref":"refs/heads/master","checkout_sha":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","message":null,"user_id":2,"user_name":"Chad","user_email":"chad@tangle.ca","user_avatar":"http://www.gravatar.com/avatar/6826fa0e424c9598c54a61c2f604cf53?s=80\u0026d=identicon","project_id":3,"project":{"name":"test","description":"","web_url":"http://192.168.0.140/exile/test","avatar_url":null,"git_ssh_url":"git@192.168.0.140:exile/test.git","git_http_url":"http://192.168.0.140/exile/test.git","namespace":"exile","visibility_level":0,"path_with_namespace":"exile/test","default_branch":"master","homepage":"http://192.168.0.140/exile/test","url":"git@192.168.0.140:exile/test.git","ssh_url":"git@192.168.0.140:exile/test.git","http_url":"http://192.168.0.140/exile/test.git"},"commits":[{"id":"da35c62e4be3406926366cae66ce7cda8d5f008c","message":"Commit 1 no new files\n","timestamp":"2016-05-09T16:28:22-06:00","url":"http://192.168.0.140/exile/test/commit/da35c62e4be3406926366cae66ce7cda8d5f008c","author":{"name":"Chad","email":"chadklassen1@gmail.com"},"added":[],"modified":["index.php"],"removed":[]},{"id":"e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","message":"Added new file in subfolder\n","timestamp":"2016-05-09T16:28:59-06:00","url":"http://192.168.0.140/exile/test/commit/e4dea47c1f4b756e8f0a6e21d285dd471992b2ff","author":{"name":"Chad","email":"chadklassen1@gmail.com"},"added":["lib/libfile.php"],"modified":[],"removed":[]}],"total_commits_count":2,"repository":{"name":"test","url":"git@192.168.0.140:exile/test.git","description":"","homepage":"http://192.168.0.140/exile/test","git_http_url":"http://192.168.0.140/exile/test.git","git_ssh_url":"git@192.168.0.140:exile/test.git","visibility_level":0}}';
$hookObj = json_decode($message);

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

$configObj = json_decode($config);
$projectObj = $hookObj->project;
/*
 * First I need to find a valid git repository from a JSON array lets say...
 */

//::Set deployment key to null for the check later on.
$deploymentKey = null;
/*
  Find a deployment configuration that matches the commit that just game through.
 */
foreach ($configObj->deployments as $key => $current) {
    if ($hookObj->repository->url == $current->url || $hookObj->repository->git_http_url == $current->url || $hookObj->repository->git_ssh_url == $current->url) {
        $deploymentKey = $key;
        break;
    }
}

//::Lets do some stuff if there is a deployment that matches a git URL
if ($deploymentKey !== null) {
    echo 'Deployment configuration found';

    $deploymentConfig = $configObj->deployments[$deploymentKey];
    var_dump($hookObj);

    if (count($hookObj->commits) > 0) {
        foreach ($hookObj->commits as $commit) {
            var_dump($commit);
      //::Added files and modified files. Seems like the same thing.
      $copy = array_merge($commit->added, $commit->modified);

            if (count($copy) > 0) {
                foreach ($copy as $file) {
                    $contents = file_get_contents($projectObj->web_url.'/'.$commit->id.'/'.$file);
                    $tmpfile = fopen('php://memory', 'r+');
                    fputs($tmpfile, $contents);
                    rewind($tmpfile);
                    $result = ftp_fput($ftpResource, $deploymentConfig->path.$file, $tmpfile, FTP_BINARY);

                    if ($result) {
                        echo 'transfered file '.$file;
                    }
                }
            }

      //::Remove files.
        }
    } else {
        echo 'There were no commits...';
    }
} else {
    echo 'Deployment configuration not found';
}

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
        //::Set deployment key to null for the check later on.
    $deploymentKey = null;
    /*
      Find a deployment configuration that matches the commit that just game through.
     */
    foreach ($this->_configObj->deployments as $key => $current) {
        if ($this->_dataObj->repository->url == $current->url || $this->_dataObj->repository->git_http_url == $current->url || $this->_dataObj->repository->git_ssh_url == $current->url) {
            $this->_selectedConfig = $_dataObj[$key];

            return true;
        }
    }

        return false;
    }

    private function _transfer()
    {
    }

    private function _setupConnection() {

      $ftpResource = ftp_connect($this->_selectedConfig->host);
      ftp_login($ftpResource, $this->_selectedConfig->user, $this->_selectedConfig->password);

    }

    public function run()
    {
      if (  $this->_findDeployment() ) {

            if (count($this->_dataObj->commits) > 0) {
                foreach ($this->_dataObj->commits as $commit) {
                    var_dump($commit);

                    //::Added files and modified files. Seems like the same thing.
                    $copy = array_merge($commit->added, $commit->modified);

                    if (count($copy) > 0) {
                        foreach ($copy as $file) {
                            $contents = file_get_contents($projectObj->web_url.'/'.$commit->id.'/'.$file);
                            $tmpfile = fopen('php://memory', 'r+');
                            fputs($tmpfile, $contents);
                            rewind($tmpfile);
                            $result = ftp_fput($ftpResource, $deploymentConfig->path.$file, $tmpfile, FTP_BINARY);

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
