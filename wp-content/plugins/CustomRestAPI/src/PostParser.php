<?php

namespace CustomRestApi;
require_once ABSPATH.WPINC."/class-wp-post.php";

Class PostParser
{
    public \WP_Post $post; 
    private $postImgs = [];
    function __construct(\WP_Post $post)
    {
        $this->post = $post;
    }

    function getPostContentAsTxt(): String
    {
        return wp_strip_all_tags($this->post->post_content);   
    }

    function getPostImgs(): Array
    {   
        if (!empty($this->postImgs)) {
            return $this->postImgs;
        }
        $media = get_attached_media('image', $this->post);
        $content = str_replace(array("\r\n", "\r", "\n"), " ", $this->post->post_content);
        preg_match("/wp\:gallery {\"ids\":\[((?:\"?[0-9]+\"?,{0,1})+)\]/", $content, $matches);
    
        if (empty($matches[1])) {
            return [];
        }

        $mediaIds = explode(",", str_replace(["\"", "\'"],"",$matches[1]));
        $ret = [];
        foreach ($mediaIds as $mId) {
            $tmp = wp_get_attachment_image_src($mId, 'full');            
            $img = [];
            if (!empty($tmp)) {
                $img = ['src' => $tmp[0], 'width' => $tmp[1], 'height' => $tmp[2]];
                $ret[] = $img;
            }
        }
        
        $this->postImgs = $ret;
        return $ret;
    }

    function getPostFirstImg(): Array
    {
        $imgs = $this->getPostImgs();
        if (!empty($imgs)) {
            return $imgs[0];
        }
        return [];
    }

}