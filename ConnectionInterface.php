<?php

interface ConnectionInterface {

    public function initialize();
    public function put($remotePath, $localPath);
}
