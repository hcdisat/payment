<?php namespace HcDisat\Payment\Tests;

use GuzzleHttp\Message\MessageFactory;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Subscriber\Mock as MockPlugin;
use ReflectionObject;
use Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\HttpFoundation\Request as HttpRequest;
use \GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Message\RequestInterface as GuzzleRequestInterface;

trait PaymentTestActions
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';
    protected $mockRequest;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var HttpRequest
     */
    private $httpRequest;


    /**
     * Mark a request as being mocked
     *
     * @param GuzzleRequestInterface $request
     *
     * @return self
     */
    public function addMockedHttpRequest(GuzzleRequestInterface $request)
    {
        $this->mockRequest[] = $request;

        return $this;
    }
    
    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient ?? $this->httpClient = new HttpClient();

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getHttpRequest()
    {
        return $this->httpRequest ?? $this->httpRequest = new HttpRequest();
    }


    /**
     * @return \HcDisat\Payment\Core\Requests\RestAuthorizeRequest
     */
    public function getMockRequest()
    {
        return $this->mockRequest ??
        \Mockery::mock(\HcDisat\Payment\Core\Requests\RestAuthorizeRequest::class);
    }

    /**
     * Get a mock response for a client by mock file name
     *
     * @param string $path Relative path to the mock response file
     *
     * @return ResponseInterface
     */
    public function getMockHttpResponse($path)
    {
        if ($path instanceof Response) {
            return $path;
        }

        $ref = new ReflectionObject($this);
        $dir = dirname($ref->getFileName());
        $file = $dir.'/Mock/'.$path;

        // if mock file doesn't exist, check parent directory
        if (!file_exists($dir.'/Mock/'.$path) && file_exists($dir.'/../Mock/'.$path)) {
            $file = $dir.'/../Mock/'.$path;
        }

        return (new MessageFactory())->fromMessage(file_get_contents($file));
    }

    /**
     * Set a mock response from a mock file on the next client request.
     *
     * This method assumes that mock response files are located under the
     * Mock/ subdirectory of the current class. A mock response is added to the next
     * request sent by the client.
     *
     * @param string $paths Path to files within the Mock folder of the service
     *
     * @return MockPlugin returns the created mock plugin
     */
    public function setMockHttpResponse($paths)
    {
        $this->mockRequest = [];
        $mock = new MockPlugin([], true);

        $this->getHttpClient()->getEmitter()->detach($mock);
        $this->getHttpClient()->getEmitter()->on('mock.request', function(Event $event) {
            $this->addMockedHttpRequest($event['request']);
        });

        foreach ((array) $paths as $path) {
            $mock->addResponse($this->getMockHttpResponse($path));
        }

        $this->getHttpClient()->getEmitter()->attach($mock);

        return $mock;
    }


    /**
     * Helper method used by gateway test classes to generate a valid test credit card
     */
    public function getValidCard()
    {
        return [
            'firstName' => 'Example',
            'lastName' => 'User',
            'number' => '4032031662872158',
            'expiryMonth' => rand(1, 12),
            'expiryYear' => gmdate('Y') + rand(1, 5),
            'cvv' => rand(100, 999),
            'billingAddress1' => '123 Billing St',
            'billingAddress2' => 'Billsville',
            'billingCity' => 'Billstown',
            'billingPostcode' => '12345',
            'billingState' => 'CA',
            'billingCountry' => 'US',
            'billingPhone' => '(555) 123-4567',
            'shippingAddress1' => '123 Shipping St',
            'shippingAddress2' => 'Shipsville',
            'shippingCity' => 'Shipstown',
            'shippingPostcode' => '54321',
            'shippingState' => 'NY',
            'shippingCountry' => 'US',
            'shippingPhone' => '(555) 987-6543',
        ];
    }
}