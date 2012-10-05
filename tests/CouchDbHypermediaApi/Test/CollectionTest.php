<?php

namespace CouchDbHypermediaApi\Test;

use PHPUnit_Framework_TestCase as TestCase;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\View\FolderDesignDocument;
use Guzzle\Service\Client;

class CollectionTest extends TestCase
{
    const ROOT_URI = 'http://localhost:5984/{dbname}/_design/app/_rewrite/';

    const INITIAL_URI = '/';

    /**
     * @var string
     */
    private $testDbName;

    /**
     * @var Doctrine\CouchDB\CouchDBClient
     */
    private $couchDbClient;

    public function setUp()
    {
        $this->testDbName = uniqid('test_');
        $this->couchDbClient = CouchDBClient::create(array(
            'dbname'    => $this->testDbName,
        ));
        $this->couchDbClient->createDatabase($this->testDbName);
        $dir = new \DirectoryIterator(APPLICATION_ROOT . '/_design');
        foreach ($dir as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $designDoc = new FolderDesignDocument($file->getPathname());
                $this->couchDbClient->createDesignDocument($file->getFilename(), $designDoc);
            }
        }
    }

    public function tearDown()
    {
        $this->couchDbClient->deleteDatabase($this->testDbName);
    }

    public function testGetInitialUri()
    {
        $client = new Client(self::ROOT_URI, array(
        	'dbname' => $this->testDbName,
        ));
        $request = $client->get(ltrim(self::INITIAL_URI, '/'), array(
            'Accept'    => 'application/hal+json',
        ));
        $response = $request->send();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/hal+json', (string) $response->getHeader('Content-Type'));
        $this->assertEquals('Accept', (string) $response->getHeader('Vary'));
        $resource = json_decode((string) $response->getBody());
        $this->assertEquals(self::INITIAL_URI, $resource->_links->self->href);
        $this->assertCount(0, $resource->_links->item);
        $this->assertEquals('Collections', $resource->title);
    }

    public function testPostToInitialUri()
    {
        $client = new Client(self::ROOT_URI, array(
        	'dbname' => $this->testDbName,
        ));
        $postedResource = new \stdClass();
        $postedResource->title = 'Sessions';
        $request = $client->post(ltrim(self::INITIAL_URI, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedResource));
        $response = $request->send();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotNull((string) $response->getHeader('Location'));
        $this->assertEquals('application/hal+json', (string) $response->getHeader('Content-Type'));
        $this->assertEquals('Accept', (string) $response->getHeader('Vary'));
        $resource = json_decode((string) $response->getBody());
        $this->assertNotNull($resource->_links->self->href);
        $this->assertEquals(self::INITIAL_URI, $resource->_links->up->href);
        $this->assertEquals($postedResource->title, $resource->title);
    }
}
