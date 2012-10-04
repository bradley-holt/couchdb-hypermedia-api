<?php

namespace CouchDbHypermediaApi\AdminCommand;

use Cilex\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\View\FolderDesignDocument;

class MigrateCouchDb extends Command
{
    protected function configure()
    {
        $this->setName('couchdb:migrate');
        $this->setDescription('Migrate CouchDB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the CouchDB configuration settings
        $couchConfig = include APPLICATION_ROOT . '/config/couch.php';
        // Create a CouchDB client
        $couchDbClient = CouchDBClient::create($couchConfig);
        // Create the database, if it does not yet exist
        if (!in_array($couchConfig['dbname'], $couchDbClient->getAllDatabases())) {
            $output->writeln('Creating database "' . $couchConfig['dbname'] . '"...');
            $couchDbClient->createDatabase($couchConfig['dbname']);
        }
        // Iterate through the _design directory
        $dir = new \DirectoryIterator(APPLICATION_ROOT . '/_design');
        foreach ($dir as $file) {
            // Only consider directories that are not dots
            if ($file->isDir() && !$file->isDot()) {
                // Get the name of the design document
                $designDocName = $file->getFilename();
                // Create a folder design document
                $designDoc = new FolderDesignDocument($file->getPathname());
                // Get the design document to see if it needs to be updated or if it even exists
                $response = $couchDbClient->findDocument( '_design/' . $designDocName);
                switch ($response->status) {
                    case 200:
                        // Determine if the design document needs to be updated
                        $newDesignDocMd5 = md5(json_encode($designDoc->getData()));
                        $body = $response->body;
                        unset($body['_id']);
                        unset($body['_rev']);
                        $oldDesignDocMd5 = md5(json_encode($body));
                        if ($newDesignDocMd5 !== $oldDesignDocMd5) {
                            // Update the design document
                            $output->writeln('Updating "' . $designDocName . '" design document...');
                            $couchDbClient->createDesignDocument($designDocName, $designDoc);
                        }
                        break;
                    case 404:
                        // Create the design document
                        $output->writeln('Creating "' . $designDocName . '" design document...');
                        $couchDbClient->createDesignDocument($designDocName, $designDoc);
                        break;
                }
            }
        }
    }
}
