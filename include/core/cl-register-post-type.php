<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Cl_Register_Post_Type{

	protected $args = array();

	public function __construct( $args ){
		$this->args = $args;

		add_action( 'init', array( $this, 'register_type' ) );
		
		add_filter("manage_edit-".$args['post_type']."_columns", array( $this, 'prod_edit_columns' ));
		add_action("manage_posts_custom_column",  array( $this, 'prod_custom_columns' ));
	}

	public function register_type( ){

		$this->register_post_type( );
		$this->register_taxonomy( );

	}

	public function register_post_type(){

		if( ! empty( $this->args['post_type'] ) && isset($this->args['labels']) && isset($this->args['slugRule']) ){

			$newArgs = array(

				'labels' => $this->args['labels'],

				'public' => true,

				'show_ui' => true,

				'capability_type' => 'post',

				'hierarchical' => false,

				'rewrite' => array('slug'=>$this->args['slugRule'],'with_front'=>true),

				'query_var' => true,

				'show_in_nav_menus'=> false,

				'supports' => array('title','thumbnail','excerpt','editor'),

				'show_in_customizer' => $this->args['show_in_customizer']

			);

			register_post_type( $this->args['post_type'] , $newArgs );
		}

	}


	public function register_taxonomy( ){
		
		register_taxonomy( $this->args['taxonomy'] , 
			
			array($this->args['post_type']), 

			array(	"hierarchical" => true, 

			"label" => $this->args['taxonomy_label'], 

			"singular_label" => $this->args['taxonomy_label'], 

			"rewrite" => true,

			"query_var" => true

		)); 

	}


	public function prod_edit_columns($columns){
		
		$newcolumns = array(
			"cb" => "<input type=\"checkbox\" />",

			"title" => "Title",

			$this->args['taxonomy'] => "Categories"

		);

		
		$columns= array_merge($newcolumns, $columns);

		

		return $columns;

	}

	public function prod_custom_columns($column)

	{

		global $post;

		switch ($column)

		{

			case "description":

			break;

			case "price":
			
			break;

			case $this->args['taxonomy']:

			echo get_the_term_list($post->ID, $this->args['taxonomy'], '', ', ','');

			break;

		}

	}

}