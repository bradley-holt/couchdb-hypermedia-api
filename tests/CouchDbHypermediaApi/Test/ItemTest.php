<?php

namespace CouchDbHypermediaApi\Test;

use PHPUnit_Framework_TestCase as TestCase;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\CouchDB\View\FolderDesignDocument;
use Guzzle\Service\Client;

class ItemTest extends TestCase
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

    /**
     * @depends CouchDbHypermediaApi\Test\CollectionTest::testPostToInitialUri
     */
    public function testPostToCollectionUri()
    {
        $client = new Client(self::ROOT_URI, array(
        	'dbname' => $this->testDbName,
        ));
        $postedCollectionResource = new \stdClass();
        $postedCollectionResource->title = 'Sessions';
        $request = $client->post(ltrim(self::INITIAL_URI, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedCollectionResource));
        $response = $request->send();
        $collectionResource = json_decode((string) $response->getBody());
        $postedItemResource = new \stdClass();
        $postedItemResource->title = 'Building a Hypermedia API in CouchDB';
        $request = $client->post(ltrim($collectionResource->_links->self->href, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedItemResource));
        $response = $request->send();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotNull((string) $response->getHeader('Location'));
        $this->assertEquals('application/hal+json', (string) $response->getHeader('Content-Type'));
        $this->assertEquals('Accept', (string) $response->getHeader('Vary'));
        $itemResource = json_decode((string) $response->getBody());
        $this->assertNotNull($itemResource->_links->self->href);
        $this->assertEquals($collectionResource->_links->self->href, $itemResource->_links->collection->href);
        $this->assertEquals($postedItemResource->title, $itemResource->title);
    }

    /**
     * @depends testPostToCollectionUri
     */
    public function testGetItemEditUri()
    {
        $client = new Client(self::ROOT_URI, array(
        	'dbname' => $this->testDbName,
        ));
        $postedCollectionResource = new \stdClass();
        $postedCollectionResource->title = 'Sessions';
        $request = $client->post(ltrim(self::INITIAL_URI, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedCollectionResource));
        $response = $request->send();
        $collectionResource = json_decode((string) $response->getBody());
        $postedItemResource = new \stdClass();
        $postedItemResource->title = 'Building a Hypermedia API in CouchDB';
        $request = $client->post(ltrim($collectionResource->_links->self->href, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedItemResource));
        $response = $request->send();
        $itemResource = json_decode((string) $response->getBody());
        $request = $client->get(ltrim($itemResource->_links->edit->href, '/'), array(
            'Accept'        => 'application/hal+json',
        ));
        $response = $request->send();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/hal+json', (string) $response->getHeader('Content-Type'));
        $this->assertEquals('Accept', (string) $response->getHeader('Vary'));
        $resource = json_decode((string) $response->getBody());
        $this->assertEquals($itemResource->_links->edit->href, $resource->_links->edit->href);
        $this->assertEquals($collectionResource->_links->self->href, $resource->_links->collection->href);
        $this->assertEquals($postedItemResource->title, $resource->title);
    }

    /**
     * @depends testPostToCollectionUri
     */
    public function testPutToItemEditUri()
    {
        $client = new Client(self::ROOT_URI, array(
        	'dbname' => $this->testDbName,
        ));
        $postedCollectionResource = new \stdClass();
        $postedCollectionResource->title = 'Sessions';
        $request = $client->post(ltrim(self::INITIAL_URI, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedCollectionResource));
        $response = $request->send();
        $collectionResource = json_decode((string) $response->getBody());
        $postedItemResource = new \stdClass();
        $postedItemResource->title = 'Building a Hypermedia API in CouchDB';
        $request = $client->post(ltrim($collectionResource->_links->self->href, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedItemResource));
        $response = $request->send();
        $itemResource = json_decode((string) $response->getBody());
        $itemResource->description = <<<'EOD'
Unlike relational databases, document databases like CouchDB and Couchbase do not directly support entity 
relationships. This talk will explore patterns of modeling one-to-many and many-to-many entity relationships in a 
document database. These patterns include using an embedded JSON array, relating documents using identifiers, using a 
list of keys, and using relationship documents.
EOD;
        $request = $client->put(ltrim($itemResource->_links->edit->href, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($itemResource));
        $response = $request->send();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/hal+json', (string) $response->getHeader('Content-Type'));
        $this->assertEquals('Accept', (string) $response->getHeader('Vary'));
        $resource = json_decode((string) $response->getBody());
        $this->assertEquals($itemResource->_links->edit->href, $resource->_links->edit->href);
        $this->assertEquals($collectionResource->_links->self->href, $resource->_links->collection->href);
        $this->assertEquals($itemResource->title, $resource->title);
        $this->assertEquals($itemResource->description, $resource->description);
    }

    /**
     * @depends testPostToCollectionUri
     */
    public function testDeleteItemEditUri()
    {
        $client = new Client(self::ROOT_URI, array(
        	'dbname' => $this->testDbName,
        ));
        $postedCollectionResource = new \stdClass();
        $postedCollectionResource->title = 'Sessions';
        $request = $client->post(ltrim(self::INITIAL_URI, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedCollectionResource));
        $response = $request->send();
        $collectionResource = json_decode((string) $response->getBody());
        $postedItemResource = new \stdClass();
        $postedItemResource->title = 'Building a Hypermedia API in CouchDB';
        $request = $client->post(ltrim($collectionResource->_links->self->href, '/'), array(
            'Content-Type'  => 'application/hal+json',
            'Accept'        => 'application/hal+json',
        ), json_encode($postedItemResource));
        $response = $request->send();
        $deleteResource = json_decode((string) $response->getBody());
        $request = $client->delete(ltrim($deleteResource->_links->edit->href, '/'), array(
            'Accept'        => 'application/hal+json',
        ));
        $deleteResponse = $request->send();
        $this->assertEquals(201, $deleteResponse->getStatusCode());
        $this->assertNull($deleteResponse->getHeader('Location'));
        $this->assertEquals('application/hal+json', (string) $deleteResponse->getHeader('Content-Type'));
        $this->assertEquals('Accept', (string) $deleteResponse->getHeader('Vary'));
        $resource = json_decode((string) $deleteResponse->getBody());
        $this->assertTrue($resource->ok);
        $request = $client->get(ltrim($deleteResource->_links->edit->href, '/'), array(
            'Accept'        => 'application/hal+json',
        ));
        $this->setExpectedException('\Guzzle\Http\Exception\ClientErrorResponseException');
        $request->send();
    }
}
