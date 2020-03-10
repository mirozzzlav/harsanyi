<?php

namespace CustomRestApi;
require 'PostParser.php';
require 'Validators.php';
require 'ApplicationCreator.php';
require_once \WP_PLUGIN_DIR . '/constants.php';

class RestController extends \WP_REST_Controller {
 
    /**
     * Register the routes for the objects of the controller.
     */
    const VERSION = 2;
    const ROUTE_PREFIX = "custom-api";
    const ROUTES = [
      [
        "methods" => \WP_REST_Server::CREATABLE,
        "callback" => "createApplication",
        "url" => "/create-application"
      ],
      [
        "methods" => \WP_REST_Server::READABLE,
        "callback" => "getPosts",
        "url" => "/get-posts/(?P<post_type>[\da-zA-Z\_\-]+)(?:/(?P<page_nr>\d+)/(?P<posts_per_page>\d+))?",
      ],
      [
        "methods" => \WP_REST_Server::READABLE,
        "callback" => "getSupportValues",
        "url" => "/get-support-values",
      ]
    ];

    public function register_routes() {
      $namespace = self::ROUTE_PREFIX . "/v" . self::VERSION;
      
      foreach(self::ROUTES as $route) {
        register_rest_route( $namespace, $route['url'], 
          [
            [
                'methods'             => $route['methods'],
                'callback'            => [$this, $route['callback']],
                //'permission_callback' => array( $this, 'create_item_permissions_check' ),
                //'args'                =>  !empty($route['args']) ? $route['args'] : [],
            ],
          ]
        );
      }
    }
   
    
    /**
     * Sends a message from contact form to our email
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function sendContactMessage( $request ) {
   
      //return new WP_Error( 'cant-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
      return new \WP_REST_Response( ['som' => 'send'], 200 );
    }
    /**
     * Get posts
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function getPosts($request)
    {  
      $argsForGetPosts = ['orderby' => 'date', 'order' => 'DESC'];
      $getParams = $request->get_params();
      
      $argsForGetPosts['post_type'] = !empty($getParams['post_type']) ? 
        $getParams['post_type'] : WEHELPED_POSTTYPE;  
      
      $argsForGetPosts['posts_per_page'] = isset($getParams['posts_per_page']) ? 
        $getParams['posts_per_page'] : MAX_IMAGES_CAROUSEL;  
      $pageNr = isset($getParams['page_nr']) ? 
        $getParams['page_nr'] : 1;  
      $argsForGetPosts['offset'] = ($pageNr - 1) * $argsForGetPosts['posts_per_page'];

      $posts = get_posts($argsForGetPosts);
      $itemsCount = (wp_count_posts(WEHELPED_POSTTYPE))->publish;
      $response = ['items' => [], 'items_count' => $itemsCount];

      foreach ($posts as $post) {        
        $parser = new PostParser($post);
        $response['items'][] = [
          'id' => $parser->post->ID, 'post_title' => $parser->post->post_title, 
          'post_content' => $parser->getPostContentAsTxt(), 
          'imgs' => $parser->getPostImgs(), 'first_img' => $parser->getPostFirstImg()
        ];
      }
      return new \WP_REST_Response($response, 200);
      
    }

    private function errorResponse(String $errName, String $errMsg, Array $errMsgParams = [])
    {
      if (!empty($errMsgParams)) {
        $errMsg = sprintf($errMsg, $errMsgParams);
      }
      return new \WP_Error($errName, $errMsgParams,['status' => 500]);
    }

    public function createApplication($request)
    {
      $appCreator = new ApplicationCreator();

      $applicationData = array_merge(
        $request->get_params(), 
        $request->get_file_params()
      );
      
      
      $isValid = $appCreator->validate($applicationData);

      $invalidParamName =  !empty($appCreator->invalidParamName) ? " [$appCreator->invalidParamName]" : "";
      
      if (!$isValid) {
        return $this->errorResponse(
          "create-application-validation-failed", 
          "Vyskytla sa chyba na vstupch z formulára%s.",
          [$invalidParamName]
        );
      }
      
      if (!$appCreator->create($applicationData)) {
        return $this->errorResponse(
          "create-application-insert-failed", 
          "Vyskytla sa neočakávaná chyba pri ukladaní žiadosti.",
        );
      }

      return new \WP_REST_Response(['applicationCreated' => true], 200);
    }
    public function getSupportValues() {
      $resp = [
        "supported_projects" => get_option(SUPPORTED_PROJECTS_NAME) ?: 0,
        "supported_projects_value" => get_option(SUPPORTED_PROJECTS_VALUE_NAME) ?: 0
      ];
      return new \WP_REST_Response($resp, 200);
      
    }
   
  }