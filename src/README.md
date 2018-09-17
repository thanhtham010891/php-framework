# PHP Framework

Create the basic framework for my work!

##### I. Routes
Supported:
- fixed string
- regular expression

Do not supported:
- Group routes


    namespace App\Providers\Route;
    
    use App\Controllers\Api\V1;
    use App\Controllers\IndexController;
    use App\Core\Contract\RouteInterface;
    
    class Route implements RouteInterface
    {
    
        public function getResource()
        {
            return [
                '/' => [
                    'controller' => IndexController::class, 'method' => 'index', 'args' => []
                ],
                '/post/([0-9]+)' => ['controller' => IndexController::class, 'method'],
                '/api/v1' => [
                    'controller' => V1::class, 'method' => 'index'
                ]
            ];
        }
    }

##### II. Controllers

Extends from ApiView

Supported: 
- Json response


    namespace App\Controllers\Api;
    
    use App\Providers\View\Type\Api;
    
    class V1 extends Api
    {
    
        public function index()
        {
            return $this->success([
                'name' => 'thamtt',
                'email' => 'thamtt@nal.vn'
            ]);
        }
    }

Extends from WebView

Supported: 
- Twig


    namespace App\Controllers;
    
    use App\Providers\View\Type\Web;
    
    class IndexController extends Web
    {
        public function index()
        {
            return [
                'render' => 'index.html',
                'data' => ['name' => 'thamtt', 'email' => 'thamtt@gmail.com']
            ];
        }
    }
    
    
